<?php

namespace App\Http\Controllers\Admin;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Helpers\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SendMoneyController extends Controller
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
        )->where('type', 'TRANSFER-MONEY')->latest()->paginate(20);
        return view('admin.sections.send-money.index',compact(
            'page_title',
            'transactions'
        ));
    } 
    /**
     * Display All Pending Logs
     * @return view 
     */
    public function pending() {
        $page_title = "Pending Logs";
        $transactions = Transaction::with(
            'user:id,firstname,email,username,mobile',
            'gateway_currency:id,name',
        )->where('type', 'TRANSFER-MONEY')->where('status', 2)->latest()->paginate(20);
        return view('admin.sections.send-money.index',compact(
            'page_title',
            'transactions'
        ));
    } 
    /**
     * Display All Complete Logs
     * @return view
     */
    public function complete() {
        $page_title = "Complete Logs";
        $transactions = Transaction::with(
            'user:id,firstname,email,username,mobile',
            'gateway_currency:id,name',
        )->where('type', 'TRANSFER-MONEY')->where('status', 1)->latest()->paginate(20);
        return view('admin.sections.send-money.index',compact(
            'page_title',
            'transactions'
        ));
    } 
    /**
     * Display All Canceled Logs
     * @return view
     */
    public function canceled() {
        $page_title = "Canceled Logs";
        $transactions = Transaction::with(
            'user:id,firstname,email,username,mobile',
            'gateway_currency:id,name',
        )->where('type', 'TRANSFER-MONEY')->where('status', 4)->latest()->paginate(20);
        return view('admin.sections.send-money.index',compact(
            'page_title',
            'transactions'
        ));
    } 
    public function sendMoneyDetails($id){

        $data = Transaction::where('id',$id)->with(
          'user:id,firstname,lastname,email,username,full_mobile',
            'gateway_currency:id,name,alias,payment_gateway_id,currency_code,rate',
        )->where('type', 'TRANSFER-MONEY')->first();
        $page_title = "Transfer Money Details For".'  '.$data->trx_id;
        return view('admin.sections.send-money.details', compact(
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
        )->where('type', 'TRANSFER-MONEY')->where("trx_id","like","%".$validated['text']."%")->latest()->paginate(20);
        return view('admin.components.data-table.send-money-transaction-log', compact(
            'transactions'
        ));
    }
}
