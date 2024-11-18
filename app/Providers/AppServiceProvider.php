<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use App\Models\Setting;
use Auth;
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
