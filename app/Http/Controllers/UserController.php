<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Models\Role;


class UserController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('user-list'), only: ['index']),
            new Middleware(PermissionMiddleware::using('user-create'), only: ['store', 'create']),
            new Middleware(PermissionMiddleware::using('user-edit'), only: ['edit', 'update']),
            new Middleware(PermissionMiddleware::using('user-show'), only: ['show']),
            new Middleware(PermissionMiddleware::using('user-delete'), only: ['destroy']),
            new Middleware(PermissionMiddleware::using('user-status-update'), only: ['userstatus']),
            new Middleware(PermissionMiddleware::using('user-working-status'), only: ['workingstatus']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        try{
            $users = User::paginate(10);
            return view('user.index', compact('users'));
        }catch (\Exception $e){
            return redirect()->back()->with(['error' => 'Failed to fetch users. Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('user.create',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if($request->password == $request->re_password){
            try {
                DB::beginTransaction();
                $request->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:users,email',
                    'password' => 'required|min:8',
                    'phone' => 'required|min:11|max:13',
                    'role_id' => 'required'
                ]);
                $user = new User();
                $user->name = $request->name;
                $user->phone = $request->phone;
                $user->email = $request->email;
                $user->password = bcrypt($request->password);
                $user->role_id = $request->role_id;
                $user->save();
                //assign role
                $roleName = Role::find($request->role_id)->value('name');
                $user->assignRole($roleName);

                DB::commit();
                $notify = ['message'=> 'User Create Successfully', 'alert-type' => 'success'];

            } catch (\Exception $e) {
                DB::rollback();
                $notify = ["message" => "User Create Failed: " . $e->getMessage(), 'alert-type' => 'error'];
            }

        }else{
            $notify = ["message" => "Password and Confirm Password not match", 'alert-type' => 'error'];
            return redirect()->back()->with($notify);
        }
        return redirect()->route('user.index')->with($notify);
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
            $roles = Role::all();
            $user = User::findOrfail($id);
            if ($user) {
                return view('user.edit', compact('user','roles'));
            }else{
                return redirect()->back()->with(['error' => 'User not found.']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => 'Failed to fetch user. Error: ' . $e->getMessage()]);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {


        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => "required|email|max:255|unique:users,email, $id",
                'phone' => 'required|min:11|max:13',
                'role_id' => 'required'
            ]);
            $user = User::findOrfail($id);
            if ($user) {
                $user->name = $request->name;
                $user->phone = $request->phone;
                $user->email = $request->email;
                if($request->password != null){
                    $user->password = bcrypt($request->password);
                }
                $user->role_id = $request->role_id;
                $user->save();
                return redirect()->back()->with(['success' => 'User updated successfully.']);
            } else {
                return redirect()->back()->with(['error' => 'User not found.']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => 'Failed to update user. Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrfail($id);
            if ($user) {
                $user->delete();
                return redirect()->route('user.index')->with(['success' => 'User deleted successfully.']);
            } else {
                return redirect()->back()->with(['error' => 'User not found.']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => 'Failed to delete user. Error: ' . $e->getMessage()]);
        }

    }

    public function userstatus($id){
        try {
            DB::beginTransaction();
            $user = User::findOrfail($id);

                $user->is_active = $user->is_active == 1 ? 0 : 1;
                $user->save();
                Log::create([
                    'user_id' => Auth::id(),
                    'action' => Auth::user()->name ."are updated. $user->name this user status",
                    'type' => 2,
                    'module_type' => 1,
                    'updated_at' => Carbon::now(),
                    'updated_by' => Auth::id(),
                    'created_by_ip' => request()->ip(),
                ]);
                DB::commit();
                return response()->json(['status' => $user->is_active]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                "status" => "error",
                "status_code" => 500,
                "message" => "Failed to update user status. Error: " . $e->getMessage(),
            ], 500);
        }
    }

    public function workingstatus($id){
        try {
            DB::beginTransaction();
            $user = User::findOrfail($id);
                $user->is_working = $user->is_working == 1 ? 0 : 1;
                $user->save();
                Log::create([
                    'user_id' => Auth::id(),
                    'action' => Auth::user()->name ."are updated. $user->name this user status",
                    'type' => 2,
                    'module_type' => 1,
                    'updated_at' => Carbon::now(),
                    'updated_by' => Auth::id(),
                    'updated_by_ip' => request()->ip(),
                ]);
                DB::commit();
                return response()->json(["status" => $user->is_working], 200);

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
