<?php

namespace App\Http\Controllers\Client\Invoice;

use Throwable;
use Illuminate\Http\Request;
use App\DataTables\InvoiceDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Repositories\Invoice\InvoiceRepositoryInterface;

class InvoicesController extends Controller
{
    protected $invoiceRepository;

    public function __construct(InvoiceRepositoryInterface $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;

        // SAAS user package wise access manage
        $this->middleware('permission:' . saasPermission('Invoice_Manage'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(InvoiceDataTable $dataTable)
    {
        set_page_meta('Invoices');
        return $dataTable->render('client.invoices.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $all_status = $this->invoiceRepository->getAllStatus();

        set_page_meta('Add Invoice');
        return view('client.invoices.create', compact('all_status'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InvoiceRequest $request)
    {
        $data = $request->validated();

        try {
            $this->invoiceRepository->storeOrUpdate($data);
            flash('Invocie created successfully')->success();

            return response()->json(['success' => true], 200);
        } catch (Throwable $th) {
            flash('Invocie create failed')->success();

            return response()->json(['success' => false], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $invoice = $this->invoiceRepository->get($id, ['items']);

        set_page_meta('Show Invoice');
        return view('client.invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $invoice = $this->invoiceRepository->get($id, ['items']);
        $all_status = $this->invoiceRepository->getAllStatus();

        set_page_meta('Edit Invoice');
        return view('client.invoices.edit', compact('invoice', 'all_status'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(InvoiceRequest $request, $id)
    {
        $data = $request->validated();

        try {
            $this->invoiceRepository->storeOrUpdate($data, $id);
            flash('Invocie updated successfully')->success();

            return response()->json(['success' => true], 200);
        } catch (Throwable $th) {
            flash('Invocie update failed')->success();

            return response()->json(['success' => false], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $invoice = $this->invoiceRepository->get($id, ['items']);

        if ($invoice->delete()) {
            flash('Invoice delete successfully')->success();
        } else {
            flash('Invoice delete fail')->error();
        }

        return redirect()->route('client.invoices.index');
    }

    public function download($id)
    {
        return $this->invoiceRepository->download($id);
    }

    public function addPayment(Request $request)
    {
        $data = $this->validate($request, [
            'invoice_id' => 'required|numeric',
            'date' => 'required',
            'payment_type' => 'required|max:50',
            'amount' => 'required|numeric',
            'notes' => 'nullable|max:200'
        ]);

        try {
            $this->invoiceRepository->addPayment($data);

            flash('Payment added successfully')->success();
            return redirect()->route('client.invoices.index');
        } catch (Throwable $th) {
            flash('Payment add failed')->error();
            return redirect()->route('client.invoices.index');
        }
    }

    public function getPayments($invoice_id)
    {
        return $this->invoiceRepository->getPayments($invoice_id);
    }

    public function deletePayment($id)
    {
        $this->invoiceRepository->deletePayment($id);

        flash('Payment deleted successfully')->success();
        return redirect()->route('client.invoices.index');
    }

    public function sendInvoice(Request $request)
    {
        $data = $this->validate($request, [
            'invoice_id' => 'required|numeric',
            'email' => 'required|email',
        ]);

        try {
            $this->invoiceRepository->sendInvoice($data);

            flash('Invoice send successfully')->success();
            return redirect()->route('client.invoices.index');
        } catch (Throwable $th) {
            flash('Invoice send failed')->error();
            return redirect()->route('client.invoices.index');
        }
    }
}
