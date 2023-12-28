<?php

namespace App\Http\Controllers\Client\Administration;

use App\DataTables\RoleDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Repositories\Role\RoleRepositoryInterface;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    protected $roleRepository;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        RoleRepositoryInterface $roleRepository
    ) {
        $this->roleRepository = $roleRepository;

        // SAAS user package wise access manage
        $this->middleware('permission:' . saasPermission('Roles'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(RoleDataTable $dataTable)
    {
        set_page_meta('Roles');
        return $dataTable->render('client.administration.roles.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = $this->roleRepository->getParentPermissions();

        set_page_meta('Create Role');
        return view('client.administration.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        $data = $request->validated();

        if ($this->roleRepository->storeOrUpdate($data)) {
            flash('Role created successfully')->success();
        } else {
            flash('Role created fail')->error();
        }

        return redirect()->route('client.roles.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = $this->roleRepository->get($id);

        set_page_meta('Show Role');
        return view('client.administration.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = $this->roleRepository->get($id);
        $permissions = $this->roleRepository->getParentPermissions();

        $parents_id = [];
        $role_permission = [];
        foreach ($role->permissions as $key => $value) {
            array_push($role_permission, $value->id);
            array_push($parents_id, $value->parent_id);
        }

        set_page_meta('Edit Role');
        return view('client.administration.roles.edit', compact('permissions', 'role', 'parents_id', 'role_permission'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, $id)
    {
        $data = $request->validated();

        if ($this->roleRepository->storeOrUpdate($data, $id)) {
            flash('Role updated successfully')->success();
        } else {
            flash('Role updated fail')->error();
        }

        return redirect()->route('client.roles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->roleRepository->delete($id)) {
            flash('Role deleted successfully')->success();
        } else {
            flash('Role deleted fail')->error();
        }

        return redirect()->route('client.roles.index');
    }
}
