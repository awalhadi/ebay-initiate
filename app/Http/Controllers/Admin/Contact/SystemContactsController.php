<?php

namespace App\Http\Controllers\Admin\Contact;

use App\DataTables\Admin\ContactDataTable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemContactRequest;
use App\Repositories\Country\CountryRepositoryInterface;
use App\Repositories\SystemContact\SystemContactRepositoryInterface;

class SystemContactsController extends Controller
{
    protected $countryRepository;
    protected $systemContactRepository;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        CountryRepositoryInterface $countryRepository,
        SystemContactRepositoryInterface $systemContactRepository
    ) {
        $this->countryRepository = $countryRepository;
        $this->systemContactRepository = $systemContactRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ContactDataTable $dataTable)
    {
        set_page_meta('Contact List');
        return $dataTable->render('admin.contacts.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = $this->countryRepository->get();

        set_page_meta('Add Contact');
        return view('admin.contacts.create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SystemContactRequest $request)
    {
        $data = $request->validated();

        if ($this->systemContactRepository->storeOrUpdate($data)) {
            flash('Contact added successfully')->success();
        } else {
            flash('Contact add failed')->error();
        }
        return redirect()->route('admin.contacts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $contact = $this->systemContactRepository->get($id, ['country', 'state', 'city']);

        set_page_meta('Show Contact');
        return view('admin.contacts.show', compact('contact'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $contact = $this->systemContactRepository->get($id);
        $countries = $this->countryRepository->get();

        set_page_meta('Edit Contact');
        return view('admin.contacts.edit', compact('contact', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SystemContactRequest $request, $id)
    {
        $data = $request->validated();

        if ($this->systemContactRepository->storeOrUpdate($data, $id)) {
            flash('Contact updated successfully')->success();
        } else {
            flash('Contact update failed')->error();
        }
        return redirect()->route('admin.contacts.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->systemContactRepository->delete($id)) {
            flash('Contact deleted successfully')->success();
        } else {
            flash('Contact delete failed')->error();
        }
        return redirect()->route('admin.contacts.index');
    }
}
