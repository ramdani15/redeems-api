<?php

namespace App\Http\Controllers\Api\V1;

use App\Cores\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Api\V1\User\CreateRequest;
use App\Http\Requests\Api\V1\User\DetailRequest;
use App\Http\Requests\Api\V1\User\UpdateRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Repositories\Api\V1\UserRepository;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(
        User $user,
        UserRepository $userRepository
    ) {
        $this->user = $user;
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/users",
     *      summary="List Users",
     *      description="Get List Users",
     *      tags={"Users"},
     *      security={
     *          {"token": {}}
     *      },
     *      @OA\Parameter(
     *          name="name",
     *          in="query",
     *          description="Name"
     *      ),
     *      @OA\Parameter(
     *          name="email",
     *          in="query",
     *          description="Email"
     *      ),
     *      @OA\Parameter(
     *          name="sort",
     *          in="query",
     *          description="1 for Ascending -1 for Descending"
     *      ),
     *      @OA\Parameter(
     *          name="sortBy",
     *          in="query",
     *          description="Field to sort"
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          description="Limit (Default 10)"
     *      ),
     *      @OA\Parameter(
     *          name="page",
     *          in="query",
     *          description="Num Of Page"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="object", example={}),
     *              @OA\Property(property="pagination", type="object", example={}),
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Failed to Get Users."),
     *              @OA\Property(property="code", type="number", example=500),
     *              @OA\Property(property="error", type="string", example="Something Wrong."),
     *          )
     *      ),
     * )
     */
    public function index(Request $request)
    {
        try {
            canApiOrAbort('api-users-index');
            $users = $this->userRepository->searchUsers($request);
            return $this->responseJson('pagination', 'Get Users Successfully.', $users, 200, [$request->sortBy, $request->sort]);
        } catch (\Throwable $th) {
            return $this->responseJson('error', 'Failed to Get Users.', $th, $th->getCode() ?? 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/v1/users",
     *      summary="Create User",
     *      description="Create User",
     *      tags={"Users"},
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
     *          response=403,
     *          description="Forbidden",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Failed to Create User."),
     *              @OA\Property(property="code", type="number", example=403),
     *              @OA\Property(property="error", type="string", example="You don't have permission."),
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
     *              @OA\Property(property="message", type="string", example="Failed to Create User."),
     *              @OA\Property(property="code", type="number", example=500),
     *              @OA\Property(property="error", type="string", example="Something Wrong."),
     *          )
     *      ),
     *  )
     */
    public function store(CreateRequest $request)
    {
        try {
            canApiOrAbort('api-users-store');
            $data = $request->validated();
            $user = $this->userRepository->register($data);
            if ($user === false) {
                return $this->responseJson('error', 'Failed to Signup', '', 500);
            }
            return $this->responseJson('created', 'Create User Successfully.', $user, 201);
        } catch (\Throwable $th) {
            return $this->responseJson('error', 'Failed to Create User.', $th, $th->getCode() ?? 500);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/users/{id}",
     *      summary="Detail User",
     *      description="Get Detail User",
     *      tags={"Users"},
     *      security={
     *          {"token": {}}
     *      },
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          description="ID"
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
     *              @OA\Property(property="message", type="string", example="Failed to Get Detail User."),
     *              @OA\Property(property="code", type="number", example=500),
     *              @OA\Property(property="error", type="string", example="Something Wrong."),
     *          )
     *      ),
     * )
     */
    public function show(DetailRequest $request)
    {
        try {
            canApiOrAbort('api-users-show');
            $user = $this->user->find($request->id);
            if (!$user) {
                return $this->responseJson('error', 'User Not Found', '', 404);
            }
            $user = new UserResource($user);
            return $this->responseJson('success', 'Get Detail User Successfully.', $user);
        } catch (\Throwable $th) {
            return $this->responseJson('error', 'Failed to Get Detail User.', $th, $th->getCode() ?? 500);
        }
    }

    /**
     * @OA\Patch(
     *      path="/api/v1/users/{id}",
     *      summary="Update User",
     *      description="Update Data User",
     *      tags={"Users"},
     *      security={
     *          {"token": {}}
     *      },
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          description="ID"
     *      ),
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
     *              @OA\Property(property="message", type="string", example="Failed to Update Data User."),
     *              @OA\Property(property="code", type="number", example=500),
     *              @OA\Property(property="error", type="string", example="Something Wrong."),
     *          )
     *      ),
     * )
     */
    public function update(UpdateRequest $request)
    {
        try {
            canApiOrAbort('api-users-update');
            $user = $this->user->find($request->id);
            if (!$user) {
                return $this->responseJson('error', 'User Not Found', '', 404);
            }
            $data = $request->validated();
            if (!empty($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            }
            $user->update($data);
            $user = new UserResource($this->user->find($user->id));
            return $this->responseJson('success', 'Update User Successfully.', $user);
        } catch (\Throwable $th) {
            return $this->responseJson('error', 'Failed to Update User.', $th, $th->getCode() ?? 500);
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/users/{id}",
     *      summary="Delete User",
     *      description="Delete Data User",
     *      tags={"Users"},
     *      security={
     *          {"token": {}}
     *      },
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          description="ID"
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="No Content",
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
     *              @OA\Property(property="message", type="string", example="Failed to Delete User."),
     *              @OA\Property(property="code", type="number", example=500),
     *              @OA\Property(property="error", type="string", example="Something Wrong."),
     *          )
     *      ),
     * )
     */
    public function destroy(DetailRequest $request)
    {
        try {
            canApiOrAbort('api-users-destroy');
            $user = $this->user->find($request->id);
            if (!$user) {
                return $this->responseJson('error', 'User Not Found', '', 404);
            }
            $user->delete();
            return $this->responseJson('deleted', 'Delete User Successfully.');
        } catch (\Throwable $th) {
            return $this->responseJson('error', 'Failed to Delete User.', $th, $th->getCode() ?? 500);
        }
    }
    
    /**
     * @OA\Delete(
     *      path="/api/v1/users/{id}/delete-permanent",
     *      summary="Delete Permanent User",
     *      description="Delete Permanent Data User",
     *      tags={"Users"},
     *      security={
     *          {"token": {}}
     *      },
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          description="ID"
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="No Content",
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
     *              @OA\Property(property="message", type="string", example="Failed to Delete User."),
     *              @OA\Property(property="code", type="number", example=500),
     *              @OA\Property(property="error", type="string", example="Something Wrong."),
     *          )
     *      ),
     * )
     */
    public function deletePermanent(DetailRequest $request)
    {
        try {
            canApiOrAbort('api-users-destroy');
            $user = $this->user->withTrashed()->find($request->id);
            if (!$user) {
                return $this->responseJson('error', 'User Not Found', '', 404);
            }
            $user->forceDelete();
            return $this->responseJson('deleted', 'Delete User Successfully.');
        } catch (\Throwable $th) {
            return $this->responseJson('error', 'Failed to Delete User.', $th, $th->getCode() ?? 500);
        }
    }
}
