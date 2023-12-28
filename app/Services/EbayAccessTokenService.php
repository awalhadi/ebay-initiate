<?php
// app/Services/EbayAccessTokenService.php
namespace App\Services;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;
use App\Contracts\AccessTokenServiceInterface;

class EbayAccessTokenService implements AccessTokenServiceInterface
{
  // Properties declaration
  protected string $configPrefix;
  protected Client $httpClient;
  protected bool $isProductionMode = false;
  protected string $apiUrl;
  protected string $authUrl;
  protected string $clientId;
  protected string $clientSecret;
  protected string $scopes;
  protected string $redirectUri;

  public function __construct()
  {
    // Determine the configuration prefix based on the environment
    $this->configPrefix = config('ebay.environment') ? 'ebay.production' : 'ebay.sandbox';

    // Assign configuration values to class properties
    $this->httpClient = new Client();
    $this->apiUrl = config("{$this->configPrefix}.api_url");
    $this->authUrl = config("{$this->configPrefix}.auth_url");
    $this->redirectUri = config("{$this->configPrefix}.redirect_uri");
    $this->clientId = config("{$this->configPrefix}.client_id");
    $this->clientSecret = config("{$this->configPrefix}.client_secret");
    $this->scopes = config("{$this->configPrefix}.scopes");
  }

  // Method to generate connection URL
  public function getConnectionUrl()
  {
    // Construct and return the connection URL with necessary parameters
    $scopes = urlencode($this->scopes);
    return "{$this->authUrl}/oauth2/authorize?client_id={$this->clientId}&response_type=code&redirect_uri={$this->redirectUri}&scope={$scopes}&prompt=login";
  }



  // Method to initiate user consent token retrieval
  public function prepareForGetUserConsentToken(): \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|null
  {
    // Redirect the user to the eBay consent page to obtain the token
    try {
      return redirect()->away($this->getConnectionUrl());
    } catch (\Exception $ex) {
      return logger($ex);
    }
  }

  // Method to obtain user consent token and store it
  public function getUserConsentToken(Request $request): ?string
  {
    // Store the consent token retrieved from the user's request
    $consentToken = $request->code;
    Config::set("{$this->configPrefix}.consent_token", $consentToken);

    $userAccessToken = $this->getUserAccessToken($consentToken);

    // Retrieve and return the access token
    return $this->getAccessToken();
  }




  // get access token
  public function getAccessToken(): ?string
  {

    // Retrieve necessary token information from configuration
    $refreshTokenCreatedAt = config("{$this->configPrefix}.refresh_token_created_at");
    $refreshTokenExpiresIn = config("{$this->configPrefix}.refresh_token_expires_in");

    $accessToken = '';

    // Check expire
    $token_expire_time = Carbon::parse($refreshTokenCreatedAt)->addSeconds($refreshTokenExpiresIn);

    // Check if the access token has expired or needs refreshing
    if (!$refreshTokenCreatedAt || $token_expire_time <= now()) {
      $accessToken = $this->getTokenByRefreshToken(config("{$this->configPrefix}.refresh_token"));
    } else if (!$accessToken) {

      $accessToken = config("{$this->configPrefix}.access_token");
    }

    return $accessToken;
  }


  // get auth user access token
  public function getUserAccessToken($consentToken)
  {
    try {
      $refreshTokenCreatedAt = config("{$this->configPrefix}.refresh_token_created_at");
      $refreshTokenExpiresIn = config("{$this->configPrefix}.refresh_token_expires_in");

      // Calculate the expiration time
      $expirationTime = strtotime($refreshTokenCreatedAt) + $refreshTokenExpiresIn;
      // Check if the token has expired
      if (time() >= $expirationTime) {
        // Token has expired
        $response = $this->httpClient->request('POST', "{$this->apiUrl}/identity/v1/oauth2/token", [
          'headers'  => [
            'content-type' => 'application/x-www-form-urlencoded',
            'Authorization' => $this->getBasicAuthorizationHeader()
          ],
          'form_params' => [
            'grant_type' => 'authorization_code',
            'code' => $consentToken,
            'redirect_uri' => $this->redirectUri,
          ]
        ]);

        if ($response) {
          $data = json_decode($response->getBody()->getContents());
          Log::info(json_encode($data));
          // handle other process for access token 
          $this->handleUserAccessToken($data);

          return $data;
        }
        return false;
      } else {
        return;
      }
    } catch (\Exception $ex) {
      return logger($ex);
    }
  }



  // store and update user access token
  public function handleUserAccessToken($userAccessToken)
  {
    Config::set("{$this->configPrefix}.refresh_token", $userAccessToken->refresh_token ?? '');

    Config::set("{$this->configPrefix}.refresh_token_expires_in", $userAccessToken->refresh_token_expires_in ?? '');

    Config::set("{$this->configPrefix}.refresh_token_created_at", Carbon::now());

    Config::set("{$this->configPrefix}.user_access_token", $userAccessToken->refresh_token ?? '');
  }


  public function getBasicAuthorizationHeader(): ?string
  {
    try {
      return 'Basic ' . base64_encode($this->clientId . ":" . $this->clientSecret);
    } catch (\Exception $ex) {
      return logger($ex);
    }
  }


  // ACCESS TOKEN SYSTEM SECTION
  public function getTokenByRefreshToken($refreshToken = '')
  {
    try {
      $response = $this->httpClient->request('POST', "{$this->apiUrl}/identity/v1/oauth2/token", [
        'headers'  => [
          'content-type' => 'application/x-www-form-urlencoded',
          'Authorization' => $this->getBasicAuthorizationHeader()
        ],
        'form_params' => [
          'grant_type' => 'refresh_token',
          'refresh_token' => $refreshToken
        ]
      ]);

      if ($response) {
        $accessToken = optional(json_decode($response->getBody()->getContents()))->access_token;
        if ($accessToken) {
          Config::set("{$this->configPrefix}.refresh_token_created_at", Carbon::now());
          Config::set("{$this->configPrefix}.access_token", $accessToken);
        }
        return $accessToken;
      }

      return false;
    } catch (\Exception $ex) {
      return logger($ex);
    }
  }
}
