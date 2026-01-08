<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyRoleRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
//use App\Permission;
use App\Role;
use App\Models\Auditlog;
use Illuminate\Support\Facades\Route;

class RolesController extends Controller
{

    public function index()
    {
        $roles = Role::all();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
         
        return view('admin.roles.create');
    }

    public function store(StoreRoleRequest $request)
    {
       
       // echo 123;exit;
        $role = Role::create($request->all());
        //$role->permissions()->sync($request->input('permissions', []));

        // Audit Log Entry
        $resultMessage = sprintf('Created role: %s | name: %s', $role->id, $role->name);
        Auditlog::info(Auditlog::CATEGORY_ROLE, $resultMessage, $role->id);

        return redirect()->route('admin.roles.index')->with('success', 'Role successfully added!');
    }

    public function edit(Role $role)
    {
        
        

        return view('admin.roles.edit', compact('role'));
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
       

        $role->update($request->all());

        // Audit Log Entry
        $resultMessage = sprintf('Updated role: %s | name: %s', $role->id, $role->name);
        Auditlog::info(Auditlog::CATEGORY_ROLE, $resultMessage, $role->id);

        //$role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('admin.roles.index');
    }

    public function show(Role $role)
    {
       

        return view('admin.roles.show', compact('role'));
    }

    public function destroy(Role $role)
    {
        

        $role->delete();

        // Audit Log Entry
        $resultMessage = sprintf('Deleted role: %s | name: %s', $role->id, $role->name);
        Auditlog::critical(Auditlog::CATEGORY_ROLE, $resultMessage, $role->id);

        return back();
    }

    public function massDestroy(MassDestroyRoleRequest $request)
    {
        Role::whereIn('id', request('ids'))->delete();

        return response(null, 204);
    }
}
