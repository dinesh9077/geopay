<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use App\Models\Setting;
use Auth, Config;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
	{   
		// Check if the 'settings' table exists before querying
		if (Schema::hasTable('settings')) {
			// Fetch settings and map `name` to `value`
			$settings = Setting::all()->pluck('value', 'name')->toArray(); 

			// Dynamically add settings to the configuration
			foreach ($settings as $key => $value) {
				config()->set('setting.' . $key, $value);
			} 
			
			// Update mail configuration dynamically
			Config::set('mail.mailers.smtp.host', $settings['mail_host'] ?? env('MAIL_HOST'));
			Config::set('mail.mailers.smtp.port', $settings['mail_port'] ?? env('MAIL_PORT'));
			Config::set('mail.mailers.smtp.username', $settings['mail_username'] ?? env('MAIL_USERNAME'));
			Config::set('mail.mailers.smtp.password', $settings['mail_password'] ?? env('MAIL_PASSWORD'));
			Config::set('mail.mailers.smtp.encryption', $settings['mail_encryption'] ?? env('MAIL_ENCRYPTION'));
			Config::set('mail.from.address', $settings['mail_from_address'] ?? env('MAIL_FROM_ADDRESS'));
			Config::set('mail.from.name', $settings['mail_from_name'] ?? env('MAIL_FROM_NAME'));
		}
		
		$viewShares = [
			'cryptoKey' => env('ENCRYPTION_SECRET_KEY',''), 
		];
		
		foreach ($viewShares as $key => $viewShare) {
			View::share($key, $viewShare);
		}
		
		// Ensure schema column length is set
		Schema::defaultStringLength(191);
	} 
}
