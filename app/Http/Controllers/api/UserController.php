<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $users = User::paginate(10);
            if ($users->isEmpty()) {
                return response()->json([
                    "status" => "success",
                    "status_code" => 404,
                    "message" => "No user found.",
                    "data" => []
                ], 404);
            }
            return response()->json([
                "status" => "success",
                "status_code" => 200,
                "message" => "User list.",
                "data" => $users
            ], 200);
        }catch (\Exception $e){
            return response()->json([
                "status" => "error",
                "status_code" => 500,
                "message" => "Failed to fetch user. Error: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $roles = Role::all();
            if ($roles->isEmpty()) {
                return response()->json([
                    "status" => "error",
                    "status_code" => 404,
                    "message" => "No role found.",
                    "data" => []
                ], 404);
            }
            return response()->json([
                "status" => "success",
                "status_code" => 200,
                "message" => "Role data found",
                "data" => $roles
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "status_code" => 500,
                "message" => "Failed to fetch role. Error: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:user,email',
                'password' => 'required|min:8',
                'phone' => 'required|min:11|max:13',
                'role_id' => 'required'
            ]);
            $user = new User();
            $user->name = $request->name;
            $user->u_id = $request->u_id;
            $user->u_password = $request->u_password;
            $user->u_group = $request->u_group;
            $user->hnddevice = $request->hnddevice;
            $user->bar_user_id = $request->bar_user_id;
            $user->bar_user_pass = $request->bar_user_pass;
            $user->barcode_qty = $request->barcode_qty;
            $user->barcode_type = $request->barcode_type;
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->created_by = auth()->user()->id;
            $user->created_by_ip = $request->ip();
            $user->role_id = $request->role_id;
            $user->type = $request->type;
            $user->save();

            Log::create([
                'user_id' => Auth::id(),
                'action' => Auth::user()->name . "are created. $request->name this user",
                'type' => 2,
                'module_type' => 1,
                'created_by' => Auth::id(),
                'created_by_ip' => request()->ip(),
            ]);
            $token = $user->createToken('auth_token')->plainTextToken;
            DB::commit();
            return response()->json([
                "status" => "success",
                "message" => "User save success",
                "data" => $user,
                "token" => $token
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                "status" => "error",
                "status_code" => 422,
                "message" => "Validation failed. Error: " . $e->getMessage()
            ], 422);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                "status" => "error",
                "status_code" => 500,
                "message" => "Failed to save. Please try again. Error: " . $e->getMessage()
            ], 500);
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
    public function edit($id)
    {
        try {
            $user = User::findOrfail($id);
            if ($user) {
                return response()->json([
                    "status" => "success",
                    "status_code" => 200,
                    "message" => "User found.",
                    "data" => $user
                ], 200);
            } else {
                return response()->json([
                    "status" => "error",
                    "status_code" => 404,
                    "message" => "User not found.",
                    "data" => []
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "status_code" => 500,
                "message" => "Failed to fetch user. Error: " . $e->getMessage()
            ], 500);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try{
            DB::beginTransaction();
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => "required|email|max:255|unique:user,email, $id",
                'password' => 'required|min:8',
                'phone' => 'required|min:11|max:13',
                'role_id' => 'required'
            ]);
            $user = User::findOrfail($id);
            if ($user) {
                $user->name = $request->name;
                $user->u_id = $request->u_id;
                $user->u_password = $request->u_password;
                $user->u_group = $request->u_group;
                $user->hnddevice = $request->hnddevice;
                $user->bar_user_id = $request->bar_user_id;
                $user->bar_user_pass = $request->bar_user_pass;
                $user->barcode_qty = $request->barcode_qty;
                $user->barcode_type = $request->barcode_type;
                $user->phone = $request->phone;
                $user->email = $request->email;
                $user->password = bcrypt($request->password);
                $user->created_by = auth()->user()->id;
                $user->created_by_ip = $request->ip();
                $user->role_id = $request->role_id;
                $user->type = $request->type;
                $user->save();

                Log::create([
                    'user_id' => Auth::id(),
                    'action' => Auth::user()->name ."are updated. $request->name this user information",
                    'type' => 2,
                    'module_type' => 1,
                    'updated_by' => Auth::id(),
                    'updated_by_ip' => request()->ip(),
                ]);
                DB::commit();
                return response()->json([
                    "status" => "success",
                    "status_code" => 200,
                    "message" => "User updated successfully.",
                    "data" => $user
                ], 200);
            } else {
                return response()->json([
                    "status" => "error",
                    "status_code" => 404,
                    "message" => "User not found.",
                    "data" => []
                ], 404);
            }
        }catch (\Illuminate\Validation\ValidationException $e){
            DB::rollback();
            return response()->json([
                "status" => "error",
                "status_code" => 422,
                "message" => "Validation failed. Error: " . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $user = User::findOrfail($id);
            if ($user) {
                $user->delete();
                Log::create([
                    'user_id' => Auth::id(),
                    'action' => Auth::user()->name ."are Delete. $user->name this user information",
                    'type' => 2,
                    'module_type' => 1,
                    'updated_by' => Auth::id(),
                    'updated_by_ip' => request()->ip(),
                ]);
                DB::commit();
                return response()->json([
                    "status" => "success",
                    "status_code" => 200,
                    "message" => "User deleted successfully."
                ], 200);
            } else {
                return response()->json([
                    "status" => "error",
                    "status_code" => 404,
                    "message" => "User not found.",
                    "data" => []
                ], 404);
            }

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                "status" => "error",
                "status_code" => 500,
                "message" => "Failed to delete user. Error: " . $e->getMessage()
            ], 500);
        }

    }

    public function status($id){

        try {
            DB::beginTransaction();
            $user = User::findOrfail($id);
            if ($user) {
                $user->is_active = $user->is_active == 1 ? 0 : 1;
                $user->save();
                Log::create([
                    'user_id' => Auth::id(),
                    'action' => Auth::user()->name ."are updated. $user->name this user status",
                    'type' => 2,
                    'module_type' => 1,
                    'updated_by' => Auth::id(),
                    'updated_by_ip' => request()->ip(),
                ]);
                DB::commit();
                return response()->json([
                    "status" => "success",
                    "status_code" => 200,
                    "message" => $user->is_active == 1 ? "User Activated successfully" : "User Deactivated successfully",
                ], 200);
            } else {
                return response()->json([
                    "status" => "error",
                    "status_code" => 404,
                    "message" => "User not found.",
                ], 404);
            }

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                "status" => "error",
                "status_code" => 500,
                "message" => "Failed to update user status. Error: " . $e->getMessage()
            ], 500);
        }

    }

    public function workingstatus($id){

            try {
                DB::beginTransaction();
                $user = User::findOrfail($id);
                if ($user) {
                    $user->is_working = $user->is_working == 1 ? 0 : 1;
                    $user->save();
                    Log::create([
                        'user_id' => Auth::id(),
                        'action' => Auth::user()->name ."are updated. $user->name this user status",
                        'type' => 2,
                        'module_type' => 1,
                        'updated_by' => Auth::id(),
                        'updated_by_ip' => request()->ip(),
                    ]);
                    DB::commit();
                    return response()->json([
                        "status" => "success",
                        "status_code" => 200,
                        "message" => $user->is_working == 1 ? "User Activated successfully" : "User Deactivated successfully",
                    ], 200);
                } else {
                    return response()->json([
                        "status" => "error",
                        "status_code" => 404,
                        "message" => "User not found.",
                    ], 404);
                }

            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    "status" => "error",
                    "status_code" => 500,
                    "message" => "Failed to update user status. Error: " . $e->getMessage()
                ], 500);
            }
    }
}
