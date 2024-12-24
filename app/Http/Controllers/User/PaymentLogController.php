<?php

namespace App\Http\Controllers\User;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Http\Controllers\Controller;
use App\Constants\PaymentGatewayConst;

class PaymentLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = "Payment Log";
        $transactions = Transaction::where(function ($query) {
            $query->where('receiver_type', GlobalConst::MERCHANT)
                  ->where('receiver_id', auth()->user()->id);
        })->where('attribute',PaymentGatewayConst::PAYMENT_CREATE)->orderByDesc('id')
          ->take(15)
          ->get();
        return view('user.sections.payment-log.index',compact('page_title','transactions'));
    }
}
