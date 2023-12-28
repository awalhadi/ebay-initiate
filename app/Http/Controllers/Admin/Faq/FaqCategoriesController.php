<?php

namespace App\Http\Controllers\Admin\Faq;

use App\DataTables\Admin\FaqCategoryDataTable;
use App\Http\Controllers\Controller;
use App\Repositories\FaqCategory\FaqCategoryRepositoryInterface;
use Illuminate\Http\Request;

class FaqCategoriesController extends Controller
{
    protected $faqCategoryReporitory;


    public function __construct(FaqCategoryRepositoryInterface $faqCategoryReporitory)
    {
        $this->faqCategoryReporitory = $faqCategoryReporitory;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FaqCategoryDataTable $dataTable)
    {
        set_page_meta('FAQ Category');
        return $dataTable->render('admin.faq_categories.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        set_page_meta('Add FAQ Category');
        return view('admin.faq_categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $data = $this->validate($request, [
            'name' => 'required|max:200|unique:faq_categories'
        ]);

        if ($this->faqCategoryReporitory->storeOrUpdate($data)) {
            flash('Faq category added successfully')->success();
        } else {
            flash('Faq category added fail')->error();
        }

        return redirect()->route('admin.faq-categories.index');
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
        $category = $this->faqCategoryReporitory->get($id);

        set_page_meta('Edit FAQ Category');
        return view('admin.faq_categories.edit', compact('category'));
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
            'name' => 'required|max:200|unique:faq_categories,name,' . $id
        ]);


        if ($this->faqCategoryReporitory->storeOrUpdate($data, $id)) {
            flash('Faq category updated successfully')->success();
        } else {
            flash('Faq category updated fail')->error();
        }

        return redirect()->route('admin.faq-categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->faqCategoryReporitory->delete($id)) {
            flash('Faq category deleted successfully')->success();
        } else {
            flash('Faq category deleted fail')->error();
        }

        return redirect()->route('admin.faq-categories.index');
    }
}
