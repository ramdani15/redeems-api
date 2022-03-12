<?php

namespace App\Repositories\Api\V1;

use App\Traits\ApiFilterTrait;
use App\Repositories\ModelRepository;
use App\Models\User;
use App\Models\Role;
use App\Http\Resources\Api\V1\UserResource;

class UserRepository extends ModelRepository
{
    use ApiFilterTrait;

    public function __construct(
        User $user,
        Role $role
    ) {
        $this->user = $user;
        $this->role = $role;
    }

    /**
     * Register User
     * @param array $data
     * @return App\Http\Resources\Api\V1\UserResource
     */
    public function register($data)
    {
        $role = $this->role->whereName('user')->first();
        if (!$role) {
            return false;
        }
        $data['password'] = bcrypt($data['password']);
        $new_user = $this->user->create($data);
        $new_user->attachRole($role->id);

        return new UserResource($new_user);
    }

    /**
     * Search and Filter Users
     * @param Illuminate\Http\Request $request
     * @return App\Http\Resources\Api\V1\UserResource
     */
    public function searchUsers($request)
    {
        $limit = $request->limit ?? 10;
        $data = $this->user->where('id', '!=', auth()->user()->id);
        $filters = [
            [
                'field' => 'name',
                'value' => $request->name,
                'query' => 'like'
            ],
            [
                'field' => 'email',
                'value' => $request->email,
            ],
        ];
        $data = $this->filterFields($data, $filters);
        $data = $this->setOrder($data, [$request->sortBy, $request->sort]);
        $data = $data->paginate($limit);
        return UserResource::collection($data);
    }
}
