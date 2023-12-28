<?php

namespace App\Http\Controllers\Client\Expenses;

use PDF;
use Excel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Rules\OneDataInArray;
use App\Http\Controllers\Controller;
use App\DataTables\ExpensesDataTable;
use App\Exports\ExpensesReportExport;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Expenses\ExpensesRepositoryInterface;
use App\Repositories\ExpensesCategory\ExpensesCategoryRepositoryInterface;

class ExpensesController extends Controller
{
    protected $expensesCategoryRepository;
    protected $expensesRepository;

    public function __construct(
        ExpensesCategoryRepositoryInterface $expensesCategoryRepository,
        ExpensesRepositoryInterface $expensesRepository
    ) {
        $this->expensesCategoryRepository = $expensesCategoryRepository;
        $this->expensesRepository = $expensesRepository;

        // SAAS user package wise access manage
        $this->middleware('permission:' . saasPermission('Expenses') . '|' .  saasPermission('Expenses_Report'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, ExpensesDataTable $dataTable)
    {
        $total = 0;
        $data = [];
        $report_range = '';
        $start = $request->from_date;
        $end = $request->to_date;

        if ($start && $end) {
            $report_range = $start . ' - ' . $end;
            $data = $this->expensesRepository->filterByDateRange($start, $end, ['category']);
        } else {
            $report_range = 'All Time';
            $data = $this->expensesRepository->get(null, ['category']);
        }


        // Calculate total
        if ($data instanceof Collection) {
            $total = $data->sum('total');
        }


        // Month graph
        $graph_data = $this->expensesRepository->monthGraph($start, $end, ['category']);

        set_page_meta('Expenses List');
        return $dataTable->render('client.expenses.index', compact('graph_data', 'report_range', 'total'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = $this->expensesCategoryRepository->get();

        set_page_meta('Add Expenses');
        return view('client.expenses.create', compact('categories'));
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
            'category' => ['required', 'numeric'],
            'title' => ['required', 'max:200'],
            'date' => ['required', 'max:200'],
            'data' => ['required', 'array', new OneDataInArray],
            'notes' => ['nullable', 'max:200'],
            'files.*' => ['nullable']
        ]);

        $data['category_id'] = $request->category;


        if ($this->expensesRepository->storeOrUpdate($data)) {
            flash('Expenses created successfully')->success();
        } else {
            flash('Expenses created fail')->error();
        }

        return redirect()->route('client.expenses.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $expenses = $this->expensesRepository->get($id, ['category', 'items', 'files']);

        set_page_meta('Expenses Details');
        return view('client.expenses.show', compact('expenses'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $expenses = $this->expensesRepository->get($id, ['category', 'items']);
        $categories = $this->expensesCategoryRepository->get();

        set_page_meta('Edit Expenses');
        return view('client.expenses.edit', compact('expenses', 'categories'));
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
            'category' => ['required', 'numeric'],
            'title' => ['required', 'max:200'],
            'date' => ['required', 'max:200'],
            'data' => ['required', 'array', new OneDataInArray],
            'notes' => ['nullable', 'max:200'],
            'files.*' => ['nullable']
        ]);

        $data['category_id'] = $request->category;


        if ($this->expensesRepository->storeOrUpdate($data, $id)) {
            flash('Expenses update successfully')->success();
        } else {
            flash('Expenses update fail')->error();
        }

        return redirect()->route('client.expenses.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->expensesRepository->delete($id)) {
            flash('Expenses deleted successfully')->success();
        } else {
            flash('Expenses deleted fail')->error();
        }

        return redirect()->route('client.expenses.index');
    }

    public function deleteFile($file_id)
    {
        if ($this->expensesRepository->deleteFile($file_id)) {
            flash('File deleted successfully')->success();
        } else {
            flash('File deleted fail')->error();
        }

        return redirect()->back();
    }

    public function generateReport(Request $request)
    {
        // Check saas user permission
        checkPermission(saasPermission('Expenses_Report'));


        $total = 0;
        $data = [];
        $report_range = '';
        $start = $request->from_date;
        $end = $request->to_date;

        if ($start && $end) {
            $report_range = $start . ' - ' . $end;
            $data = $this->expensesRepository->filterByDateRange($start, $end, ['category']);
        }

        if (isset($request->q) && $request->q = 'all-time') {
            $report_range = 'All Time';
            $data = $this->expensesRepository->get(null, ['category']);
        }

        // Calculate total
        if ($data instanceof Collection) {
            $total = $data->sum('total');
        }

        set_page_meta('Expenses Report');
        return view('client.expenses.report', compact('data', 'report_range', 'total'));
    }

    public function exportReport(Request $request)
    {
        // Check saas user permission
        checkPermission(saasPermission('Expenses_Report'));

        $total = 0;
        $data = [];
        $report_range = '';
        $start = $request->from_date;
        $end = $request->to_date;
        $type = $request->type;

        if ($start && $end) {
            $report_range = $start . ' - ' . $end;
            $data = $this->expensesRepository->filterByDateRange($start, $end, ['category']);
        } else {
            $report_range = 'All Time';
            $data = $this->expensesRepository->get(null, ['category']);
        }

        // Calculate total
        if ($data instanceof Collection) {
            $total = $data->sum('total');
        }


        // return view('client.expenses.pdf.report', compact('data', 'report_range'));

        $name = 'Expenses-report-' . Str::slug($report_range);
        if ($type == 'pdf') {
            $pdf = PDF::loadView('client.expenses.pdf.report', ['data' => $data, 'report_range' => $report_range, 'total' => $total]);
            return $pdf->download($name . '.pdf');
        } else if ($type == 'csv') {
            return Excel::download(new ExpensesReportExport($data), $name . '.csv');
        } else if ($type == 'excel') {
            return Excel::download(new ExpensesReportExport($data), $name . '.xlsx');
        }
    }





    // HANDLE AJAX
    //     public function apiGet(Request $request)
    //     {
    //         $data = $this->expensesRepository->customFilter($request, ['category']);

    //         return Datatables::of($data)
    //             ->addColumn('action', function ($item) {
    //                 $buttons = '';
    //                 $buttons .= '<a class="dropdown-item" href="' . route('client.expenses.show', $item->id) . '" title="Show"><i class="mdi mdi mdi-desktop-mac"></i> Show </a>';
    //                 $buttons .= '<a class="dropdown-item" href="' . route('client.expenses.edit', $item->id) . '" title="Edit"><i class="mdi mdi-square-edit-outline"></i> Edit </a>';

    //                 $buttons .= '<form action="' . route('client.expenses.destroy', $item->id) . '"  id="delete-form-' . $item->id . '" method="post" style="">
    // <input type="hidden" name="_token" value="' . csrf_token() . '">
    // <input type="hidden" name="_method" value="DELETE">
    // <button class="dropdown-item text-danger" onclick="return makeDeleteRequest(event, ' . $item->id . ')"  type="submit" title="Delete"><i class="mdi mdi-trash-can-outline"></i> Delete</button></form>
    // ';

    //                 return '<div class="btn-group">
    // <a href="#" onclick="return false;" class="dropdown-toggle dropdown" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></a>
    // <div class="dropdown-menu">
    // ' . $buttons . '
    // </div>
    // </div>';
    //             })->editColumn('category.name', function ($item) {
    //                 return $item->category->name ?? '';
    //             })->editColumn('total', function ($item) {
    //                 return '$' . $item->total;
    //             })->editColumn('notes', function ($item) {
    //                 return Str::limit($item->notes, 50, '...');
    //             })->rawColumns(['action'])->addIndexColumn()
    //             ->make(true);
    //     }
}
