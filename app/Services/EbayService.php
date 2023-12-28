<?php
namespace App\Services;

use App\Services\EbayBaseService;
use Exception;

class EbayService extends EbayBaseService
{
  // get product lists

  public function getProductList()
    {
        // try {
            $client = $this->getHttpClient();
            $response = $client->request('GET', 'featured_treasuries/listings');
            return json_decode($response->getBody()->getContents());
        // } catch (Exception $ex) {
        //     return logger($ex);
        // }
    }
}
