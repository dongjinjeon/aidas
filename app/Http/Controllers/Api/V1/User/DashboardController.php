<?php

namespace App\Http\Controllers\Api\V1\User;

use Carbon\CarbonPeriod;
use App\Models\UserWallet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Http\Helpers\Response;
use Illuminate\Support\Carbon;
use App\Models\UserNotification;
use App\Models\UserHasInvestPlan;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Constants\PaymentGatewayConst;
use App\Providers\Admin\CurrencyProvider;

class DashboardController extends Controller
{
    public function dashboard() { 
        // User Wallets 
        $user_wallets = UserWallet::auth()->with('currency')->get()->map(function($data){
            return[
                'name'                  => $data->currency->name,
                'balance'               => $data->balance,
                'currency_code'         => $data->currency->code,
                'currency_symbol'       => $data->currency->symbol,
                'currency_type'         => $data->currency->type,
                'rate'                  => $data->currency->rate,
                'flag'                  => $data->currency->flag,
                'image_path'            => get_files_public_path('currency-flag'),
            ];
        });
        // Transaction logs
        $transactions = Transaction::auth()->latest()->take(10)->get();
        $transactions->makeHidden([
            'id',
            'user_type',
            'user_id',
            'wallet_id',
            'payment_gateway_currency_id',
            'request_amount',
            'exchange_rate',
            'percent_charge',
            'fixed_charge',
            'total_charge',
            'total_payable',
            'receiver_type',
            'receiver_id',
            'available_balance',
            'payment_currency',
            'input_values',
            'details',
            'reject_reason',
            'remark',
            'stringStatus',
            'callback_ref',
            'updated_at',
        ]);

        // Chart Data
        $monthly_day_list = CarbonPeriod::between(now()->startOfDay()->subDays(30),today()->endOfDay())->toArray();
        $define_day_value = array_fill_keys(array_values($monthly_day_list),"0.00");

        // User Information
        $user_info = auth()->user()->only([
            'id',
            'firstname',
            'lastname',
            'fullname',
            'username',
            'email',
            'image',
            'mobile_code',
            'mobile',
            'full_mobile',
            'email_verified',
            'kyc_verified',
            'two_factor_verified',
            'two_factor_status',
            'two_factor_secret',
        ]);

        $profile_image_paths = [
            'base_url'          => url("/"),
            'path_location'     => files_asset_path_basename("user-profile"),
            'default_image'     => files_asset_path_basename("profile-default"),
        ];
        return Response::success([__('User dashboard data fetch successfully!')],[
            'instructions'  => [
                'transaction_types' => [
                    PaymentGatewayConst::TYPEADDMONEY,
                    PaymentGatewayConst::TYPETRANSFERMONEY,
                    PaymentGatewayConst::TYPEWITHDRAW,
                ],
                'recent_transactions'   => [
                    'status'        => '1: Success, 2: Pending, 3: Hold, 4: Rejected',
                ],
                'user_info'         => [
                    'kyc_verified'  => "0: Default, 1: Approved, 2: Pending, 3:Rejected",
                ]
            ],
            
            'user_info'     => $user_info,
            'wallets'       => $user_wallets,
            'recent_transactions'   => $transactions,
            'profile_image_paths'   => $profile_image_paths,
        ]);
    }

    public function notifications() {
        $notifications = UserNotification::where('user_id', auth()->user()->id)->latest()->take(5)->get()->map(function($item){
            return[ 
                'id'      => $item->id,
                'user_id' => $item->user_id,
                'type'    => $item->type,
                'message' => [ 
                    'title'   => __($item->message->title),
                    'message' => $item->message->message,
                    'image' => $item->message->image,
                    'time'    => $item->created_at->diffForHumans(),
                ],
                'seen'       => $item->seen,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
        });
        return Response::success([__('User Notification data fetch successfully!')],[
            'notifications'      => $notifications, 
        ]); 
    }
}
