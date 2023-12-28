<?php

namespace App\Http\Controllers\Admin\Issue;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\Admin\IssueDataTable;
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
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IssueDataTable $dataTable)
    {
        set_page_meta('Issues List');
        return $dataTable->render('admin.issues.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        return view('admin.issues.show', compact('issue'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $data = $this->validate($request, [
            'status' => 'required',
            'notes' => 'nullable|max:200'
        ]);

        if ($this->issueRepository->systemStoreOrUpdate($data, $id)) {
            flash('Issue update successfully')->success();
        } else {
            flash('Issue update fail')->error();
        }

        return redirect()->route('admin.issues.index');
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

        return redirect()->route('admin.issues.index');
    }
}
