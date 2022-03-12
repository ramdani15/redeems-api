<?php

namespace App\Http\Controllers\Api\V1;

use App\Cores\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\User\CreateRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Repositories\Api\V1\UserRepository;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        User $user,
        Role $role,
        UserRepository $userRepository
    ) {
        $this->user = $user;
        $this->role = $role;
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Post(
     *      path="/api/v1/auth/login",
     *      summary="Sign in",
     *      description="Login by email, password",
     *      tags={"Auth"},
     *      @OA\RequestBody(
     *          required=true,
     *          required=true,
     *          description="Pass user credentials",
     *          @OA\JsonContent(
     *              required={"email","password"},
     *              @OA\Property(property="email", type="email", example="user1@gmail.com"),
     *              @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="accessToken", type="string", example="1|eyJ0eXAiOiJKV1QiLCJhbGciOi..."),
     *              @OA\Property(property="user", type="object", example={}),
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Email or Password incorrect"),
     *              @OA\Property(property="code", type="number", example=401),
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Wrong Credentials",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object", example={}),
     *          )
     *      ),
     * )
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $loginRequest = $this->user->whereEmail($credentials['email'])->first();

        if (!$loginRequest) {
            return $this->responseJson('error', 'Unauthorized. Email or username not found', '', 401);
        }

        if (!Auth::attempt($credentials)) {
            return $this->responseJson('error', 'Unauthorized.', '', 401);
        }
        $token = $loginRequest->createToken('authToken');
        $user = new UserResource($loginRequest);
        $data = [
            'accessToken' => $token->plainTextToken,
            'user' => $user
        ];
        return $this->responseJson('success', 'Login success', $data);
    }

    /**
    * @OA\Post(
    *       path="/api/v1/auth/logout",
    *       summary="Log user out ",
    *       description="Endpoint to log current user out",
    *       tags={ "Auth"},
    *       security={
    *           {"token": {}}
    *       },
    *       @OA\Response(
    *          response=200,
    *          description="Success",
    *          @OA\JsonContent(
    *              @OA\Property(property="message", type="string", example="Logout Successfully"),
    *          )
    *      ),
    *      @OA\Response(
    *          response=400,
    *          description="Bad Request",
    *          @OA\JsonContent(
    *              @OA\Property(property="message", type="string", example="Failed to Logout"),
    *              @OA\Property(property="code", type="number", example=400),
    *          )
    *      ),
    *      @OA\Response(
    *          response=401,
    *          description="Unauthenticated",
    *          @OA\JsonContent(
    *              @OA\Property(property="message", type="string", example="Unauthenticated."),
    *          )
    *      ),
    *      @OA\Response(
    *          response=404,
    *          description="Not found",
    *          @OA\JsonContent(
    *              @OA\Property(property="message", type="string", example="User not found."),
    *              @OA\Property(property="code", type="number", example=404),
    *          )
    *      ),
    * )
    */
    public function logout()
    {
        if (!auth()->user()) {
            return $this->responseJson('error', 'User not found.', '', 404);
        }

        $revoke = auth()->user()->currentAccessToken()->delete();

        /**Use below code if you want to log current user out in all devices */
        // $revoke = auth()->user()->tokens()->delete();

        if (!$revoke) {
            return $this->responseJson('error', 'Failed to Logout');
        }
        return $this->responseJson('success', 'Logout Successfully');
    }

    /**
     * @OA\Post(
     *      path="/api/v1/auth/signup",
     *      summary="Sign up",
     *      description="Register User",
     *      tags={"Auth"},
     *      @OA\RequestBody(
     *          required=true,
     *          description="Pass user credentials",
     *          @OA\JsonContent(
     *              required={"name", "email", "password", "password_confirmation"},
     *              @OA\Property(property="name", type="string", example="user1"),
     *              @OA\Property(property="email", type="email", format="email", example="user1@gmail.com"),
     *              @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *              @OA\Property(property="password_confirmation", type="string", format="password", example="PassWord12345"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Created",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="name", type="string", example="name"),
     *              @OA\Property(property="email", type="email", example="email@mail.com"),
     *              @OA\Property(property="point", type="number", example=10),
     *              @OA\Property(property="roles", type="object", example={}),
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Wrong Credentials",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object", example={}),
     *          )
     *      ),
     *  )
     */
    public function signup(CreateRequest $request)
    {
        $data = $request->validated();
        $user = $this->userRepository->register($data);
        if ($user === false) {
            return $this->responseJson('error', 'Failed to Signup', '', 500);
        }
        return $this->responseJson('created', 'Signup success', $user, 201);
    }
}
