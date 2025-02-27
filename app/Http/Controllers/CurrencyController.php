<?php

namespace App\Http\Controllers;

use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CurrencyController extends Controller
{
    protected $currencyService;
    

    public function show(){
        return view('converter');
    }

    public function index(){
        $apiKey = env('EXCHANGERATE_API_KEY');
        $response = Http::get("http://api.currencylayer.com/list?access_key={$apiKey}");
        $currencies = $response->json()['currencies'] ?? [];

        return view('currency_converter', ['currencies' => $currencies]);
    }

    public function __construct(CurrencyService $currencyService){
        $this->currencyService = $currencyService;
    }

    public function convert(Request $request){
        // Validate inputs
        $request->validate([
            'from' => 'required|string|max:3',
            'to' => 'required|string|max:3',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date|before_or_equal:today',
        ]);

        $from = strtoupper($request->input('from'));
        $to = strtoupper($request->input('to'));
        $amount = floatval($request->input('amount'));
        $date = $request->input('date');

        // Fetch converted amount from Currencylayer /convert endpoint
        $convertedAmount = $this->getConvertedAmount($from, $to, $amount, $date);

        if ($convertedAmount === null) {
            return back()->withErrors(['api' => 'Unable to convert currency. Please check your inputs or try again later.'])
                         ->withInput(); // Keep form inputs on error
        }

        // Return results to view
        return view('converter', compact('amount', 'from', 'to', 'convertedAmount'));
    }

    private function getConvertedAmount($from, $to, $amount, $date)
    {
        $apiKey = config('services.exchangerate.key');
        $today = date('Y-m-d');

        // Determine endpoint (latest or historical)
        $endpoint = $date === $today ? 'latest' : "historical/{$date}";
        
        // Log API request details
        Log::info("API Key: " . $apiKey);

        if (empty($apiKey)) {
            Log::error("API Key is missing or invalid in config/services.exchangerate.key");
            return null;
        }

        // Define a single base currency (USD) to fetch all rates
        $baseCurrency = 'USD';
        $cacheKey = "exchange_rates_{$endpoint}_{$baseCurrency}";
        Log::info("Cache Key: " . $cacheKey);
        
        // Fetch and cache rates for the base currency (USD)
        $rates = Cache::remember($cacheKey, 3600, function () use ($apiKey, $endpoint, $baseCurrency) {
            $url = "https://v6.exchangerate-api.com/v6/{$apiKey}/{$endpoint}/{$baseCurrency}";
            Log::info("Request URL: " . $url);

            try {
                $response = Http::get($url);
                Log::info("Fetching rates for {$endpoint} with base {$baseCurrency}: " . json_encode($response->json()));

                if ($response->successful()) {
                    $data = $response->json();
                    if ($data['result'] === 'success' && isset($data['conversion_rates'])) {
                        return $data['conversion_rates'];
                    }
                }

                Log::error("Failed to fetch rates: " . json_encode($response->json()));
                return null;
            } catch (\Exception $e) {
                Log::error("Error fetching rates: " . $e->getMessage());
                return null;
            }
        });

        if ($rates === null) {
            Log::error("No rates available from cache or API for base {$baseCurrency}");
            return null;
        }
    
        // Log the rates being used for debugging
        Log::info("Rates for base {$baseCurrency}: " . json_encode($rates));
        Log::info("Converting from {$from} to {$to} with amount {$amount}");
    
        // Ensure the base currency rate is 1
        if (!isset($rates[$baseCurrency]) || $rates[$baseCurrency] !== 1) {
            Log::error("Base currency {$baseCurrency} rate is not 1: " . json_encode($rates));
            return null;
        }
    
        // Check if the from and to currencies exist in the rates
        if (!isset($rates[$from])) {
            Log::error("Currency not found in rates: From={$from}");
            return null;
        }
        if (!isset($rates[$to])) {
            Log::error("Currency not found in rates: To={$to}");
            return null;
        }
    
        // Convert via the base currency (USD)
        // 1. Convert from $from to USD: amount / rateFrom (USD/from)
        // 2. Convert USD to $to: amount * rateTo (USD/to)
        $rateFromToUSD = $rates[$from]; // Rate of $from relative to USD (e.g., USD/USD = 1, NGN/USD = 1/1500)
        $rateUSDToTo = $rates[$to];     // Rate of $to relative to USD (e.g., USD/NGN = 1500)
    
        // Correct conversion: (amount / rateFromToUSD) * rateUSDToTo
        $convertedAmount = ($amount / $rateFromToUSD) * $rateUSDToTo;
        Log::info("Calculated converted amount: {$convertedAmount} for {$amount} {$from} to {$to}");
    
        return round($convertedAmount, 2);
    }

    // New method to format currency with symbols and commas
    public function formatCurrency($amount, $currency)
    {
        $symbols = [
            'USD' => '$',
            'GBP' => '£',
            'EUR' => '€',
            'CAD' => '$',
            'JPY' => '¥',
            'NGN' => '₦',
            'GHS' => '₵',
            'ZAR' => 'R',
            'AUD' => '$',
            'CHF' => 'Fr',
            'CNY' => '¥'
        ];

        $symbol = $symbols[$currency] ?? '';
        $formattedAmount = number_format($amount, 2);
        return $symbol . $formattedAmount . ' ' . $currency;
    }

}