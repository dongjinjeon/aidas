<?php

namespace App\Http\Controllers\PaymentGateway\Walletium\v1;

use Exception;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Http\Helpers\Response;
use App\Models\Admin\Currency;
use Illuminate\Support\Carbon; 
use Illuminate\Support\Facades\DB;
use App\Models\PaymentOrderRequest;
use App\Http\Controllers\Controller;
use App\Constants\PaymentGatewayConst;
use App\Models\Merchant\SandboxWallet; 
use Illuminate\Support\Facades\Validator;
use App\Models\Admin\MerchantConfiguration;
use App\Notifications\PaymentGateway\PaymentVerification;

class PaymentController extends Controller
{
 
    protected $access_token_expire_time = 600;
    protected $test_email_verification_code = 123456;
    protected $testuser_email = "sandbox@appdevs.net";
    protected $testuser_username = "appdevs";

    public function paymentCreate(Request $request) {
        $access_token = $request->bearerToken(); 
        if(!$access_token) return Response::paymentApiError(['Access denied! Token not found'],[],403);

        $request_record = PaymentOrderRequest::where('access_token',$access_token)->first();
        if(!$request_record) return Response::paymentApiError(['Requested with invalid token!'],[],403);

        if(Carbon::now() > $request_record->created_at->addSeconds($this->access_token_expire_time)) {
            try{
                $request_record->update([
                    'status'    => PaymentGatewayConst::EXPIRED,
                ]);
            }catch(Exception $e) {
                return Response::paymentApiError(['Failed to create payment! Please try again'],[],500);
            }
        }

        if($request_record->status == PaymentGatewayConst::EXPIRED) return Response::paymentApiError(['Request token is expired'],[],401);

        if($request_record->status != PaymentGatewayConst::CREATED) return Response::paymentApiError(['Requested with invalid token!'],[],400);

        $validator = Validator::make($request->all(),[
            'amount'        => 'required|string|numeric|gt:0',
            'currency'      => 'required|string|exists:currencies,code',
            'return_url'    => 'required|string',
            'cancel_url'    => 'required|string',
        ]);

        if($validator->fails()) return Response::paymentApiError($validator->errors()->all(),[],400);
        $validated = $validator->validate();

        $merchant = $request_record->merchant;
        $developer_credentials = $merchant->developerApi;

        if(!$merchant || !$developer_credentials) return Response::paymentApiError(['Merchant does\'t exists']);

        // check request URL is sandbox or production
        if(request()->is("*/sandbox/*")) {
            // Requested with sandbox URL
            if($developer_credentials->mode != PaymentGatewayConst::ENV_SANDBOX) return Response::paymentApiError(['Requested with invalid credentials!']);
            $payment_url = route('walletium.pay.sandbox.v1.user.auth.form',$request_record->token);
        }else {
            if($developer_credentials->mode != PaymentGatewayConst::ENV_PRODUCTION) return Response::paymentApiError(['Requested with invalid credentials!']);
            $payment_url = route('walletium.pay.v1.user.auth.form',$request_record->token);
        }

        // Update and generate redirect links
        try{
            $request_record->update([
                'amount'        => $validated['amount'],
                'currency'      => $validated['currency'],
                'data'          => [
                    'return_url'    => $validated['return_url'],
                    'cancel_url'    => $validated['cancel_url'],
                ],
            ]);
        }catch(Exception $e) {
            return Response::paymentApiError(['Failed to create payment! Please try again'],[],500);
        }


        return Response::paymentApiSuccess([$request_record->status],[
            'token'         => $request_record->token,
            'payment_url'   => $payment_url,
        ],200);
        
    }

    public function paymentPreview($token) {
        $user_wallet = null;
        // common code Start
        $request_record = PaymentOrderRequest::where('token',$token)->first();
        if(!$request_record) {
            $page_title = "Process Error";
            $data = [
                'title'         => "Payment request is invalid!",
                'subtitle'      => "Something went wrong! Go back and try again.",
                'button_text'   => "Home",
                'link'          => url('/'),
                'logo'          => "",
            ];
            return view('walletium-gateway.pages.error',compact('data','page_title'));
        }

        $merchant_configuration = MerchantConfiguration::first();
        if(!$merchant_configuration) {
            $page_title = "Process Error";
            $data = [
                'title'         => "Payment gateway no longer available",
                'subtitle'      => "",
                'button_text'   => "Cancel and return/Home",
                'link'          => $request_record->data->cancel_url ?? url("/"),
                'logo'          => "",
            ];
            return view('walletium-gateway.pages.error',compact('data','page_title'));
        }
        $payment_gateway_image = get_image($merchant_configuration->image,'merchant-config');

        if($request_record->authentication != true) {
            $page_title = "Process Error";
            $data = [
                'title'         => "Authentication Failed!",
                'subtitle'      => "You are requested with unauthenticated user. Please make sure you are authenticated",
                'button_text'   => "Got to Login",
                'link'          => route('walletium.pay.sandbox.v1.user.auth.form',$token),
                'logo'          => $payment_gateway_image,
            ];
            return view('walletium-gateway.pages.error',compact('data','page_title'));
        }

        $merchant = $request_record->merchant;
        $developer_credentials = $merchant->developerApi;

        if(!$merchant || !$developer_credentials) {
            $page_title = "Process Error";
            $data = [
                'title'         => "Merchant doesn't exists or credentials is invalid!",
                'subtitle'      => "",
                'button_text'   => "Cancel and return/Home",
                'link'          => $request_record->data->cancel_url ?? url("/"),
                'logo'          => "",
            ];
            return view('walletium-gateway.pages.error',compact('data','page_title'));
        }

        if(Carbon::now() > $request_record->created_at->addSeconds($this->access_token_expire_time)) {
            $page_title = "Process Error";
            $data = [
                'title'         => "Session Expired!",
                'subtitle'      => "Your token session is expired. Go back and try again",
                'button_text'   => "Cancel and return",
                'link'          => $request_record->data->cancel_url ?? url("/"),
                'logo'          => $payment_gateway_image,
            ];
            return view('walletium-gateway.pages.error',compact('data','page_title'));
        }
        // common code End

        // Check request comes from sandbox or production url
        if(request()->is("*/sandbox/*")) {
            if($developer_credentials->mode != PaymentGatewayConst::ENV_SANDBOX) {
                $page_title = "Process Error";
                $data = [
                    'title'         => "Requested with invalid credentials!",
                    'subtitle'      => "",
                    'button_text'   => "Cancel and return/Home",
                    'link'          => $request_record->data->cancel_url ?? url("/"),
                    'logo'          => "",
                ];
                return view('walletium-gateway.pages.error',compact('data','page_title'));
            }
            // sandbox request
            $submit_form_url = route('walletium.pay.sandbox.v1.user.payment.preview.submit',$token);
            $user = (object) [
                'fullname'  => "Test User",
                'address'   => [
                    'address'   => "V942+HW4, Dhaka 1230",
                ],
                'image'     => "",
            ];
        }else {
            if($developer_credentials->mode != PaymentGatewayConst::ENV_PRODUCTION) {
                $page_title = "Process Error";
                $data = [
                    'title'         => "Requested with invalid credentials!",
                    'subtitle'      => "",
                    'button_text'   => "Cancel and return/Home",
                    'link'          => $request_record->data->cancel_url ?? url("/"),
                    'logo'          => "",
                ];
                return view('walletium-gateway.pages.error',compact('data','page_title'));
            }
            $submit_form_url = route('walletium.pay.v1.user.payment.preview.submit',$token);
            $user = $request_record->user;
            $payment_currency = Currency::where('code',$request_record->currency)->first();
            $user_wallet = UserWallet::where('user_id',$request_record->user->id)->where('currency_id',$payment_currency->id)->first(); 
        }

        $page_title = "Payment Confirm";
        return view('walletium-gateway.pages.confirm',compact('page_title','merchant_configuration','request_record','user','user_wallet','payment_gateway_image','token','submit_form_url'));
    }

    public function paymentConfirm(Request $request,$token) {

        // common code Start
        $request_record = PaymentOrderRequest::where('token',$token)->first();
        if(!$request_record) {
            $page_title = "Process Error";
            $data = [
                'title'         => "Payment request is invalid!",
                'subtitle'      => "Something went wrong! Go back and try again.",
                'button_text'   => "Home",
                'link'          => url('/'),
                'logo'          => "",
            ];
            return view('walletium-gateway.pages.error',compact('data','page_title'));
        }

        $merchant_configuration = MerchantConfiguration::first();
        if(!$merchant_configuration) {
            $page_title = "Process Error";
            $data = [
                'title'         => "Payment gateway no longer available",
                'subtitle'      => "",
                'button_text'   => "Cancel and return/Home",
                'link'          => $request_record->data->cancel_url ?? url("/"),
                'logo'          => "",
            ];
            return view('walletium-gateway.pages.error',compact('data','page_title'));
        }
        $payment_gateway_image = get_image($merchant_configuration->image,'merchant-config');

        if($request_record->authentication != true) {
            $page_title = "Process Error";
            $data = [
                'title'         => "Authentication Failed!",
                'subtitle'      => "You are requested with unauthenticated user. Please make sure you are authenticated",
                'button_text'   => "Got to Login",
                'link'          => route('walletium.pay.sandbox.v1.user.auth.form',$token),
                'logo'          => $payment_gateway_image,
            ];
            return view('walletium-gateway.pages.error',compact('data','page_title'));
        }

        $merchant = $request_record->merchant;
        $developer_credentials = $merchant->developerApi;

        if(Carbon::now() > $request_record->created_at->addSeconds($this->access_token_expire_time)) {
            $page_title = "Process Error";
            $data = [
                'title'         => "Session Expired!",
                'subtitle'      => "Your token session is expired. Go back and try again",
                'button_text'   => "Cancel and return",
                'link'          => $request_record->data->cancel_url ?? url("/"),
                'logo'          => $payment_gateway_image,
            ];
            return view('walletium-gateway.pages.error',compact('data','page_title'));
        }
        // common code End

        if($merchant_configuration->email_verify == true && (int) $request_record->email_verify == false) {

            if($developer_credentials->mode == PaymentGatewayConst::ENV_SANDBOX) {
                // No need to send mail, redirect verify page
                
                $request_record->update([
                    'email_code' => $this->test_email_verification_code,
                ]);
    
            }else {
                // Need to send mail to user email address
                $user = $request_record->user;
    
                $code = generate_random_code(6);
                $request_record->update([
                    'email_code' => $code,
                ]);
    
                $data = [
                    'fullname'  => $user->fullname,
                    'username'  => $user->username,
                    'email'     => $user->email,
                    'amount'    => $request_record->amount,
                    'currency'  => $request_record->currency,
                    'code'      => $code,
                ];
                try {
                    $user->notify(new PaymentVerification((object) $data));
                } catch (\Throwable $th) {
                    $page_title = "Process Error";
                    $data = [
                        'title'         => "Failed To Send Verification Email!",
                        'subtitle'      => "The email method is not working. Please contact system administrators.",
                        'button_text'   => "Got to Login",
                        'link'          => route('walletium.pay.sandbox.v1.user.auth.form',$token),
                        'logo'          => $payment_gateway_image,
                    ];
                    return view('walletium-gateway.pages.error',compact('data','page_title'));
                }
                
            }

            if(request()->is('*/sandbox/*')) {
                return redirect()->route('walletium.pay.sandbox.v1.user.auth.mail.verify.form',$token);
            }else {
                return redirect()->route('walletium.pay.v1.user.auth.mail.verify.form',$token);
            }
        }

        if($request_record->status != PaymentGatewayConst::CREATED) {
            $page_title = "Process Error";
            $data = [
                'title'         => "Payment request is invalid!",
                'subtitle'      => "Something went wrong! Go back and try again.",
                'button_text'   => "Cancel and return",
                'link'          => $request_record->data->cancel_url ?? url("/"),
                'logo'          => $payment_gateway_image,
            ];
            return view('walletium-gateway.pages.error',compact('data','page_title'));
        }

        if($request_record->merchant->developerApi->mode == PaymentGatewayConst::ENV_SANDBOX) {
            $merchant_wallet = SandboxWallet::where('user_id',$merchant->id)->whereHas('currency',function($q) use ($request_record) {
                $q->where('code',$request_record->currency);
            })->where('status',true)->first();
        }else {
            $merchant_wallet = UserWallet::where('user_id',$merchant->id)->whereHas('currency',function($q) use ($request_record) {
                $q->where('code',$request_record->currency);
            })->where('status',true)->first();
        }

        $charges = [
            'exchange_rate'         => 1,
            'sender_amount'         => $request_record->amount,
            'sender_currency'       => $request_record->currency,
            'receiver_amount'       => $request_record->amount,
            'receiver_currency'     => $request_record->currency,
            'percent_charge'        => 0,
            'fixed_charge'          => 0,
            'total_charge'          => 0,
            'sender_wallet_balance' => 100000, // set demo to testuser/testpayment
            'payable'               => $request_record->amount,
        ];

        // Check request comes from sandbox or production url
        if(request()->is("*/sandbox/*")) {
            // sandbox requested
            if($developer_credentials->mode != PaymentGatewayConst::ENV_SANDBOX) {
                $page_title = "Process Error";
                $data = [
                    'title'         => "Requested with invalid credentials!",
                    'subtitle'      => "",
                    'button_text'   => "Cancel and return/Home",
                    'link'          => $request_record->data->cancel_url ?? url("/"),
                    'logo'          => "",
                ];
                return view('walletium-gateway.pages.error',compact('data','page_title'));
            }

            // Sandbox transaction
            DB::beginTransaction();
            try{
                $trx_id = generate_unique_string("transactions","trx_id",16);
                // Sandbox TRX 
                $inserted_id = DB::table("transactions")->insertGetId([ 
                    'type'              => PaymentGatewayConst::TYPEMAKEPAYMENT,
                    'trx_id'            => $trx_id,
                    'user_type'         => GlobalConst::MERCHANT,
                    'user_id'       => $merchant_wallet->merchant->id,
                    'sandbox_wallet_id' => $merchant_wallet->id,
                    'available_balance' => $merchant_wallet->balance + $charges['receiver_amount'],
                    'request_amount'    => $charges['sender_amount'],
                    'request_currency'  => $charges['sender_currency'],
                    'exchange_rate'     => 1,
                    'percent_charge'    => $charges['percent_charge'],
                    'fixed_charge'      => $charges['fixed_charge'],
                    'total_charge'      => $charges['total_charge'],
                    'total_payable'     => $charges['receiver_amount'],
                    'receive_amount'    => $charges['receiver_amount'],
                    'receiver_type'     => GlobalConst::MERCHANT,
                    'receiver_id'       => $merchant_wallet->merchant->id,
                    'payment_currency'  => $charges['receiver_currency'],
                    'details'           => json_encode([
                        'receiver_username' => $merchant_wallet->merchant->username,
                        'sender_username'   => $this->testuser_username,
                        'charges'           => $charges,
                        'token'             => $token,
                    ]),
                    'status'     => PaymentGatewayConst::STATUSSUCCESS,
                    'created_at' => now(),
                ]); 

                $merchant_wallet->balance += $charges['receiver_amount'];
                $merchant_wallet->save();

                $request_record->update([
                    'status'    => PaymentGatewayConst::SUCCESS,
                ]);

                DB::commit();
            }catch(Exception $e) {
                DB::rollBack();
                $http_query = http_build_query([
                    'message'     => [
                        'code'  => 500,
                        'error' => [
                            $e->getMessage(),
                        ],
                    ],
                    'data'  => [],
                    'type'  => 'error',
                ]);

                return redirect($request_record->data->return_url."?".$http_query);
            }

            $payer = [
                'username'  => $this->testuser_username,
                'email'     => $this->testuser_email,
            ];

        }else {
            // production requested
            if($developer_credentials->mode != PaymentGatewayConst::ENV_PRODUCTION) {
                $page_title = "Process Error";
                $data = [
                    'title'         => "Requested with invalid credentials!",
                    'subtitle'      => "",
                    'button_text'   => "Cancel and return/Home",
                    'link'          => $request_record->data->cancel_url ?? url("/"),
                    'logo'          => "",
                ];
                return view('walletium-gateway.pages.error',compact('data','page_title'));
            }

            // Production transaction
            $user = $request_record->user;
            $user_wallet = UserWallet::where('user_id',$user->id)->whereHas('currency',function($q) use ($request_record){
                $q->where('code',$request_record->currency);
            })->where('status',GlobalConst::ACTIVE)->first();

            if(!$user_wallet) {
                $page_title = "Process Error";
                $data = [
                    'title'         => "Wallet not found!",
                    'subtitle'      => "Your wallet not available/active with currency (".$request_record->currency.")",
                    'button_text'   => "Cancel and return",
                    'link'          => $request_record->data->cancel_url ?? url("/"),
                    'logo'          => $payment_gateway_image,
                ];
                return view('walletium-gateway.pages.error',compact('data','page_title'));
            }

            if($user_wallet->balance < $request_record->amount) {
                $page_title = "Process Error";
                $data = [
                    'title'         => "Insufficient balance!",
                    'subtitle'      => "Request failed due to insufficient balance in your wallet",
                    'button_text'   => "Cancel and return",
                    'link'          => $request_record->data->cancel_url ?? url("/"),
                    'logo'          => $payment_gateway_image,
                ];
                return view('walletium-gateway.pages.error',compact('data','page_title'));
            }

            $charges['sender_wallet_balance'] = $user_wallet->balance - $request_record->amount;

            // Create Transactions  for production
            DB::beginTransaction();
            try{
                $trx_id = generate_unique_string("transactions","trx_id",16);
                 
                $inserted_id = DB::table("transactions")->insertGetId([ 
                    'type'              => PaymentGatewayConst::TYPEMAKEPAYMENT,
                    'attribute'              => PaymentGatewayConst::PAYMENT_CREATE,
                    'trx_id'            => $trx_id,
                    'user_type'         => GlobalConst::USER,
                    'user_id'           => $user_wallet->user->id,
                    'wallet_id'         => $user_wallet->id,
                    'available_balance' => $user_wallet->balance - $request_record->amount,
                    'request_amount'    => $request_record->amount,
                    'request_currency'  => $user_wallet->currency->code,
                    'exchange_rate'     => 1,
                    'percent_charge'    => $charges['percent_charge'],
                    'fixed_charge'      => $charges['fixed_charge'],
                    'total_charge'      => $charges['total_charge'],
                    'total_payable'     => $request_record->amount,
                    'receive_amount'    => $request_record->amount,
                    'receiver_type'     => GlobalConst::MERCHANT,
                    'receiver_id'       => $merchant_wallet->user->id,
                    'payment_currency'  => $user_wallet->currency->code,
                    'details'           => json_encode([
                        'receiver_username' => $merchant_wallet->user->username,
                        'sender_username'   => $user_wallet->user->username,
                        'charges'           => $charges,
                    ]),
                    'status'     => PaymentGatewayConst::STATUSSUCCESS,
                    'created_at' => now(),
                ]);  

                $user_wallet->balance -= $charges['payable'];
                $user_wallet->save();

                $merchant_wallet->balance += $charges['receiver_amount'];
                $merchant_wallet->save();

                $request_record->update([
                    'status'    => PaymentGatewayConst::SUCCESS,
                ]);

                DB::commit();
            }catch(Exception $e) {
                DB::rollBack();
                $http_query = http_build_query([
                    'message'     => [
                        'code'  => 500,
                        'error' => [
                            $e->getMessage(),
                        ],
                    ],
                    'data'  => [],
                    'type'  => 'error',
                ]);

                return redirect($request_record->data->return_url."?".$http_query);
            }

            $payer = [
                'username'  => $user->username,
                'email'     => $user->email,
            ];
        }

        $http_query = http_build_query([
            'message'     => [
                'code'  => 200,
                'success' => [
                    PaymentGatewayConst::SUCCESS,
                ],
            ],
            'data'  => [
                'token'     => $token,
                'trx_id'    => $trx_id,
                'payer'     => $payer,
            ],
            'type'  => 'success',
        ]);

        return redirect($request_record->data->return_url."?".$http_query);
    }
}
