<?php

namespace App\Http\Controllers\Admin;

use App\Models\Transaction;
use App\Models\RequestMoney;
use Illuminate\Http\Request;
use App\Http\Helpers\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class RequestMoneyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = "All Logs";
        $transactions = RequestMoney::with('transaction')->latest()->paginate(20);   
        return view('admin.sections.request-money.index',compact(
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
        $transactions = RequestMoney::with('transaction')->where('status', 2)->latest()->paginate(20);
        return view('admin.sections.request-money.index',compact(
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
        $transactions = RequestMoney::with('transaction')->where('status', 1)->latest()->paginate(20);
        return view('admin.sections.request-money.index',compact(
            'page_title',
            'transactions'
        ));
    } 
    public function requestMoneyDetails($id){

        $data = RequestMoney::where('id',$id)->with('transaction','transaction.user')->first();
        $page_title = "Request Money Details For";
        // return $data;
        return view('admin.sections.request-money.details', compact(
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

        $transactions = RequestMoney::with('transaction')
        ->whereHas('transaction', function ($query) use ($validated) {
            $query->where('trx_id', 'like', '%' . $validated['text'] . '%');
        })
        ->latest()
        ->paginate(20);
        return view('admin.components.data-table.request-money-transaction-log', compact(
            'transactions'
        ));
    }
}
