<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Auth; // Add this line
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/create-tokens', function () {
    // Authentication
    $credentials = [
        'email' => 'admin1@admin1.com',
        'password' => 'password1',
    ];

    if (!Auth::attempt($credentials)) {
        // Create a new user
        $user = new \App\Models\User();
        $user->name = 'Admin';
        $user->email = $credentials['email'];
        $user->password = Hash::make($credentials['password']);
        $user->save();

        // Attempt authentication again
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Create tokens
            $adminToken = $user->createToken('admin-token', ['create', 'update', 'delete']);
            $updateToken = $user->createToken('update-token', ['create', 'update']);
            $basicToken = $user->createToken('basic-token');

            // Return tokens as a JSON response
            return response()->json([
                'admin' => $adminToken->plainTextToken,
                'update' => $updateToken->plainTextToken,
                'basic' => $basicToken->plainTextToken,
            ]);
        }
    }
    return response()->json(['message' => 'Tokens not generated.'], 400);
});