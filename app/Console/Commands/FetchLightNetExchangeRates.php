<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LiveExchangeRate;
use App\Models\LightnetCountry;
use Illuminate\Support\Facades\DB;
use App\Services\LiquidNetService;
use Log;
class FetchLightNetExchangeRates extends Command
{
    protected $signature = 'fetch:lightnet-exchange-rates'; // Command name
    protected $description = 'Fetch live exchange rates from the lightnet service';

   /**
	 * TransactionService instance.
	*/
	protected $liquidNetService;

	/**
	 * Create a new command instance.
	 */
	public function __construct(LiquidNetService $liquidNetService)
	{
		parent::__construct();
		$this->liquidNetService = $liquidNetService;
	} 

    public function handle()
    {
		//Log::info('Fetch live exchange rates from the lightnet service => '.now());
        $channel = 'lightnet';  // Replace this with dynamic channel or from the request

        try {
            DB::beginTransaction();

            $lightnetCountries = LightnetCountry::where('service_name', $channel)->get();
            $currentDate = now()->toDateString();

            foreach ($lightnetCountries as $lightnetCountry) {
                $response = $this->liquidNetService->getRateHistory(
                    $lightnetCountry->data,
                    $lightnetCountry->value,
                    $currentDate
                );
			 
                // Validate response
                if(!$response['success']) { 
                    continue;
                }

                if(isset($response['response']['code']) && $response['response']['code'] != 0) {
                    continue; 
                }

                $rateHistory = $response['response']['rateHistory'][0] ?? null;

                if (!$rateHistory) {
                    continue; // Skip if no rate history is available
                }

                $rate = $rateHistory['rate'] ?? 0;
                $updatedDate = $rateHistory['updatedDate'] ?? $currentDate;
                $countryName = $lightnetCountry->label;
                $currency = $lightnetCountry->value;

                // Fetch existing record or default values
                $liveExchangeRate = LiveExchangeRate::where([
                    'channel' => $channel,
                    'currency' => $currency,
                ])->first();

                $markdownType = $liveExchangeRate->markdown_type ?? 'flat';
                $markdownTypeCharge = $liveExchangeRate->markdown_charge ?? 0;

                // Calculate markdown charge and rate
                $markdownCharge = $markdownType === "flat"
                    ? max($markdownTypeCharge, 0)
                    : max(($rate * $markdownTypeCharge / 100), 0);

                $markdownRate = $rate - $markdownCharge;

                // Upsert the record
                LiveExchangeRate::updateOrCreate(
                    [
                        'channel' => $channel,
                        'currency' => $currency,
                    ],
                    [
                        'country_name' => $countryName,
                        'markdown_rate' => $markdownRate,
                        'aggregator_rate' => $rate,
                        'markdown_type' => $markdownType,
                        'markdown_charge' => $markdownTypeCharge,
                        'status' => 1,
                        'updated_at' => $updatedDate,
                    ]
                );
            } 
            DB::commit();
            $this->info('Live exchange rates fetched successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Failed to fetch rates. Error: ' . $e->getMessage());
        }
    }
}
