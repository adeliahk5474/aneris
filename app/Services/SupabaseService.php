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
        $this->url = env('SUPABASE_URL'); // misal: https://your-project.supabase.co
        $this->key = env('SUPABASE_KEY'); // gunakan anon public key
    }

    public function signUp($email, $password, $redirectTo)
    {
        $response = $this->client->post("{$this->url}/auth/v1/signup", [
            'json' => [
                'email' => $email,
                'password' => $password,
                'options' => ['redirectTo' => $redirectTo]
            ],
            'headers' => [
                'apikey' => $this->key,
                'Authorization' => 'Bearer ' . $this->key,
                'Content-Type' => 'application/json'
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function signIn($email, $password)
    {
        $response = $this->client->post("{$this->url}/auth/v1/token?grant_type=password", [
            'json' => ['email' => $email, 'password' => $password],
            'headers' => [
                'apikey' => $this->key,
                'Authorization' => 'Bearer ' . $this->key,
                'Content-Type' => 'application/json'
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function verifyEmail($access_token)
    {
        $response = $this->client->get("{$this->url}/auth/v1/verify", [
            'query' => ['token' => $access_token],
            'headers' => [
                'apikey' => $this->key,
                'Authorization' => 'Bearer ' . $this->key,
                'Content-Type' => 'application/json'
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
