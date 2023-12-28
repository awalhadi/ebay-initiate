<?php

namespace App\Http\Controllers\Admin\Subscription;

use App\Models\Permission;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Services\Paypal\PaypalService;
use App\DataTables\Admin\SubPackDataTable;
use App\Http\Requests\SubscriptionPackageRequest;
use App\Repositories\SubPack\SubPackRepositoryInterface;

/**
 * PackagesController
 */
class PackagesController extends Controller
{
    protected $subPackRepository;

    /**
     * __construct
     *
     * @param  mixed $subPackRepository
     * @return void
     */
    public function __construct(SubPackRepositoryInterface $subPackRepository)
    {
        $this->subPackRepository = $subPackRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SubPackDataTable $dataTable)
    {
        set_page_meta('Subscription Package');
        return $dataTable->render('admin.subscription.packages.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::where('permission_for', Permission::APP_USER_PERMISSION)->get();

        set_page_meta('Add Subscription Package');
        return view('admin.subscription.packages.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubscriptionPackageRequest $request)
    {
        $data = [
            'package_type' => $request->package_type,
            'package_title' => $request->package_title,
            'package_price' => $request->package_price,
            'package_features' => $request->package_features,
            'package_status' => $request->package_status,
            'package_price' => $request->package_price,
            'status' => $request->status,
        ];

        if ($this->subPackRepository->storeOrUpdate($data)) {
            flash('Package added successfully')->success();
        } else {
            flash('Package added fail')->error();
        }


        return redirect()->route('admin.subs-packages.index');
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
        $package = $this->subPackRepository->get($id);
        // TODO: make repository
        $permissions = Permission::where('permission_for', Permission::APP_USER_PERMISSION)->get();
        $role = Role::where('name', $package->package_title)->first();


        set_page_meta('Edit Subscription Package');
        return view('admin.subscription.packages.edit', compact('package', 'permissions', 'role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SubscriptionPackageRequest $request, $id)
    {
        $data = [
            // 'package_type' => $request->package_type,
            'package_title' => $request->package_title,
            'package_price' => $request->package_price,
            'package_features' => $request->package_features,
            'package_price' => $request->package_price,
            'status' => $request->status,
            'permissions' => $request->permissions,
            'update_only_permission' => $request->update_only_permission
        ];

        if ($this->subPackRepository->storeOrUpdate($data, $id)) {
            flash('Package updated successfully')->success();
        } else {
            flash('Package updated fail')->error();
        }

        return redirect()->route('admin.subs-packages.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->subPackRepository->delete($id)) {
            flash('Package delete successfully')->success();
        } else {
            flash('Package delete fail')->error();
        }

        return redirect()->route('admin.subs-packages.index');
    }
}
