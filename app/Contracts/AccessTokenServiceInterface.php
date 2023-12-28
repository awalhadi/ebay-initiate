<?php

namespace App\Contracts;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;

interface AccessTokenServiceInterface
{
    public function getBasicAuthorizationHeader(): ?string;
    public function prepareForGetUserConsentToken(): RedirectResponse|Response|null;
    public function getUserConsentToken(Request $request): ?string; // Adjusted return type
    public function getAccessToken(): ?string; // Adjusted return type
}