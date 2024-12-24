<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $page_title = "Request Moneny";
        return view('frontend.pages.user.invoice', compact('page_title'));
    }

    public function create(Request $request)
    {
        $page_title = "Withdraw Log";
        return view('frontend.pages.user.invoice_create', compact('page_title'));
    }
}
