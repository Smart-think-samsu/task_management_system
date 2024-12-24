<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('role-list'), only: ['index']),
            new Middleware(PermissionMiddleware::using('role-create'), only: ['store', 'create']),
            new Middleware(PermissionMiddleware::using('role-edit'), only: ['edit', 'update']),
            new Middleware(PermissionMiddleware::using('role-show'), only: ['show']),
            new Middleware(PermissionMiddleware::using('role-delete'), only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $roles = Role::orderBy('id', 'DESC')->get();

        return view('role.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::orderby('id')->get();

        return view('role.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'role_name' => 'bail|required|unique:roles,name'
            ]);

            $role = Role::create(['name' => $request->role_name, 'guard_name' => 'web']);

            $postPermissions = $request->input('permissions');

            $permissionsForLog = Permission::whereIn('id', $postPermissions)->get();

            $permissionForLog = implode(",", $permissionsForLog->pluck('name')->toArray());

            foreach ($postPermissions as $key => $val) {
                $permissions[intval($val)] = intval($val);
            }

            $role->syncPermissions($permissions);

            Log::create([
                'user_id' => Auth::id(),
                'action' => "$request->name role created. Permissions is ($permissionForLog)",
                'type' => 2,
                'module_type' => 2,
                'created_by' => Auth::id(),
                'created_by_ip' => request()->ip(),
            ]);

            DB::commit();

            $notify = ['message'=> 'Role Create Successfully', 'alert-type' => 'success'];

        } catch (\Exception $e) {
            DB::rollback();

            $notify = ["message" => "Role Create Failed: " . $e->getMessage(), 'alert-type' => 'error'];
        }

        return redirect()->route('roles.index')->with($notify);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $roleWithPermission = Role::with('permissions')->findOrFail($id);

        return view('role.show', compact('roleWithPermission'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $permissions = Permission::all();

        $roleWithPermissions = Role::with('permissions')->findOrFail($id);

        return view('role.edit', compact('permissions','roleWithPermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        DB::beginTransaction();
        try {

            $role = Role::findOrFail($role->id);

            $nameForLog = $role->name;

            $request->validate([
                'role_name' => "bail|required|unique:roles,name, $role->id"
            ]);

            $role->update(['name' => $request->role_name]);

            $postPermissions = $request->input('permissions');

            $newPermissionsForLog = Permission::whereIn('id', $postPermissions)->get();

            $newPermissionForLog = implode(",", $newPermissionsForLog->pluck('name')->toArray());

            $oldPermissionsForLog = Role::with('permissions')->where('id', $role->id)->first();

            $oldPermissionForLog = implode(",", $oldPermissionsForLog->permissions->pluck('name')->toArray());

            foreach ($postPermissions as $key => $val) {
                $permissions[intval($val)] = intval($val);
            }

            $role->syncPermissions($permissions);

            Log::create([
                'user_id' => Auth::id(),
                'action' => "$nameForLog role updated. new role name is $request->name. Permissions updated..! Old permission is ($oldPermissionForLog). New permission is ($newPermissionForLog)",
                'type' => 2,
                'module_type' => 2,
                'updated_by' => Auth::id(),
                'updated_by_ip' => request()->ip(),
            ]);

            DB::commit();

            $notify = ['message'=> 'Role Update Successfully', 'alert-type' => 'success'];

        } catch (\Exception $e) {
            DB::rollback();
            $notify = ["message" => "Role Update Failed: " . $e->getMessage(), 'alert-type' => 'error'];
        }

        return redirect()->route('roles.index')->with($notify);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
