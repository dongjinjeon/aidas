<?php

namespace App\Http\Controllers\Api\V1\User;

use Exception;
use App\Models\Voucher;
use App\Models\Transaction;
use App\Models\RequestMoney;
use Illuminate\Http\Request;
use App\Http\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Constants\PaymentGatewayConst;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function slugValue($slug) {
        $values =  [
            'add-money'         => PaymentGatewayConst::TYPEADDMONEY,
            'money-out'         => PaymentGatewayConst::TYPEMONEYOUT,
            'money-transfer'    => PaymentGatewayConst::TYPETRANSFERMONEY,
            'money-exchange'    => PaymentGatewayConst::TYPEMONEYEXCHANGE,
            'make-payment'      => PaymentGatewayConst::TYPEMAKEPAYMENT,
            'withdraw'          => PaymentGatewayConst::TYPEWITHDRAW,
        ];

        if(!array_key_exists($slug,$values)) return abort(404);
        return $values[$slug];
    }

    public function log(Request $request) { 
        $validator = Validator::make($request->all(),[
            'slug'      => "nullable|string|in:add-money,money-out,money-transfer,money-exchange,withdraw,make-payment",
        ]);
        if($validator->fails()) return Response::error($validator->errors()->all(),[]); 
        $validated = $validator->validate();

        try{ 
            if(isset($validated['slug']) && $validated['slug'] != "") {
                $transactions = Transaction::auth()->where("type",$this->slugValue($validated['slug']))->orderByDesc("id")->paginate(20);
            }else {
                $transactions = Transaction::auth()->orderByDesc("id")->paginate(20);
            }  
            $transactions->getCollection()->transform(function ($item) {
                return [
                    'id'               => $item->id,
                    'type'             => $item->type,
                    'attribute'        => $item->attribute,
                    'trx_id'           => $item->trx_id,
                    'gateway_currency' => $item->gateway_currency->name ?? null,
                    'transaction_type' => $item->type,
                    'request_amount'   => $item->request_amount,
                    'request_currency' => $item->request_currency,
                    'exchange_rate'    => $item->exchange_rate,
                    'total_charge'     => $item->total_charge,
                    'total_payable'    => $item->total_payable,
                    'receive_amount'   => $item->receive_amount ?? null,
                    'payment_currency' => $item->payment_currency,
                    'receiver_username' => $item->receiver_info->username ?? null,
                    'remark'           => $item->remark,
                    'status'           => $item->status,
                    'created_at'       => $item->created_at,
                ];
            });

        }catch(Exception $e) {
            return Response::error([__('Something went wrong! Please try again')],[],500);
        }

        return Response::success([__('Transactions fetch successfully!')],[
            'instructions'  => [
                'slug'      => "add-money,money-transfer,withdraw",
                'status'    => "1: Success, 2: Pending, 3: Hold, 4: Rejected, 5: Waiting"
            ],
            'transaction_types' => [
                PaymentGatewayConst::TYPEADDMONEY,
                PaymentGatewayConst::TYPEMONEYOUT,
                PaymentGatewayConst::TYPETRANSFERMONEY,
                PaymentGatewayConst::TYPEMONEYEXCHANGE,
                PaymentGatewayConst::TYPEWITHDRAW,
                PaymentGatewayConst::TYPEMAKEPAYMENT,
            ],
            'transactions'  => $transactions,
        ],200);
    }
    public function requestMoney(){ 
        try{
            $transactions = RequestMoney::with('transaction')
            ->where(function ($query) {
                $query->where('user_id', auth()->user()->id)
                    ->orWhereHas('transaction', function ($subquery) {
                        $subquery->where('user_id', auth()->user()->id);
                    });
            })->latest()->paginate(20); 
            
            $transactions->getCollection()->transform(function ($item) {
                return [
                    'id'               => $item->id,
                    'identifier'       => $item->identifier,
                    'created_by'       => $item->user->email ?? null,
                    'paid_by'       => $item->transaction->user->email ?? null,
                    'request_amount'   => $item->request_amount ?? null,
                    'request_currency' => $item->request_currency ?? null,
                    'exchange_rate'    => $item->exchange_rate ?? null,
                    'total_charge'     => $item->total_charge ?? null,
                    'total_payable'    => $item->total_payable ?? null,
                    'link'    => $item->link ?? null,
                    'remark'    => $item->remark ?? null,
                    'status'           => $item->status,
                    'created_at'       => $item->created_at,
                    'transaction'               => [
                        'id'               => $item->transaction->id ?? null,
                        'type'             => $item->transaction->type ?? null,
                        'attribute'        => $item->transaction->attribute ?? null,
                        'trx_id'           => $item->transaction->trx_id ?? null,
                        'transaction_type' => $item->transaction->type ?? null,
                        'request_amount'   => $item->transaction->request_amount ?? null,
                        'request_currency' => $item->transaction->request_currency ?? null,
                        'exchange_rate'    => $item->transaction->exchange_rate ?? null,
                        'total_charge'     => $item->transaction->total_charge ?? null,
                        'total_payable'    => $item->transaction->total_payable ?? null,
                        'receive_amount'   => $item->transaction->receive_amount ?? null,
                        'payment_currency' => $item->transaction->payment_currency ?? null,
                        'remark'           => $item->transaction->remark ?? null,
                        'status'           => $item->transaction->status ?? null,
                        'created_at'       => $item->transaction->created_at ?? null,
                    ],
                ];
            });
        }catch(Exception $e) { 
            return Response::error([__('Failed to fetch data. Please try again')],[],500);
        }

        return Response::success([__('Transactions fetch successfully!')],['transactions' => $transactions],200);
    }
    public function voucherMoney(){ 
        try{
            $transactions = Voucher::with('transaction')
            ->where(function ($query) {
                $query->where('user_id', auth()->user()->id)
                    ->orWhereHas('transaction', function ($subquery) {
                        $subquery->where('receiver_id', auth()->user()->id);
                    });
            })->latest()->paginate(20);
            $transactions->getCollection()->transform(function ($item) {
                return [
                    'id'               => $item->id,
                    'code'       => $item->code,
                    'created_by'       => $item->user->email ?? null,
                    'paid_by'       => $item->transaction->user->email ?? null,
                    'request_amount'   => $item->request_amount ?? null,
                    'request_currency' => $item->request_currency ?? null,
                    'exchange_rate'    => $item->exchange_rate ?? null,
                    'total_charge'     => $item->total_charge ?? null,
                    'total_payable'    => $item->total_payable ?? null,  
                    'status'           => $item->status,
                    'created_at'       => $item->created_at,
                    'transaction'               => [
                        'id'               => $item->transaction->id ?? null,
                        'type'             => $item->transaction->type ?? null,
                        'attribute'        => $item->transaction->attribute ?? null,
                        'trx_id'           => $item->transaction->trx_id ?? null,
                        'transaction_type' => $item->transaction->type ?? null,
                        'request_amount'   => $item->transaction->request_amount ?? null,
                        'request_currency' => $item->transaction->request_currency ?? null,
                        'exchange_rate'    => $item->transaction->exchange_rate ?? null,
                        'total_charge'     => $item->transaction->total_charge ?? null,
                        'total_payable'    => $item->transaction->total_payable ?? null,
                        'receive_amount'   => $item->transaction->receive_amount ?? null,
                        'payment_currency' => $item->transaction->payment_currency ?? null,
                        'remark'           => $item->transaction->remark ?? null,
                        'status'           => $item->transaction->status ?? null,
                        'created_at'       => $item->transaction->created_at ?? null,
                    ],
                ];
            });  
        }catch(Exception $e) {
            return Response::error([__('Failed to fetch data. Please try again')],[],500);
        }

        return Response::success([__('Transactions fetch successfully!')],['transactions' => $transactions],200);
    }
}
