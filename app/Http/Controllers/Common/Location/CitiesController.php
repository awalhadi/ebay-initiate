<?php

namespace App\Http\Controllers\Common\Location;

use App\Models\SystemCity;
use Illuminate\Http\Request;
use App\DataTables\CityDataTable;
use App\Http\Controllers\Controller;
use App\Repositories\City\CityRepositoryInterface;
use App\Repositories\Country\CountryRepositoryInterface;

/**
 * CitiesController
 */
class CitiesController extends Controller
{
    protected $cityRepository;
    protected $countryRpository;


    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        CityRepositoryInterface $cityRepository,
        CountryRepositoryInterface $countryRpository
    ) {
        $this->cityRepository = $cityRepository;
        $this->countryRpository = $countryRpository;
    }

    public function index(CityDataTable $dataTable)
    {
        set_page_meta('City');
        return $dataTable->render('common.locations.cities.index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = $this->countryRpository->get();

        set_page_meta('Add City');
        return view('common.locations.cities.create', compact('countries'));
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'state' => 'required|numeric',
            'name' => 'required|min:2|max:191',
        ]);

        $data = [
            'state_id' => $request->state,
            'name' => $request->name
        ];

        if ($this->cityRepository->storeOrUpdate($data)) {
            flash('City added successfully')->success();
        } else {
            flash('City added fail')->error();
        }

        return redirect()->route('common.locations.cities.index');
    }

    public function edit($id)
    {
        $city = $this->cityRepository->get($id);

        $countries = $this->countryRpository->get();

        set_page_meta('Edit City');
        return view('common.locations.cities.edit', compact('city', 'countries'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'state' => 'required|numeric',
            'name' => 'required|min:2|max:191',
        ]);

        $data = [
            'state_id' => $request->state,
            'name' => $request->name
        ];

        if ($this->cityRepository->storeOrUpdate($data, $id)) {
            flash('City updated successfully')->success();
        } else {
            flash('City updated fail')->error();
        }

        return redirect()->route('common.locations.cities.index');
    }


    public function destroy($id)
    {
        if ($this->cityRepository->delete($id)) {
            flash('City delete successfully')->success();
        } else {
            flash('City delete fail')->error();
        }

        return redirect()->route('common.locations.cities.index');
    }


    //...............HANDLE AJAX REQUEST

    public function getStateWiseCity(Request $request)
    {
        $cities = SystemCity::where('state_id', $request->id)->get();

        return response()->json([
            'data'  =>  $cities
        ]);
    }
}
