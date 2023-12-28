<?php

namespace App\Http\Controllers\Common\Location;

use App\DataTables\StateDataTable;
use App\Models\SystemState;
use App\Http\Controllers\Controller;
use App\Repositories\Country\CountryRepositoryInterface;
use App\Repositories\State\StateRepositoryInterface;
use Illuminate\Http\Request;

/**
 * StatesController
 */
class StatesController extends Controller
{
    protected $stateRepository;
    protected $countryRpository;


    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        StateRepositoryInterface $stateRepository,
        CountryRepositoryInterface $countryRpository
    ) {
        $this->stateRepository = $stateRepository;
        $this->countryRpository = $countryRpository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(StateDataTable $dataTable)
    {
        set_page_meta('State');
        return $dataTable->render('common.locations.states.index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = $this->countryRpository->get();

        set_page_meta('Add State');
        return view('common.locations.states.create', compact('countries'));
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'country' => 'required|numeric',
            'name' => 'required|min:2|max:191',
        ]);

        $data = [
            'country_id' => $request->country,
            'name' => $request->name
        ];

        if ($this->stateRepository->storeOrUpdate($data)) {
            flash('State added successfully')->success();
        } else {
            flash('State added fail')->error();
        }

        return redirect()->route('common.locations.states.index');
    }


    public function edit($id)
    {
        $state = $this->stateRepository->get($id);

        $countries = $this->countryRpository->get();

        set_page_meta('Edit State');
        return view('common.locations.states.edit', compact('state', 'countries'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'country' => 'required|numeric',
            'name' => 'required|min:2|max:191',
        ]);

        $data = [
            'country_id' => $request->country,
            'name' => $request->name
        ];

        if ($this->stateRepository->storeOrUpdate($data, $id)) {
            flash('State updated successfully')->success();
        } else {
            flash('State updated fail')->error();
        }

        return redirect()->route('common.locations.states.index');
    }


    public function destroy($id)
    {
        if ($this->stateRepository->delete($id)) {
            flash('State delete successfully')->success();
        } else {
            flash('State delete fail')->error();
        }

        return redirect()->route('common.locations.states.index');
    }



    // HANDLE AJAX REQUEST

    public function getCountryWiseState(Request $request)
    {
        $states = SystemState::where('country_id', $request->id)->get();

        return response()->json([
            'data'  =>  $states
        ]);
    }
}
