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

class PermissionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('permission-list'), only: ['index']),
            new Middleware(PermissionMiddleware::using('permission-create'), only: ['store', 'create']),
            new Middleware(PermissionMiddleware::using('permission-edit'), only: ['edit', 'update']),
            new Middleware(PermissionMiddleware::using('permission-delete'), only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::orderBy('id', 'DESC')->get();

        return view('permission.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('permission.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
               "permission_name" => "bail|required|unique:permissions,name",
            ]);

            Permission::create(['name' => $request->permission_name, 'guard_name' => 'web']);

            Log::create([
                'user_id' => Auth::id(),
                'action' => "$request->permission_name Permission created",
                'type' => 2,
                'module_type' => 3,
                'created_by' => Auth::id(),
                'created_by_ip' => request()->ip(),
            ]);

            DB::commit();

            $notify = ['message'=> 'Permission Create Successfully', 'alert-type' => 'success'];

        } catch (\Exception $e) {
            DB::rollback();

            $notify = ["message" => "Permission Create Failed: " . $e->getMessage(), 'alert-type' => 'error'];
        }

        return redirect()->route('permissions.index')->with($notify);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $permission = Permission::findOrFail($id);

        return view('permission.edit', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();

        try {
            $permission = Permission::findOrFail($id);

            $oldPermission = $permission->name;

            $request->validate([
                'permission_name' => "bail|required|unique:roles,name, $permission->id"
            ]);

            $permission->update(['name' => $request->permission_name]);

            Log::create([
                'user_id' => Auth::id(),
                'action' => "$request->permission_name Permission updated. Old Permission is : $oldPermission",
                'type' => 2,
                'module_type' => 2,
                'updated_by' => Auth::id(),
                'updated_by_ip' => request()->ip(),
            ]);

            DB::commit();

            $notify = ['message'=> 'Permission Update Successfully', 'alert-type' => 'success'];

        } catch (\Exception $e) {
            DB::rollback();
            $notify = ["message" => "Role Update Failed: " . $e->getMessage(), 'alert-type' => 'error'];
        }

        return redirect()->route('permissions.index')->with($notify);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
