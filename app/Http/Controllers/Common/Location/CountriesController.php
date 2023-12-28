<?php

namespace App\Http\Controllers\Common\Location;

use Illuminate\Http\Request;
use App\DataTables\CountryDataTable;
use App\Http\Controllers\Controller;
use App\Repositories\Country\CountryRepositoryInterface;

class CountriesController extends Controller
{
    protected $countryRpository;

    /**
     * __construct
     *
     * @param  mixed $countryRpository
     * @return void
     */
    public function __construct(CountryRepositoryInterface $countryRpository)
    {
        $this->countryRpository = $countryRpository;
    }

    /**
     * index
     *
     * @param  mixed $dataTable
     * @return void
     */
    public function index(CountryDataTable $dataTable)
    {
        set_page_meta('Country');
        return $dataTable->render('common.locations.countries.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        set_page_meta('Add Country');
        return view('common.locations.countries.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:2|max:191|unique:system_countries',
            'shortname' => 'required|min:1|max:50|unique:system_countries',
        ]);

        $data = [
            'name' => $request->name,
            'shortname' => $request->shortname,
        ];

        if ($this->countryRpository->storeOrUpdate($data)) {
            flash('Country added successfully')->success();
        } else {
            flash('Country added fail')->error();
        }

        return redirect()->route('common.locations.countries.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $country = $this->countryRpository->get($id);

        // Check value is empty or not
        if (!$country) {
            flash('No data found!')->error();
            return redirect()->route('common.locations.countries.index');
        }

        set_page_meta('Edit Country');
        return view('common.locations.countries.edit', compact('country'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'name' => 'required|min:2|max:191|unique:system_countries,name,' . $id,
            'shortname' => 'required|min:1|max:50|unique:system_countries,shortname,' . $id,
        ]);

        $data = [
            'name' => $request->name,
            'shortname' => $request->shortname,
        ];

        if ($this->countryRpository->storeOrUpdate($data, $id)) {
            flash('Country updated successfully')->success();
        } else {
            flash('Country updated fail')->error();
        }

        return redirect()->route('common.locations.countries.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->countryRpository->delete($id)) {
            flash('Country delete successfully')->success();
        } else {
            flash('Country delete fail')->error();
        }

        return redirect()->route('common.locations.countries.index');
    }
}
