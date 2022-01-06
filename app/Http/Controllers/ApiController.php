<?php

namespace App\Http\Controllers;

use App\Http\Helper\TokenGenerator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Class ApiController
 * @package App\Http\Controllers
 */
class ApiController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->only('name', 'email', 'password');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->toArray()], Response::HTTP_OK);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user
            ], Response::HTTP_OK);
        }catch (){

        }




    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('user', 'password');

        $validator = Validator::make($credentials, [
            'user' => 'required',
            'password' => 'required|string|min:6|max:50'
        ]);

        $validatedData = $validator->validated();

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->toArray()], Response::HTTP_OK);
        }

        $user = User::where('email',$validatedData['user'])->orWhere('name',$validatedData['user'])->first();

        if (!$user || !Hash::check($validatedData["password"], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Login credentials are invalid.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = app(TokenGenerator::class)->generateToken($user);

        if(!$token){
            return response()->json([
                'success' => false,
                'message' => 'Could not create token.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'success' => true,
            'token' => $token,
        ]);

    }
}
