<?php
namespace App\Services;

use GuzzleHttp\Client;


class EbayBaseService extends EbayAccessTokenService
{
  protected Client $httpClient;
    
    public function __construct()
    {
        parent::__construct(); // Call the parent constructor
        
        // Initialize the HttpClient here or in a method.
        $this->httpClient = new Client([
            'base_uri' => $this->apiUrl . '/',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
            ]
        ]);
    }
  public function getHttpClient()
    {
      return $this->httpClient;
    }
}