<?php

namespace App\Auth;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class ApiUserProvider implements UserProvider
{
    protected $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.api.url');
    }

    protected function getHeaders()
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    protected function hydrateUser(array $data)
    {
        return new User($data);
    }

    public function retrieveById($identifier)
    {
        $response = Http::withHeaders($this->getHeaders())
            ->get($this->apiUrl . '/users/' . $identifier);

        if ($response->successful()) {
            return $this->hydrateUser($response->json('data'));
        }

        return null;
    }

    public function retrieveByToken($identifier, $token)
    {
        return null;
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        // Not implemented
    }

    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials['email']) || empty($credentials['password'])) {
            return null;
        }

        $response = Http::withHeaders($this->getHeaders())
            ->post($this->apiUrl . '/auth/login', [
                'email' => $credentials['email'],
                'password' => $credentials['password'],
            ]);

        if ($response->successful()) {
            return $this->hydrateUser($response->json('data'));
        }

        return null;
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return $user !== null;
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false)
    {
        // Do nothing as the password hashing is handled by the API service
    }
}
