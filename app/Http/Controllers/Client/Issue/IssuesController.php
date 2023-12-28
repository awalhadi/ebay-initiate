<?php

namespace App\Http\Controllers\Client\Issue;

use Carbon\Carbon;
use App\DataTables\IssueDataTable;
use App\Http\Requests\IssueRequest;
use App\Http\Controllers\Controller;
use App\Repositories\Issue\IssueRepository;

class IssuesController extends Controller
{

    protected $issueRepository;
    /**
     * __construct
     *
     * @param  mixed $model
     * @return void
     */
    public function __construct(
        IssueRepository $issueRepository
    ) {
        $this->issueRepository = $issueRepository;

        // SAAS user package wise access manage
        $this->middleware('permission:' . saasPermission('Issues'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IssueDataTable $dataTable)
    {
        $latest_issues = $this->issueRepository->getLatest(6);

        set_page_meta('Issues List');
        return $dataTable->render('client.issues.index', compact('latest_issues'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        set_page_meta('Create Issue');
        return view('client.issues.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(IssueRequest $request)
    {
        $data = $request->validated();
        $data['date'] = Carbon::now();


        if ($this->issueRepository->storeOrUpdate($data)) {
            flash('Issue created successfully')->success();
        } else {
            flash('Issue created fail')->error();
        }

        return redirect()->route('client.issues.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $issue = $this->issueRepository->get($id);

        set_page_meta('Show Issue');
        return view('client.issues.show', compact('issue'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $issue = $this->issueRepository->get($id);

        set_page_meta('Edit Issue');
        return view('client.issues.edit', compact('issue'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(IssueRequest $request, $id)
    {
        $data = $request->validated();
        $data['date'] = Carbon::now();


        if ($this->issueRepository->storeOrUpdate($data, $id)) {
            flash('Issue update successfully')->success();
        } else {
            flash('Issue update fail')->error();
        }

        return redirect()->route('client.issues.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->issueRepository->delete($id)) {
            flash('Issue deleted successfully')->success();
        } else {
            flash('Issue deleted fail')->error();
        }

        return redirect()->route('client.issues.index');
    }
}
