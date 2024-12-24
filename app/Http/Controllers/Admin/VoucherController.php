<?php

namespace App\Http\Controllers\Admin;

use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Http\Helpers\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class VoucherController extends Controller
{
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = "All Logs";
        $transactions = Voucher::with('transaction')->latest()->paginate(20);   
        return view('admin.sections.voucher-money.index',compact(
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
        $transactions = Voucher::with('transaction')->where('status', 2)->latest()->paginate(20);
        return view('admin.sections.voucher-money.index',compact(
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
        $transactions = Voucher::with('transaction')->where('status', 1)->latest()->paginate(20);
        return view('admin.sections.voucher-money.index',compact(
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
        $transactions = Voucher::with('transaction')->where('status', 4)->latest()->paginate(20);
        return view('admin.sections.voucher-money.index',compact(
            'page_title',
            'transactions'
        ));
    } 
    public function VoucherDetails($id){

        $data = Voucher::where('id',$id)->with('transaction','transaction.user')->first();
        $page_title = "Request Money Details For";
        // return $data;
        return view('admin.sections.voucher-money.details', compact(
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

        $transactions = Voucher::with('transaction')
        ->whereHas('transaction', function ($query) use ($validated) {
            $query->where('trx_id', 'like', '%' . $validated['text'] . '%');
        })
        ->latest()
        ->paginate(20);
        return view('admin.components.data-table.voucher-money-transaction-log', compact(
            'transactions'
        ));
    }
}
