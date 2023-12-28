<?php

namespace App\Contracts;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;

interface AccessTokenServiceInterface
{
    // public function getApplicationAccessToken(): ?string;
    public function prepareForGetUserConsentToken(): RedirectResponse|Response|null;
    public function getUserConsentToken(Request $request): ?string; // Adjusted return type
}