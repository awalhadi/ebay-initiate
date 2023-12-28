<?php

namespace App\Http\Controllers\Client\Profile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Country\CountryRepositoryInterface;
use App\Repositories\Profile\ProfileRepositoryInterface;

class ProfilesController extends Controller
{
    protected $countryRepository;
    protected $profileRepository;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        CountryRepositoryInterface $countryRepository,
        ProfileRepositoryInterface $profileRepository
    ) {
        $this->countryRepository = $countryRepository;
        $this->profileRepository = $profileRepository;
    }

    /**
     * edit
     *
     * @return void
     */
    public function edit()
    {
        $countries = $this->countryRepository->get();
        $profile = $this->profileRepository->getByUser(Auth::id());

        set_page_meta('Profile Edit');
        return view('client.profiles.edit', compact('countries', 'profile'));
    }

    /**
     * update
     *
     * @param  mixed $request
     * @return void
     */
    public function update(ProfileRequest $request)
    {
        $data = $request->validated();

        if ($this->profileRepository->updateProfile($data)) {
            flash('Profile updated successfully')->success();
        } else {
            flash('Profile updated fail')->error();
        }

        return back();
    }
}
