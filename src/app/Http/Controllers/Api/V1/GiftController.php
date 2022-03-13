<?php

namespace App\Http\Controllers\Api\V1;

use App\Cores\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Gift;
use App\Models\GiftLike;
use App\Models\GiftRating;
use App\Http\Requests\Api\V1\Gift\CreateRequest;
use App\Http\Requests\Api\V1\Gift\DetailRequest;
use App\Http\Requests\Api\V1\Gift\RatingRequest;
use App\Http\Requests\Api\V1\Gift\RedeemRequest;
use App\Http\Requests\Api\V1\Gift\RedeemsRequest;
use App\Http\Requests\Api\V1\Gift\UpdateRequest;
use App\Http\Resources\Api\V1\GiftResource;
use App\Repositories\Api\V1\GiftRepository;

class GiftController extends Controller
{
    use ApiResponse;

    public function __construct(
        Gift $gift,
        GiftLike $giftLike,
        GiftRating $giftRating,
        GiftRepository $giftRepository
    ) {
        $this->gift = $gift;
        $this->giftLike = $giftLike;
        $this->giftRating = $giftRating;
        $this->giftRepository = $giftRepository;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/gifts",
     *      summary="List Gifts",
     *      description="Get List Gifts",
     *      tags={"Gifts"},
     *      security={
     *          {"token": {}}
     *      },
     *      @OA\Parameter(
     *          name="name",
     *          in="query",
     *          description="Name"
     *      ),
     *      @OA\Parameter(
     *          name="description",
     *          in="query",
     *          description="description"
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
     *              @OA\Property(property="message", type="string", example="Failed to Get Gifts."),
     *              @OA\Property(property="code", type="number", example=500),
     *              @OA\Property(property="error", type="string", example="Something Wrong."),
     *          )
     *      ),
     * )
     */
    public function index(Request $request)
    {
        try {
            canApiOrAbort('api-gifts-index');
            $gifts = $this->giftRepository->searchGifts($request);
            return $this->responseJson('pagination', 'Get Gifts Successfully.', $gifts, 200, [$request->sortBy, $request->sort]);
        } catch (\Throwable $th) {
            return $this->responseJson('error', 'Failed to Get Gifts.', $th, $th->getCode() ?? 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/v1/gifts",
     *      summary="Create Gift",
     *      description="Create Gift",
     *      tags={"Gifts"},
     *      security={
     *          {"token": {}}
     *      },
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"name", "description", "stock", "point", "image"},
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="description",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="stock",
     *                      type="number",
     *                  ),
     *                  @OA\Property(
     *                      property="point",
     *                      type="number",
     *                  ),
     *                  @OA\Property(
     *                      property="image",
     *                      type="file",
     *                  ),
     *              )
     *          )
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
     *              @OA\Property(property="message", type="string", example="Failed to Create Gift."),
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
     *              @OA\Property(property="message", type="string", example="Failed to Create Gift."),
     *              @OA\Property(property="code", type="number", example=500),
     *              @OA\Property(property="error", type="string", example="Something Wrong."),
     *          )
     *      ),
     *  )
     */
    public function store(CreateRequest $request)
    {
        try {
            canApiOrAbort('api-gifts-store');
            $data = $request->validated();
            $imageName = time().'.'.$request->image->extension();
            $request->image->storeAs('public/images/', $imageName);
            $data['image'] = 'images/'.$imageName;
            $gift = $this->gift->create($data);
            $gift = new GiftResource($gift);
            return $this->responseJson('created', 'Create Gift Successfully.', $gift, 201);
        } catch (\Throwable $th) {
            return $this->responseJson('error', 'Failed to Create Gift.', $th, 500);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/gifts/{id}",
     *      summary="Detail Gift",
     *      description="Get Detail Gift",
     *      tags={"Gifts"},
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
     *              @OA\Property(property="message", type="string", example="Gift Not Found."),
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
     *              @OA\Property(property="message", type="string", example="Failed to Get Detail Gift."),
     *              @OA\Property(property="code", type="number", example=500),
     *              @OA\Property(property="error", type="string", example="Something Wrong."),
     *          )
     *      ),
     * )
     */
    public function show(DetailRequest $request)
    {
        try {
            canApiOrAbort('api-gifts-show');
            $gift = $this->gift->find($request->id);
            if (!$gift) {
                return $this->responseJson('error', 'Gift Not Found', '', 404);
            }
            $gift = new GiftResource($gift);
            return $this->responseJson('success', 'Get Detail Gift Successfully.', $gift);
        } catch (\Throwable $th) {
            return $this->responseJson('error', 'Failed to Get Detail Gift.', $th, $th->getCode() ?? 500);
        }
    }

    /**
     * @OA\Patch(
     *      path="/api/v1/gifts/{id}",
     *      summary="Update Gift",
     *      description="Update Data Gift",
     *      tags={"Gifts"},
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
     *              @OA\Property(property="message", type="string", example="Gift Not Found."),
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
     *              @OA\Property(property="message", type="string", example="Failed to Update Data Gift."),
     *              @OA\Property(property="code", type="number", example=500),
     *              @OA\Property(property="error", type="string", example="Something Wrong."),
     *          )
     *      ),
     * )
     */
    public function update(UpdateRequest $request)
    {
        try {
            canApiOrAbort('api-gifts-update');
            $gift = $this->gift->find($request->id);
            if (!$gift) {
                return $this->responseJson('error', 'Gift Not Found', '', 404);
            }
            $data = $request->validated();
            if (!empty($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            }
            $gift->update($data);
            $gift = new GiftResource($this->gift->find($gift->id));
            return $this->responseJson('success', 'Update Gift Successfully.', $gift);
        } catch (\Throwable $th) {
            return $this->responseJson('error', 'Failed to Update Gift.', $th, $th->getCode() ?? 500);
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/gifts/{id}",
     *      summary="Delete Gift",
     *      description="Delete Data Gift",
     *      tags={"Gifts"},
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
     *              @OA\Property(property="message", type="string", example="Gift Not Found."),
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
     *              @OA\Property(property="message", type="string", example="Failed to Delete Gift."),
     *              @OA\Property(property="code", type="number", example=500),
     *              @OA\Property(property="error", type="string", example="Something Wrong."),
     *          )
     *      ),
     * )
     */
    public function destroy(DetailRequest $request)
    {
        try {
            canApiOrAbort('api-gifts-destroy');
            $gift = $this->gift->find($request->id);
            if (!$gift) {
                return $this->responseJson('error', 'Gift Not Found', '', 404);
            }
            $gift->delete();
            return $this->responseJson('deleted', 'Delete Gift Successfully.');
        } catch (\Throwable $th) {
            return $this->responseJson('error', 'Failed to Delete Gift.', $th, $th->getCode() ?? 500);
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/gifts/{id}/delete-permanent",
     *      summary="Delete Permanent Gift",
     *      description="Delete Permanent Data Gift",
     *      tags={"Gifts"},
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
     *              @OA\Property(property="message", type="string", example="Gift Not Found."),
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
     *              @OA\Property(property="message", type="string", example="Failed to Delete Gift."),
     *              @OA\Property(property="code", type="number", example=500),
     *              @OA\Property(property="error", type="string", example="Something Wrong."),
     *          )
     *      ),
     * )
     */
    public function deletePermanent(DetailRequest $request)
    {
        try {
            canApiOrAbort('api-gifts-destroy');
            $gift = $this->gift->withTrashed()->find($request->id);
            if (!$gift) {
                return $this->responseJson('error', 'Gift Not Found', '', 404);
            }
            $gift->forceDelete();
            return $this->responseJson('deleted', 'Delete Gift Successfully.');
        } catch (\Throwable $th) {
            return $this->responseJson('error', 'Failed to Delete Gift.', $th, $th->getCode() ?? 500);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/gifts/liked",
     *      summary="List Liked Gifts",
     *      description="Get List Liked Gifts",
     *      tags={"Gifts"},
     *      security={
     *          {"token": {}}
     *      },
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          description="Gift ID"
     *      ),
     *      @OA\Parameter(
     *          name="name",
     *          in="query",
     *          description="Name"
     *      ),
     *      @OA\Parameter(
     *          name="description",
     *          in="query",
     *          description="description"
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
     *              @OA\Property(property="message", type="string", example="Failed to Get List Liked Gifts."),
     *              @OA\Property(property="code", type="number", example=500),
     *              @OA\Property(property="error", type="string", example="Something Wrong."),
     *          )
     *      ),
     * )
     */
    public function liked(Request $request)
    {
        try {
            canApiOrAbort('api-gifts-like');
            $gifts = $this->giftRepository->searchLikesGifts($request);
            return $this->responseJson('pagination', 'Get List Liked Gifts Successfully.', $gifts, 200, [$request->sortBy, $request->sort]);
        } catch (\Throwable $th) {
            return $this->responseJson('error', 'Failed to Get List Liked Gifts.', $th, $th->getCode() ?? 500);
        }
    }
    
    /**
     * @OA\Post(
     *      path="/api/v1/gifts/{id}/like",
     *      summary="Like / Unlike Redeemed Gift",
     *      description="Like / Unlike Redeemed Gift",
     *      tags={"Gifts"},
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
     *          description="Ok",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Like Gift Successfully."),
     *              @OA\Property(property="status", type="boolean", example=true),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Gift Not Found."),
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
     *              @OA\Property(property="message", type="string", example="Failed to Like / Unlike Redeemed Gift."),
     *              @OA\Property(property="code", type="number", example=500),
     *              @OA\Property(property="error", type="string", example="Something Wrong."),
     *          )
     *      ),
     * )
     */
    public function like(DetailRequest $request)
    {
        try {
            canApiOrAbort('api-gifts-like');
            $gift = $this->gift->find($request->id);
            if (!$gift) {
                return $this->responseJson('error', 'Gift Not Found', '', 404);
            }
            $userId = auth()->id();
            // Check Redeem
            $order = $gift->orders()->whereUserId($userId)->first();
            if (!$order) {
                return $this->responseJson('error', "You haven't redeem this gift", '', 400);
            }
            $like = $gift->likes()->whereUserId($userId)->first();
            if ($like) {
                $like->delete();
                $message = 'Unlike Gift Successfully';
            } else {
                $data = [
                    'gift_id' => $gift->id,
                    'user_id' => $userId,
                ];
                $this->giftLike->create($data);
                $message = 'Like Gift Successfully';
            }
            return $this->responseJson('success', $message);
        } catch (\Throwable $th) {
            return $this->responseJson('error', 'Failed to Like / Unlike Redeemed Gift.', $th, $th->getCode() ?? 500);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/gifts/rated",
     *      summary="List Rated Gifts",
     *      description="Get List Rated Gifts",
     *      tags={"Gifts"},
     *      security={
     *          {"token": {}}
     *      },
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          description="Gift ID"
     *      ),
     *      @OA\Parameter(
     *          name="name",
     *          in="query",
     *          description="Name"
     *      ),
     *      @OA\Parameter(
     *          name="description",
     *          in="query",
     *          description="description"
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
     *              @OA\Property(property="message", type="string", example="Failed to Get List Rated Gifts."),
     *              @OA\Property(property="code", type="number", example=500),
     *              @OA\Property(property="error", type="string", example="Something Wrong."),
     *          )
     *      ),
     * )
     */
    public function rated(Request $request)
    {
        try {
            canApiOrAbort('api-gifts-rating');
            $gifts = $this->giftRepository->searchRatingsGifts($request);
            return $this->responseJson('pagination', 'Get List Rated Gifts Successfully.', $gifts, 200, [$request->sortBy, $request->sort]);
        } catch (\Throwable $th) {
            return $this->responseJson('error', 'Failed to Get List Rated Gifts.', $th, $th->getCode() ?? 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/v1/gifts/{id}/rating",
     *      summary="Rating Redeemed Gift",
     *      description="Give Rating Redeemed Gift",
     *      tags={"Gifts"},
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
     *              required={"rating"},
     *              @OA\Property(property="rating", type="number", example=1.8),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Ok",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Rating Gift Successfully."),
     *              @OA\Property(property="status", type="boolean", example=true),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Gift Not Found."),
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
     *              @OA\Property(property="message", type="string", example="Failed to Rating Redeemed Gift."),
     *              @OA\Property(property="code", type="number", example=500),
     *              @OA\Property(property="error", type="string", example="Something Wrong."),
     *          )
     *      ),
     * )
     */
    public function rating(RatingRequest $request)
    {
        try {
            canApiOrAbort('api-gifts-rating');
            $gift = $this->gift->find($request->id);
            if (!$gift) {
                return $this->responseJson('error', 'Gift Not Found', '', 404);
            }
            $data = $request->validated();
            $userId = auth()->id();
            // Check Redeem
            $order = $gift->orders()->whereUserId($userId)->first();
            if (!$order) {
                return $this->responseJson('error', "You haven't redeem this gift", '', 400);
            }
            $rating = $gift->ratings()->whereUserId($userId)->first();
            if ($rating) {
                $rating->update($data);
                $message = 'Update Gift Rating Successfully';
            } else {
                $data = [
                    'gift_id' => $gift->id,
                    'user_id' => $userId,
                    'rating' => $data['rating']
                ];
                $this->giftRating->create($data);
                $message = 'Rating Gift Successfully';
            }
            return $this->responseJson('success', $message);
        } catch (\Throwable $th) {
            return $this->responseJson('error', 'Failed to Rating Redeemed Gift.', $th, $th->getCode() ?? 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/v1/gifts/{id}/redeem",
     *      summary="Redeem Gift",
     *      description="Redeem Gift",
     *      tags={"Gifts"},
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
     *              required={"qty"},
     *              @OA\Property(property="qty", type="number", example=1),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Created",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Redeem Gift Successfully."),
     *              @OA\Property(property="status", type="boolean", example=true),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Gift Not Found."),
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
     *              @OA\Property(property="message", type="string", example="Failed to Redeem Gift."),
     *              @OA\Property(property="code", type="number", example=500),
     *              @OA\Property(property="error", type="string", example="Something Wrong."),
     *          )
     *      ),
     * )
     */
    public function redeem(RedeemRequest $request)
    {
        try {
            canApiOrAbort('api-gifts-redeem');
            $user = auth()->user();
            $gift = $this->gift->find($request->id);
            if (!$gift) {
                return $this->responseJson('error', 'Gift Not Found', '', 404);
            } elseif (($gift->stock - $request->qty) < 0) { // Check Stock
                return $this->responseJson('error', 'Out of Stock', '', 400);
            } elseif (($user->point - ($gift->point * $request->qty)) < 0) { // Check User Point
                return $this->responseJson('error', 'User point not enough', '', 400);
            }
            $this->giftRepository->redeem($gift->id, $request->qty);
            return $this->responseJson('created', 'Redeem Gift Successfully');
        } catch (\Throwable $th) {
            return $this->responseJson('error', 'Failed to Redeem Gift.', $th, $th->getCode() ?? 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/v1/gifts/redeem",
     *      summary="Redeem Multiple Gift",
     *      description="Redeem Multiple Gift",
     *      tags={"Gifts"},
     *      security={
     *          {"token": {}}
     *      },
     *      @OA\RequestBody(
     *          required=true,
     *          description="Pass user data",
     *          @OA\JsonContent(
     *              required={"ids"},
     *              @OA\Property(
     *                   property="ids",
     *                   type="array",
     *                   @OA\Items(
     *                       example=1,
     *                   ),
     *                   description="Gift IDs"
     *               ),
     *              @OA\Property(
     *                   property="qtys",
     *                   type="array",
     *                   @OA\Items(
     *                       example=1,
     *                   ),
     *                   description="Qty per Gift ID"
     *               ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Created",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Redeem Multiple Gift Successfully."),
     *              @OA\Property(property="status", type="boolean", example=true),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Gift Not Found."),
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
     *              @OA\Property(property="message", type="string", example="Failed to Redeem Multiple Gift."),
     *              @OA\Property(property="code", type="number", example=500),
     *              @OA\Property(property="error", type="string", example="Something Wrong."),
     *          )
     *      ),
     * )
     */
    public function redeems(RedeemsRequest $request)
    {
        try {
            canApiOrAbort('api-gifts-redeem');
            $data = $request->validated();
            $user = auth()->user();
            $totalPoint = 0;
            $gifts = $this->gift->whereIn('id', $data['ids'])->get();
            
            // Check IDS
            if (count($data['ids']) != $gifts->count()) {
                return $this->responseJson('error', 'Gift Not Found. Please Check Gift ID', '', 404);
            }

            // Check Gifts Stocks
            foreach ($gifts as $gift) {
                $index = array_search($gift->id, $data['ids']);
                $qty = $data['qty'][$index];
                if (($gift->stock - $qty) < 0) {
                    return $this->responseJson('error', "{$gift->name} Out of Stock", '', 400);
                }
                $totalPoint += ($gift->point * $qty);
            }

            if (($user->point - $totalPoint) < 0) {
                return $this->responseJson('error', 'User point not enough', '', 400);
            }

            $this->giftRepository->redeem($data['ids'], $data['qtys']);
            return $this->responseJson('created', 'Redeem Multiple Gift Successfully');
        } catch (\Throwable $th) {
            return $this->responseJson('error', 'Failed to Redeem Multiple Gift.', $th, $th->getCode() ?? 500);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/gifts/redeem",
     *      summary="List Redeemed Gifts",
     *      description="Get List Redeemed Gifts",
     *      tags={"Gifts"},
     *      security={
     *          {"token": {}}
     *      },
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
     *              @OA\Property(property="message", type="string", example="Failed to List Redeemed Gifts."),
     *              @OA\Property(property="code", type="number", example=500),
     *              @OA\Property(property="error", type="string", example="Something Wrong."),
     *          )
     *      ),
     * )
     */
    public function orders(Request $request)
    {
        try {
            canApiOrAbort('api-gifts-redeem');
            $orders = $this->giftRepository->searchOrders($request);
            return $this->responseJson('pagination', 'Get List Redeemed Gifts Successfully.', $orders, 200, [$request->sortBy, $request->sort]);
        } catch (\Throwable $th) {
            return $this->responseJson('error', 'Failed to List Redeemed Gifts.', $th, $th->getCode() ?? 500);
        }
    }
}
