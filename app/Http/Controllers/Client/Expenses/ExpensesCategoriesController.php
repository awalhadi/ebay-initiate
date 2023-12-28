<?php

namespace App\Http\Controllers\Client\Expenses;

use App\DataTables\ExpensesCategoryDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExpensesCategoryRequest;
use App\Repositories\ExpensesCategory\ExpensesCategoryRepositoryInterface;
use Illuminate\Http\Request;

class ExpensesCategoriesController extends Controller
{
    protected $expensesCategoryRepository;

    public function __construct(ExpensesCategoryRepositoryInterface $expensesCategoryRepository)
    {
        $this->expensesCategoryRepository = $expensesCategoryRepository;

        // SAAS user package wise access manage
        $this->middleware('permission:' . saasPermission('Expenses_Category'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ExpensesCategoryDataTable $dataTable)
    {
        set_page_meta('Expenses Categories');
        return $dataTable->render('client.expenses_categories.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        set_page_meta('Add Category');
        return view('client.expenses_categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ExpensesCategoryRequest $request)
    {
        $data = $request->validated();


        if ($this->expensesCategoryRepository->storeOrUpdate($data)) {
            flash('Expenses category added successfully')->success();
        } else {
            flash('Expenses category added fail')->error();
        }

        return redirect()->route('client.expenses-categories.index');
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
        $category = $this->expensesCategoryRepository->get($id);

        set_page_meta('Edit Category');
        return view('client.expenses_categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ExpensesCategoryRequest $request, $id)
    {
        $data = $request->validated();

        if ($this->expensesCategoryRepository->storeOrUpdate($data, $id)) {
            flash('Expenses category updated successfully')->success();
        } else {
            flash('Expenses category updated fail')->error();
        }

        return redirect()->route('client.expenses-categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->expensesCategoryRepository->delete($id)) {
            flash('Product category delete successfully')->success();
        } else {
            flash('Product category delete fail')->error();
        }

        return redirect()->route('client.expenses-categories.index');
    }
}
