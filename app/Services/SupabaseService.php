<?php

namespace App\Services;

use GuzzleHttp\Client;

class SupabaseService
{
    protected $client;
    protected $url;
    protected $key;

    public function __construct()
    {
        $this->client = new Client();

        $this->url = env('SUPABASE_URL');

        $this->key = env('SUPABASE_KEY');
    }

    // ===============================
    // REGISTER
    // ===============================
    public function signUp($email, $password)
    {
        $response = $this->client->post(
            "{$this->url}/auth/v1/signup",
            [
                'json' => [
                    'email' => $email,
                    'password' => $password,
                ],

                'headers' => [
                    'apikey' => $this->key,
                    'Authorization' => 'Bearer ' . $this->key,
                    'Content-Type' => 'application/json'
                ]
            ]
        );

        return json_decode(
            $response->getBody(),
            true
        );
    }
}
