<?php

namespace App\Http\Controllers\Client\Faq;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\FaqCategory\FaqCategoryRepositoryInterface;

class FaqsController extends Controller
{
    protected $faqCategoryReporitory;

    public function __construct(
        FaqCategoryRepositoryInterface $faqCategoryReporitory
    ) {
        $this->faqCategoryReporitory = $faqCategoryReporitory;

        // SAAS user package wise access manage
        $this->middleware('permission:' . saasPermission('FAQ'));
    }


    public function index()
    {
        $faq_categories = $this->faqCategoryReporitory->get(null, ['faqs']);

        set_page_meta('FAQ');
        return view('client.faqs.index', compact('faq_categories'));
    }
}
