<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EbayService;
use App\Contracts\AccessTokenServiceInterface;

class WelcomeController extends Controller
{
    protected $accessTokenService;
    protected $ebayService;

    public function __construct(AccessTokenServiceInterface $accessTokenService)
    {
        $this->accessTokenService = $accessTokenService;
        $this->ebayService = new EbayService;
    }

    public function index()
    {
        // return $this->accessTokenService->prepareForGetUserConsentToken();
        $products = $this->ebayService->getProductList();
        dd($products);
        return view('welcome');
    }

    public function handleConsentToken(Request $request)
    {
        $access_token = $this->accessTokenService->getUserConsentToken($request);
        $data = [
            'status' => 'success',
            'access_token'=> $access_token
        ];
        return response()->json($data);
    }
}
