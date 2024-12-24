<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    public function apiKey() {
        $page_title = __("API Key");
        return view('user.api-key.api-key',compact('page_title'));
    }
}
