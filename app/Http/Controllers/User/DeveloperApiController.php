<?php

namespace App\Http\Controllers\User;

use App\Constants\PaymentGatewayConst;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class DeveloperApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = "API Credentials";
        return view('user.api-key.api-key',compact('page_title'));
    }

    public function updateMode(Request $request) {
        $merchant_developer_api = auth()->user()->developerApi;
        if(!$merchant_developer_api) return back()->with(['error' => ['Developer API not found!']]);

        $update_mode = ($merchant_developer_api->mode == PaymentGatewayConst::ENV_SANDBOX) ? PaymentGatewayConst::ENV_PRODUCTION : PaymentGatewayConst::ENV_SANDBOX;

        try{
            $merchant_developer_api->update([
                'mode'      => $update_mode,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }
        return back()->with(['success' => ['Developer API mode updated successfully!']]);
    }
}
