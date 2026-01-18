<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */

    public function addUser(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $request->id],
            'role' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::defaults()]
        ]);

        if ($validation->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->errors(),
            ], 422);
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->string('password'))
        ]);

        return response()->noContent();
    }

    public function updateUser(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $id],
            'role' => ['required', 'string'],
            'password' => ['nullable', 'confirmed', Password::defaults()]
        ]);

        if ($validation->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->errors(),
            ], 422);
        }

        $user = User::findOrFail($id);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role
        ];

        if ($request->filled('password')){
            $data['password'] = Hash::make($request->string('password'));
        }

        $user->update($data);

        return response()->noContent();
    }

    public function deleteUser(Request $request, $id): Response 
    {
        $user = User::findOrFail($id);

        $user->delete();
        
        return response()->noContent();
    }

    public function updateAccountInfo(Request $request){

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
        ]);

        $request->user()->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return response()->noContent();

    }

    public function updateAccountPass(Request $request){
        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->noContent();
    }
}
