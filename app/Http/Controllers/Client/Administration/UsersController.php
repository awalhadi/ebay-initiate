<?php

namespace App\Http\Controllers\Client\Administration;

use Illuminate\Http\Request;
use App\DataTables\UserDataTable;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use App\Repositories\Role\RoleRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;

class UsersController extends Controller
{
    protected $roleRepository;
    protected $userRepository;

    public function __construct(RoleRepositoryInterface $roleRepository, UserRepositoryInterface $userRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;

        // SAAS user package wise access manage
        $this->middleware('permission:' . saasPermission('Users'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UserDataTable $dataTable)
    {
        set_page_meta('Users');
        return $dataTable->render('client.administration.users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = $this->roleRepository->get();

        set_page_meta('Add User');
        return view('client.administration.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $data = $request->validated();

        if ($this->userRepository->storeOrUpdate($data)) {
            flash('User added successfully')->success();
        } else {
            flash('User added fail')->error();
        }

        return redirect()->route('client.users.index');
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
        $roles = $this->roleRepository->get();
        $user = $this->userRepository->getAppUserUsers($id);

        if (!$user) abort(404);

        set_page_meta('Edit User');
        return view('client.administration.users.edit', compact('roles', 'user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        $data = $request->validated();

        if ($this->userRepository->storeOrUpdate($data, $id)) {
            flash('User added successfully')->success();
        } else {
            flash('User added fail')->error();
        }

        return redirect()->route('client.users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->userRepository->delete($id, 'avatar')) {
            flash('User delete successfully')->success();
        } else {
            flash('User delete fail')->error();
        }

        return redirect()->route('client.users.index');
    }
}
