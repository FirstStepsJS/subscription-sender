<?php


namespace App\Services;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Dotmailer
{

    protected $apiUrl;
    protected $token;
    protected $testEmailAddress;

    public function __construct() {
        $this->apiUrl = config('services.dotmailer.base_url') .  '/api/dotmailer/';
        $this->token = config('services.dotmailer.token');
        $this->testEmailAddress = config('services.dotmailer.test_email_address');
    }

    public function sendCampaign($campaignName, $emailAddress, $params = []) {
        $client = new Client([
            'base_uri' => $this->apiUrl,
            'headers' => [
                'authorization' => 'Bearer ' . $this->token
            ]
        ]);

        if (env('APP_ENV') != 'production') {
            $emailAddress = $this->testEmailAddress;
        }

        if (!empty($params['count']) && $params['count'] > 0) { // Technically !empty() also checks >0
            Log::info("Sent email to " . $emailAddress . " because they have " . $params['count'] . " matched articles");
            $client->post(sprintf('campaign/%s/%s', $campaignName, $emailAddress), ['body' => json_encode($params)]);
        }
        else {
            Log::error("User $emailAddress does not have a matching article, post request not sent.");
        }
    }

}