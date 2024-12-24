<?php
namespace App\Http\Controllers\User;

use App\Constants\GlobalConst;
use Exception;
use App\Models\User;
use App\Models\UserWallet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Constants\PaymentGatewayConst;

class DashboardController extends Controller
{
    public function index()
    {
        $page_title = "Dashboard";
        $userWallet = UserWallet::with('currency')->where(['user_id' => auth()->user()->id, 'status' => 1])->orderBY('balance', 'DESC')->get(); 
        $transactions = Transaction::where(function ($query) {
            $query->where('user_type', GlobalConst::USER)
                  ->where('receiver_id', auth()->user()->id);
        })->orWhere('user_id', auth()->user()->id)
          ->orderByDesc('id')
          ->take(5)
          ->get();  
        //chart data calculation 
        $start = strtotime(date('Y-m-01'));
        $end = strtotime(date('Y-m-31'));
        //add money data 
        $add_money_success_data  = [];
        $withdraw_money_success_data  = [];
        $send_money_success_data = [];
        
        //get chart data  
        $month_day  = [];
        while ($start <= $end) {
            $start_date = date('Y-m-d', $start);
            //================ Monthley add money start======================== 
            $add_money_trn_active = Transaction::where('user_id', auth()->user()->id)
                                        ->where('type', PaymentGatewayConst::TYPEADDMONEY)
                                        ->whereDate('created_at',$start_date)
                                        ->where('status',PaymentGatewayConst::STATUSSUCCESS)
                                        ->count();

            $withdraw_money_trn_active = Transaction::where('user_id', auth()->user()->id)
                                        ->where('type', PaymentGatewayConst::TYPEWITHDRAW)
                                        ->whereDate('created_at',$start_date)
                                        ->where('status',PaymentGatewayConst::STATUSSUCCESS)
                                        ->count();

            $send_money_trn_active = Transaction::where('user_id', auth()->user()->id)
                                        ->where('type', PaymentGatewayConst::TYPETRANSFERMONEY)
                                        ->whereDate('created_at',$start_date)
                                        ->where('status',PaymentGatewayConst::STATUSSUCCESS)
                                        ->count();

            $add_money_success_data[]  = $add_money_trn_active;
            $withdraw_money_success_data[]  = $withdraw_money_trn_active;
            $send_money_success_data[]  = $send_money_trn_active;

            $month_day[] = date('Y-m-d', $start);
            $start = strtotime('+1 day',$start);
        }
        $chart_one_data = [
            'add_money'  =>$add_money_success_data,
            'withdraq_money'  => $withdraw_money_success_data,
            'send_money' => $send_money_success_data,
        ];
        $chartData =[   
            'chart_one_data'   => $chart_one_data, 
            'month_day'   => $month_day, 
        ]; 
        return view('user.dashboard',compact("page_title",'userWallet','transactions','chartData'));
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('user.login');
    }
    public function deleteAccount(Request $request) {  
        $user = User::findOrFail(auth()->user()->id);
        $user->status = false;
        $user->email_verified = false;
        $user->sms_verified = false;
        $user->kyc_verified = false;
        $user->deleted_at = now();
        $user->save();
        try{
            Auth::logout();
            return redirect()->route('index')->with(['success' => [__('Your profile deleted successfully!')]]);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }
    }
}
