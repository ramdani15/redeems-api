<?php

namespace App\Http\Controllers\Api\V1;

use App\Cores\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Api\V1\Profile\UpdateRequest;
use App\Http\Resources\Api\V1\UserResource;

class ProfileController extends Controller
{
    use ApiResponse;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/profile",
     *      summary="Get current user's profile",
     *      description="Get current user's profile",
     *      tags={"Profile"},
     *      security={
     *          {"token": {}}
     *      },
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="name", type="string", example="name"),
     *              @OA\Property(property="email", type="email", example="email@mail.com"),
     *              @OA\Property(property="point", type="number", example=10),
     *              @OA\Property(property="roles", type="object", example={}),
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Not Found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User Not Found."),
     *              @OA\Property(property="code", type="number", example=404),
     *          )
     *      ),
     * )
     */
    public function profile(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return $this->responseJson('error', 'User not Found', '', 404);
        }
        $user = new UserResource($user);
        return $this->responseJson('success', 'Get Profile Successfully', $user);
    }

    /**
     * @OA\Patch(
     *      path="/api/v1/profile",
     *      summary="Update current user's profile",
     *      description="Update current user's profile",
     *      tags={"Profile"},
     *      security={
     *          {"token": {}}
     *      },
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
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="name", type="string", example="name"),
     *              @OA\Property(property="email", type="email", example="email@mail.com"),
     *              @OA\Property(property="point", type="number", example=10),
     *              @OA\Property(property="roles", type="object", example={}),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User Not Found."),
     *              @OA\Property(property="code", type="number", example=404),
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
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Failed to Update Current User's Profile."),
     *              @OA\Property(property="code", type="number", example=500),
     *              @OA\Property(property="error", type="string", example="Something Wrong."),
     *          )
     *      ),
     * )
     */
    public function update(UpdateRequest $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return $this->responseJson('error', 'User Not Found', '', 404);
            }
            $data = $request->validated();
            if (!empty($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            }
            $user->update($data);
            $user = new UserResource($this->user->find($user->id));
            return $this->responseJson('success', 'Update Current User\'s Profile Successfully.', $user);
        } catch (\Throwable $th) {
            return $this->responseJson('error', 'Failed to Update Current User\'s Profile.', $th, $th->getCode() ?? 500);
        }
    }
}
