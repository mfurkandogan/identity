<?php

namespace App\Http\Controllers;

use App\Http\Helper\TokenGenerator;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

/**
 * Class ApiController
 * @package App\Http\Controllers
 *
 * @OA\Post(path="/api/v1/register",
 *   tags={"user"},
 *   summary="Register user into the system",
 *   description="",
 *   operationId="login",
 *   @OA\Parameter(
 *     name="name",
 *     required=true,
 *     in="query",
 *     description="The user name for register",
 *     @OA\Schema(
 *         type="string"
 *     )
 *   ),
 *   @OA\Parameter(
 *     name="email",
 *     required=true,
 *     in="query",
 *     description="The email for register",
 *     @OA\Schema(
 *         type="string"
 *     )
 *   ),
 *   @OA\Parameter(
 *     name="password",
 *     required=true,
 *     in="query",
 *     description="The password for register",
 *     @OA\Schema(
 *         type="string",
 *     ),
 *     description="The password for login in clear text",
 *   ),
 *   @OA\Response(
 *     response=200,
 *     description="successful operation",
 *     @OA\Schema(type="string")
 *   ),
 *   @OA\Response(response=400, description="Invalid username/password supplied")
 * )
 */
class ApiController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $data = $request->only('name', 'email', 'password');
        $validator = Validator::make($data, [
            'name' => 'required|string|unique:users',
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
        }catch (QueryException $e){
            $errorCode = $e->errorInfo[1];
           if ($errorCode == 1062) {
               return response()->json([
                   'success' => false,
                   'message' => 'Username or email already exists'
               ], Response::HTTP_OK);
           }

        }

        return response()->json([
            'success' => false,
            'message' => 'Error'
        ], Response::HTTP_OK);

    }

    /**
     * @OA\Post(path="/api/v1/login",
     *   tags={"user"},
     *   summary="Logs user into the system",
     *   description="",
     *   operationId="login",
     *   @OA\Parameter(
     *     name="username or email",
     *     required=true,
     *     in="query",
     *     description="User name or Email for login",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="password",
     *     in="query",
     *     @OA\Schema(
     *         type="string",
     *     ),
     *     description="The password for login in clear text",
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="successful operation",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(response=400, description="Invalid username/password supplied")
     * )
     */
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
