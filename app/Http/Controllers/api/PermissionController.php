<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $permissions = Permission::latest()->get();

            if($permissions->isEmpty()){
                return response()->json([
                    "status" => "error",
                    "status_code" => 404,
                    "message" => "No permissions found"
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
            ])->setStatusCode(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
               "name" => "bail|required|unique:permissions,name",
            ]);

            Permission::create(['name' => $request->name, 'guard_name' => 'web']);

            Log::create([
                'user_id' => Auth::id(),
                'action' => "$request->name Permission created",
                'type' => 2,
                'module_type' => 3,
                'created_by' => Auth::id(),
                'created_by_ip' => request()->ip(),
            ]);

            DB::commit();

            return response()->json([
                "status" => "success",
                "status_code" => 201,
                "message" => "Permissions saved successfully."
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $data = Permission::findOrFail($id);

            return response()->json([
                "status" => "success",
                "status_code" => 200,
                "message" => "Permission edit data found",
                "data" => $data
            ])->setStatusCode(200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                "status" => "error",
                "status_code" => 404,
                "message" => "Permission edit data not found."
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
    public function update(Request $request, string $id)
    {
        try {

            $permission = Permission::findOrFail($id);

            $oldPermission = $permission->name;

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
                'name' => "bail|required|unique:roles,name, $permission->id"
            ]);

            $permission->update(['name' => $request->name]);

            Log::create([
                'user_id' => Auth::id(),
                'action' => "$request->name Permission updated. Old Permission is : $oldPermission",
                'type' => 2,
                'module_type' => 2,
                'updated_by' => Auth::id(),
                'updated_by_ip' => request()->ip(),
            ]);

            DB::commit();

            return response()->json([
                "status" => "success",
                "status_code" => 200,
                "message" => "permissions Update successfully."
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
