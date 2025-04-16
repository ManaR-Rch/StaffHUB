<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|unique:utilisateurs,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'sometimes|in:employe,manager,admin_rh',
        ]);

        $validated['mot_de_passe'] = Hash::make($validated['password']);
        $validated['role'] = $validated['role'] ?? 'employe';

        $user = Utilisateur::create($validated);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = Utilisateur::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->mot_de_passe)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken(
            $request->device_name,
            $this->getAbilitiesForRole($user->role),
            now()->addDays(30)
        )->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 30 * 24 * 60 * 60,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }

    protected function getAbilitiesForRole($role)
    {
        $abilities = [
            'employe' => [
                'conge:create',
                'conge:view',
                'absence:create',
                'paie:view',
            ],
            'manager' => [
                'conge:approve',
                'employe:view',
                'tache:assign',
            ],
            'admin_rh' => [
                'employe:*',
                'conge:*',
                'absence:*',
                'paie:*',
                'document:*',
            ],
        ];

        return $abilities[$role] ?? [];
    }
}
