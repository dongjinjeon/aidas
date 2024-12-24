<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\User\UserSeeder;
use Database\Seeders\Admin\RoleSeeder;
use Database\Seeders\Admin\AdminSeeder;
use Database\Seeders\Admin\CurrencySeeder;
use Database\Seeders\Admin\LanguageSeeder;
use Database\Seeders\Admin\SetupKycSeeder;
use Database\Seeders\Admin\SetupSeoSeeder;
use Database\Seeders\Admin\ExtensionSeeder;
use Database\Seeders\Admin\SetupPageSeeder;
use Database\Seeders\User\UserWalletSeeder;
use Database\Seeders\Admin\UsefulLinkSeeder;
use Database\Seeders\Admin\AdminHasRoleSeeder;
use Database\Seeders\Admin\AnnouncementSeeder;
use Database\Seeders\Admin\AppSettingsSeeder; 
use Database\Seeders\Admin\SiteSectionsSeeder;
use Database\Seeders\Admin\BasicSettingsSeeder;
use Database\Seeders\Admin\OnboardScreenSeeder;
use Database\Seeders\User\SandboxWalletSeeder; 
use Database\Seeders\Admin\PaymentGatewaySeeder;
use Database\Seeders\Admin\LiveExchangeRateSeeder;
use Database\Seeders\Admin\SystemMaintenanceSeeder;
use Database\Seeders\Admin\TransactionSettingSeeder;
use Database\Seeders\Admin\AnnouncementCategorySeeder;
use Database\Seeders\Admin\MerchantConfigurationSeeder;
use Database\Seeders\Fresh\ExtensionSeeder as FreshExtensionSeeder;
use Database\Seeders\Fresh\BasicSettingsSeeder as FreshBasicSettingsSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //demo
        // $this->call([
        //     AdminSeeder::class,
        //     RoleSeeder::class,
        //     TransactionSettingSeeder::class,
        //     CurrencySeeder::class,
        //     BasicSettingsSeeder::class,
        //     AppSettingsSeeder::class,
        //     OnboardScreenSeeder::class,
        //     SetupSeoSeeder::class,
        //     AppSettingsSeeder::class,
        //     SiteSectionsSeeder::class,
        //     SetupKycSeeder::class,
        //     ExtensionSeeder::class,
        //     AdminHasRoleSeeder::class,
        //     UserSeeder::class,
        //     UserWalletSeeder::class,
        //     SandboxWalletSeeder::class,
        //     SetupPageSeeder::class,
        //     LanguageSeeder::class,
        //     UsefulLinkSeeder::class,
        //     PaymentGatewaySeeder::class,
        //     AnnouncementCategorySeeder::class,
        //     AnnouncementSeeder::class,

        //     SandboxWalletSeeder::class,
        //     MerchantConfigurationSeeder::class,
            
        //     SystemMaintenanceSeeder::class,
        //     LiveExchangeRateSeeder::class,

        // ]);

        //fresh
        $this->call([
            AdminSeeder::class,
            RoleSeeder::class,
            TransactionSettingSeeder::class,
            CurrencySeeder::class,
            FreshBasicSettingsSeeder::class,
            AppSettingsSeeder::class,
            OnboardScreenSeeder::class,
            SetupSeoSeeder::class,
            AppSettingsSeeder::class,
            SiteSectionsSeeder::class,
            SetupKycSeeder::class,
            FreshExtensionSeeder::class,
            AdminHasRoleSeeder::class,
            
            SetupPageSeeder::class,
            LanguageSeeder::class,
            UsefulLinkSeeder::class,
            PaymentGatewaySeeder::class,
            AnnouncementCategorySeeder::class,
            AnnouncementSeeder::class,

            MerchantConfigurationSeeder::class,
            
            SystemMaintenanceSeeder::class,
            LiveExchangeRateSeeder::class,

        ]);
    }
}
