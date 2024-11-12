<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
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
		
		$viewShares = [
			'cryptoKey' => env('ENCRYPTION_SECRET_KEY'),
			'metaClientId' => env('META_VERIFICATION_API_KEY'),
			'metaFlowId' => env('META_VERIFICATION_FLOW_ID'),
			'metaSecretKey' => env('META_VERIFICATION_SECRET'),
			'authUser' => Auth::user(),
		];

		foreach ($viewShares as $key => $viewShare) {
			View::share($key, $viewShare);
		}

		// Ensure schema column length is set
		Schema::defaultStringLength(191);
	}

}
