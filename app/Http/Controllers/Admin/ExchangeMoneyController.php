<?php

namespace App\Http\Controllers\Admin;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Helpers\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ExchangeMoneyController extends Controller
{
          /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = "All Logs";
        $transactions = Transaction::with(
            'user:id,firstname,email,username,mobile',
            'gateway_currency:id,name',
        )->where('type', 'MONEY-EXCHANGE')->latest()->paginate(20);
        return view('admin.sections.exchange-money.index',compact(
            'page_title',
            'transactions'
        ));
    } 
    public function exchangeMoneyDetails($id){

        $data = Transaction::where('id',$id)->with(
          'user:id,firstname,lastname,email,username,full_mobile',
            'gateway_currency:id,name,alias,payment_gateway_id,currency_code,rate',
        )->where('type', 'MONEY-EXCHANGE')->first();
        $page_title = "Transfer Money Details For".'  '.$data->trx_id;
        return view('admin.sections.exchange-money.details', compact(
            'page_title',
            'data'
        ));
    } 
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'text'  => 'required|string',
        ]);

        if($validator->fails()) {
            $error = ['error' => $validator->errors()];
            return Response::error($error,null,400);
        }
        $validated = $validator->validate();

        $transactions = Transaction::with(
            'user:id,firstname,email,username,mobile',
            'gateway_currency:id,name',
        )->where('type', 'MONEY-EXCHANGE')->where("trx_id","like","%".$validated['text']."%")->latest()->paginate(20);
        return view('admin.components.data-table.send-money-transaction-log', compact(
            'transactions'
        ));
    }
}
