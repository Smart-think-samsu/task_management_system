<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $roles = Role::latest()->get();

            if($roles->isEmpty()){
                return response()->json([
                    "status" => "error",
                    "status_code" => 404,
                    "message" => "No roles found"
                ])->setStatusCode(404);
            }

            return response()->json([
                "status" => "success",
                "status_code" => 200,
                "message" => "Roles data found",
                "data" => $roles
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "status_code" => 500,
                "message" => "Error: " . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $permissions = Permission::latest()->get();

            if($permissions->isEmpty()){
                return response()->json([
                    "status" => "error",
                    "status_code" => 404,
                    "message" => "No Permissions found"
                ])->setStatusCode(404);
            }

            return response()->json([
                "status" => "success",
                "status_code" => 200,
                "message" => "Permissions data found",
                "data" => $permissions
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "status_code" => 500,
                "message" => "Error: " . $e->getMessage()
            ], 500);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'name' => 'bail|required|unique:roles,name'
            ]);

            $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

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

            return response()->json([
                "status" => "success",
                "status_code" => 201,
                "message" => "Role and permissions saved successfully."
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();

            return response()->json([
                "status" => "error",
                "status_code" => 422,
                "message" => "Validation failed. Error: " . $e->getMessage()
            ])->setStatusCode(422);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                "status" => "error",
                "status_code" => 500,
                "message" => "Failed to save. Please try again. Error: " . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $roleWithPermission = Role::with('permissions')->findOrFail($id);

            return response()->json([
                "status" => "success",
                "status_code" => 200,
                "message" => "Role with Permission data found",
                "data" => $roleWithPermission
            ])->setStatusCode(200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                "status" => "error",
                "status_code" => 404,
                "message" => "Role with Permission data not found."
            ])->setStatusCode(404);

        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "status_code" => 500,
                "message" => "Error: " . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        try {
            $data = Role::with('permissions')->findOrFail($id);

            return response()->json([
                "status" => "success",
                "status_code" => 200,
                "message" => "Role with Permission edit data found",
                "data" => $data
            ])->setStatusCode(200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                "status" => "error",
                "status_code" => 404,
                "message" => "Role with Permission data edit not found."
            ])->setStatusCode(404);

        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "status_code" => 500,
                "message" => "Error: " . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        try {

            $role = Role::findOrFail($role->id);

            $nameForLog = $role->name;

        } catch (ModelNotFoundException $e) {
            return response()->json([
                "status" => "error",
                "status_code" => 404,
                "message" => "No Role found"
            ], 404);
        }

        DB::beginTransaction();

        try {
            $request->validate([
                'name' => "bail|required|unique:roles,name, $role->id"
            ]);

            $role->update(['name' => $request->name]);

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

            return response()->json([
                "status" => "success",
                "status_code" => 200,
                "message" => "Role and permissions Update successfully."
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();

            return response()->json([
                "status" => "error",
                "status_code" => 422,
                "message" => "Validation failed. Error: " . $e->getMessage()
            ])->setStatusCode(422);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                "status" => "error",
                "status_code" => 500,
                "message" => "Failed to save. Please try again. Error: " . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
