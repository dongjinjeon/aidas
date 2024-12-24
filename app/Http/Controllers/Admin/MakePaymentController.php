<?php

namespace App\Http\Controllers\Admin;

use App\Models\Transaction;
use App\Http\Controllers\Controller;
use App\Constants\PaymentGatewayConst;

class MakePaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = "All Logs";
        $transactions = Transaction::makePayment()->orderByDesc("id")->paginate(12);
        return view('admin.sections.make-payment.index',compact(
            'page_title',
            'transactions',
        ));
    }

    public function details($trx_id) {
        $transactions = Transaction::where('trx_id',$trx_id)->first();
        if(!$transactions) return back()->with(['error' => ['Transaction Information not found!']]);
        $page_title = "Transaction Logs";
        // return $transactions;
        return view('admin.sections.make-payment.details',compact("page_title","transactions"));
    }
}
