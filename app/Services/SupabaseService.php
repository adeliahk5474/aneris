<?php
// app/Services/SupabaseService.php
// Tambahkan 'verify' => false HANYA untuk local dev

namespace App\Services;

use GuzzleHttp\Client;

class SupabaseService
{
    protected Client $client;
    protected string $url;
    protected string $key;

    public function __construct()
    {
        $this->url = config('services.supabase.url');
        $this->key = config('services.supabase.key');

        $this->client = new Client([
            // Disable SSL verify hanya di local (app.env = local)
            // Di production JANGAN false — hapus baris ini
            'verify' => app()->environment('production'),
        ]);
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
                    'email'    => $email,
                    'password' => $password,
                ],
                'headers' => [
                    'apikey'        => $this->key,
                    'Authorization' => 'Bearer ' . $this->key,
                    'Content-Type'  => 'application/json',
                ],
            ]
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    // ===============================
    // LOGIN
    // ===============================
    public function signIn($email, $password)
    {
        $response = $this->client->post(
            "{$this->url}/auth/v1/token?grant_type=password",
            [
                'json' => [
                    'email'    => $email,
                    'password' => $password,
                ],
                'headers' => [
                    'apikey'        => $this->key,
                    'Authorization' => 'Bearer ' . $this->key,
                    'Content-Type'  => 'application/json',
                ],
            ]
        );

        return json_decode($response->getBody()->getContents(), true);
    }
}
