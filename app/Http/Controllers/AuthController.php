<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use App\Models\Barbershop;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function registerCustomer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'customer',
        ]);

        Customer::create([
            'user_id' => $user->id,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'role' => $user->role,
            'status' => 'active'
        ], 201);
    }

    public function registerOwner(Request $request)
    {
        $request->validate([
            'owner_name' => 'required|string|max:255',
            'salon_name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users',
            'address' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->owner_name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'owner',
        ]);

        Barbershop::create([
            'owner_id' => $user->id,
            'salon_name' => $request->salon_name,
            'address' => $request->address,
            'status' => 'pending',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'role' => $user->role,
            'status' => 'pending'
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'phone' => ['بيانات الدخول غير صحيحة.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        
        $status = 'active';
        if ($user->role === 'owner') {
            $salon = Barbershop::where('owner_id', $user->id)->first();
            $status = $salon ? $salon->status : 'pending';
        }

        return response()->json([
            'success' => true,
            'token' => $token,
            'role' => $user->role,
            'status' => $status
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function checkStatus(Request $request)
    {
        $user = $request->user();
        $status = 'active';

        if ($user->role === 'owner') {
            $salon = Barbershop::where('owner_id', $user->id)->first();
            $status = $salon ? $salon->status : 'pending';
        }

        return response()->json([
            'success' => true,
            'role' => $user->role,
            'status' => $status
        ]);
    }
}
