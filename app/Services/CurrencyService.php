<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log; // Import Log facade

class CurrencyService
{
    protected $client;
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = config('services.exchangerate.api_key'); // Ensure your API key is stored correctly
        $this->baseUrl = 'https://api.apilayer.com/currency_data/live';
    }

    public function getExchangeRates($baseCurrency = 'USD')
    {
        return Cache::remember("exchange_rates_{$baseCurrency}", 86400, function () use ($baseCurrency) {
            try {
                $url = "{$this->baseUrl}?access_key={$this->apiKey}&source={$baseCurrency}"; // Correct URL formatting
                
                $response = $this->client->get($url);
                $data = json_decode($response->getBody(), true);

                // Check if API request was successful and data is valid
                if (isset($data['success']) && $data['success'] && isset($data['quotes'])) {
                    return $data['quotes']; // The API returns exchange rates inside "quotes"
                }

                throw new \Exception('Invalid API response: ' . json_encode($data));
            } catch (\Exception $e) {
                Log::error('Currency API Error: ' . $e->getMessage()); // Use Log facade
                return null;
            }
        });
    }
}
