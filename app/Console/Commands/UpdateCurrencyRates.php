<?php

namespace App\Console\Commands;
use Exception;
use App\Constants\GlobalConst;
use App\Models\Admin\Currency;
use Illuminate\Console\Command;
use App\Http\Helpers\CurrencyLayer;
use App\Models\Admin\PaymentGatewayCurrency;
use App\Models\Admin\LiveExchangeRateApiSetting;

class UpdateCurrencyRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $api_rates = (new CurrencyLayer())->getLiveExchangeRates();

            if (isset($api_rates) && $api_rates['status'] == false) {
                return back()->with(['error' => [$api_rates['message'] ?? __("Something went wrong! Please try again.")]]);
            }
            $api_rates = $api_rates['data'];
            $provider = LiveExchangeRateApiSetting::where('slug', GlobalConst::CURRENCY_LAYER)->first();


            if ($provider->currency_module == 1) {
                $currencies = Currency::active()->get();
                foreach ($currencies as $currency) {
                    if (array_key_exists($currency->code, $api_rates)) {
                        $currency->rate = (float) str_replace(',', '', $api_rates[$currency->code]);
                        $currency->save();
                    }
                }
            }


            if ($provider->payment_gateway_module == 1) {
                $payment_gateways_currencies = PaymentGatewayCurrency::whereHas('gateway', function ($gateway) {
                    $gateway->where('status', 1);
                })->get();
                foreach ($payment_gateways_currencies as $currency) {
                    if (array_key_exists($currency->currency_code, $api_rates)) {
                        $currency->rate = (float) str_replace(',', '', $api_rates[$currency->currency_code]);
                        $currency->save();
                    }
                }
            }
            return back()->with(['success' => [__("Currency Rate Updated By Currency Layer.")]]);
        } catch (Exception $e) {
            return back()->with(['error' => [__("Something went wrong! Please try again.")]]);
        }
    }
}
