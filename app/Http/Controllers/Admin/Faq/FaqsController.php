<?php

namespace App\Http\Controllers\Admin\Faq;

use App\DataTables\Admin\FaqDataTable;
use Illuminate\Http\Request;
use App\Http\Requests\FaqRequest;
use App\Http\Controllers\Controller;
use App\Models\FaqImage;
use App\Repositories\Faq\FaqRepositoryInterface;
use App\Repositories\FaqCategory\FaqCategoryRepositoryInterface;

class FaqsController extends Controller
{
    protected $faqCategoryReporitory;
    protected $faqReporitory;


    public function __construct(
        FaqCategoryRepositoryInterface $faqCategoryReporitory,
        FaqRepositoryInterface $faqReporitory
    ) {
        $this->faqCategoryReporitory = $faqCategoryReporitory;
        $this->faqReporitory = $faqReporitory;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FaqDataTable $dataTable)
    {
        set_page_meta('FAQ List');
        return $dataTable->render('admin.faqs.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = $this->faqCategoryReporitory->get();

        set_page_meta('Add FAQ');
        return view('admin.faqs.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FaqRequest $request)
    {
        $data = $request->validated();
        unset($data['category']);
        $data['faq_category_id'] = $request->category;

        if ($this->faqReporitory->storeOrUpdate($data)) {
            flash('Faq created successfully')->success();
        } else {
            flash('Faq created fail')->error();
        }

        return redirect()->route('admin.faqs.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $faq = $this->faqReporitory->get($id, 'images');

        set_page_meta('Show FAQ');
        return view('admin.faqs.show', compact('faq'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $categories = $this->faqCategoryReporitory->get();
        $faq = $this->faqReporitory->get($id);

        set_page_meta('Edit FAQ');
        return view('admin.faqs.edit', compact('categories', 'faq'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FaqRequest $request, $id)
    {
        $data = $request->validated();
        unset($data['category']);
        $data['faq_category_id'] = $request->category;

        if ($this->faqReporitory->storeOrUpdate($data, $id)) {
            flash('Faq updated successfully')->success();
        } else {
            flash('Faq updated fail')->error();
        }

        return redirect()->route('admin.faqs.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->faqReporitory->delete($id)) {
            flash('Faq deleted successfully')->success();
        } else {
            flash('Faq deleted fail')->error();
        }

        return redirect()->route('admin.faqs.index');
    }

    public function deleteImage($id)
    {
        $image = FaqImage::findOrFail($id);
        $image->delete();

        flash('Faq image deleted successfully')->success();

        return redirect()->back();
    }
}
