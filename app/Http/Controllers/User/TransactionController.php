<?php

namespace App\Http\Controllers\User;

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
            'add-money-log'      => PaymentGatewayConst::TYPEADDMONEY,
            'money-out-log'      => PaymentGatewayConst::TYPEMONEYOUT,
            'send-money-log'     => PaymentGatewayConst::TYPETRANSFERMONEY,
            'money-exchange-log' => PaymentGatewayConst::TYPEMONEYEXCHANGE,
            'make-payment-log'   => PaymentGatewayConst::TYPEMAKEPAYMENT,
            'withdraw-log'       => PaymentGatewayConst::TYPEWITHDRAW,
            'request-money-log'  => PaymentGatewayConst::REQUESTMONEY,
            'voucher-log'        => PaymentGatewayConst::REDEEMVOUCHER,
        ];

        if(!array_key_exists($slug,$values)) return abort(404);
        return $values[$slug];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($slug = null) {
        if($slug != null){
            $transactions = Transaction::auth()->where("type",$this->slugValue($slug))->orderByDesc("id")->paginate(20);
            $page_title = ucwords(str_replace("-", " ", $slug));
        }else {
            $transactions = Transaction::auth()->orderByDesc("id")->paginate(12);
            $page_title = "Transaction Log";
        }

        return view('user.sections.transaction.index',compact("page_title","transactions","slug"));
    }

    public function search(Request $request) {
        $validator = Validator::make($request->all(),[
            'text'  => 'required|string',
        ]);
         
        if($validator->fails()) {
            return Response::error($validator->errors(),null,400);
        }

        $validated = $validator->validate();

        try{
            $transactions = Transaction::auth()->where("type",$this->slugValue($request->type))->search($validated['text'])->take(10)->get();
        }catch(Exception $e){
            $error = ['error' => [__('Something went wrong!. Please try again')]];
            return Response::error($error,null,500);
        }

        return view('user.components.wallets.transation-log',compact('transactions'));
    }

    public function requestMoney() {
        $page_title = "Request Money Log";
        $transactions = RequestMoney::with('transaction')
        ->where(function ($query) {
            $query->where('user_id', auth()->user()->id)
                ->orWhereHas('transaction', function ($subquery) {
                    $subquery->where('user_id', auth()->user()->id);
                });
        })->latest()->take(20)->get(); 
        return view('user.sections.transaction.request-money-transaction',compact("page_title","transactions"));
    }
    public function searchrequestMoney(Request $request) {
        $validator = Validator::make($request->all(),[
            'text'  => 'required|string',
        ]);
        if($validator->fails()) {
            return Response::error($validator->errors(),null,400);
        } 
        $validated = $validator->validate();
        
        $transactions = RequestMoney::with('transaction')
        ->where('user_id', auth()->user()->id)
        ->whereHas('transaction', function ($subquery) use ($validated) {
            $subquery->where("type", $this->slugValue('request-money-log'))
                     ->where("trx_id", $validated['text']);
        })
        ->latest()
        ->take(20)
        ->get();
        return view('user.components.wallets.request-money-transation-log',compact("transactions"));
    }

    public function voucherLog() {
        $page_title = "Voucher Log";
        $transactions = Voucher::with('transaction')
        ->where(function ($query) {
            $query->where('user_id', auth()->user()->id)
                ->orWhereHas('transaction', function ($subquery) {
                    $subquery->where('receiver_id', auth()->user()->id);
                });
        })->latest()->take(20)->get(); 
        return view('user.sections.transaction.voucher-log-transaction',compact("page_title","transactions"));
    }
    public function searchVoucherMoney(Request $request) {
        $validator = Validator::make($request->all(),[
            'text'  => 'required|string',
        ]);
        if($validator->fails()) {
            return Response::error($validator->errors(),null,400);
        } 
        $validated = $validator->validate();
        
        $transactions = Voucher::with('transaction')
        ->where('user_id', auth()->user()->id)
        ->orWhereHas('transaction', function ($subquery) use ($validated) {
            $subquery->where("type", $this->slugValue('voucher-log'))
                     ->where("trx_id", $validated['text']);
        })
        ->latest()
        ->take(20)
        ->get(); 
         
        return view('user.components.wallets.voucher-transation-log',compact("transactions"));
    }
}
