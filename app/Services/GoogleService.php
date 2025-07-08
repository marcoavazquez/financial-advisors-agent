<?php

namespace App\Services;

use App\Models\User;
use Google_Client;

class GoogleService
{

  protected $client;

  public function __construct(User $user)
  {
    $googleAuthToken = $user->google_auth_token;
    $googleRefreshToken = $user->google_refresh_token;
    $this->client = new Google_Client();
    $this->client->setAccessToken($googleAuthToken);

    if ($this->client->isAccessTokenExpired() && $googleRefreshToken) {
      $this->client->fetchAccessTokenWithRefreshToken($googleRefreshToken);
      $newToken = $this->client->getAccessToken();
      $user->google_auth_token = $newToken['access_token'];
      $user->google_refresh_token = $newToken['refresh_token'];
      $user->save();
    }
  }

  public function getClient(): Google_Client
  {
    return $this->client;
  }
}