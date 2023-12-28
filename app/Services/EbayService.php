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
            dd($client);
            $response = $client->request('GET', 'featured_treasuries/listings');
            return json_decode($response->getBody()->getContents());
        // } catch (Exception $ex) {
        //     return logger($ex);
        // }
    }



    // category

    public function getDefaultCategoryTreeId($default_marketplace="EBAY_US")
    {
        // $this->business_id = $business_id;

        try {
            $client = $this->getHttpClient();
            $response = $client->request('GET', "commerce/taxonomy/v1/get_default_category_tree_id?marketplace_id={$default_marketplace}");
            return $results = json_decode($response->getBody()->getContents());
        } catch (Exception $ex) {
            return logger($ex);
        }
    }

    public function getCategoryTree($category_tree_id = 0)
    {
        try {
            $client = $this->getHttpClient();
            $response = $client->request('GET', "commerce/taxonomy/v1/category_tree/{$category_tree_id}");

            return $results = json_decode($response->getBody()->getContents());
        } catch (Exception $ex) {
            return logger($ex);
        }
    }
}
