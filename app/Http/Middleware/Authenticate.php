<?php

namespace App\Http\Middleware;

use App\Http\Helpers\Response;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            if($request->routeIs('admin.*')) {
                return route('admin.login');
            }else if($request->routeIs("user.*")) {
                return route('user.login');
            }else if($request->routeIs("merchant.*")) {
                return route('merchant.login');
            } 
            return abort(Response::warning(['Access denied! Unauthenticated'],null,401));
        }
        return abort(Response::warning(['Access denied! Unauthenticated'],null,401));
    }
}
