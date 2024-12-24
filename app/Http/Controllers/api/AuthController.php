<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:user,email',
                'password' => 'required|min:8',
                'created_by' => 'required',
                'created_by_ip' => 'required',
                'phone' => 'required',
            ]);

            $user = new User();
            $user->name = $request->name;
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->created_by = $request->created_by;
            $user->created_by_ip = $request->created_by_ip;
            $user->role_id = $request->role_id;
            $user->save();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                "status" => "success",
                "message" => "User save success",
                "data" => $user,
                "token" => $token
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                "status" => "error",
                "message" => "Validation failed. Error: " . $e->getMessage()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to save. Please try again. Error: " . $e->getMessage()
            ], 500);
        }

    }

    public function login(Request $request){

        //return $request;

        $credentials = request(['email', 'password']);
        if (!auth()->attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
        $user = User::with('role.permissions')->where('email', $credentials['email'])->first();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

}
