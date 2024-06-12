<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Info(
 *     title="API Documentation Practice",
 *     version="1.0",
 *     description="Practicing Documentations",
 *     termsOfService="https://example.com"
 * )
 */

class AuthController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="User login",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Login successful"),
     *     @OA\Response(response="401", description="Unauthorized")
     * )
     */

    public function login(Request $request){

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('API Token')->plainTextToken;
        return (new UserResource($user))->additional(['token' => $token]);

    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="User logout",
     *     @OA\Response(response="200", description="Logout successful")
     * )
     */

    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return response()->json(['message'=>'logged out successfully']);
    }
}
