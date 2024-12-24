<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Providers\Admin\BasicSettingsProvider;
use App\Constants\GlobalConst;

class KycVerificationGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $basic_settings = BasicSettingsProvider::get();
        if($basic_settings->kyc_verification) {
            $user = auth()->user();
            if($user->kyc_verified != GlobalConst::APPROVED) {

                $smg = __("Please verify your KYC information before any withdrawal action");
                if($user->kyc_verified == GlobalConst::PENDING) {
                    $smg = __("Your KYC information is pending. Please wait for admin confirmation");
                }

                return redirect()->route("user.authorize.kyc")->with(['warning' => [$smg]]);
            }
        }
        return $next($request);
    }
}
