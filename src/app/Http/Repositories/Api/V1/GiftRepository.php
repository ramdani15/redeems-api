<?php

namespace App\Repositories\Api\V1;

use App\Traits\ApiFilterTrait;
use App\Repositories\ModelRepository;
use App\Models\Gift;
use App\Models\Order;
use App\Http\Resources\Api\V1\GiftResource;
use App\Http\Resources\Api\V1\OrderResource;

class GiftRepository extends ModelRepository
{
    use ApiFilterTrait;

    public function __construct(
        Gift $gift,
        Order $order
    ) {
        $this->gift = $gift;
        $this->order = $order;
    }

    /**
     * Search and Filter Gifts
     * @param Illuminate\Http\Request $request
     * @return App\Http\Resources\Api\V1\GiftResource
     */
    public function searchGifts($request)
    {
        $limit = $request->limit ?? 10;
        $data = $this->gift;
        $filters = [
            [
                'field' => 'name',
                'value' => $request->name,
                'query' => 'like'
            ],
            [
                'field' => 'description',
                'value' => $request->description,
                'query' => 'like'
            ],
        ];
        $data = $this->filterFields($data, $filters);
        $data = $this->setOrder($data, [$request->sortBy, $request->sort]);
        $data = $data->paginate($limit);
        return GiftResource::collection($data);
    }

    /**
     * Search and Filter Likes Gifts
     * @param Illuminate\Http\Request $request
     * @return App\Http\Resources\Api\V1\GiftResource
     */
    public function searchLikesGifts($request)
    {
        $limit = $request->limit ?? 10;
        $data = $this->gift->whereHas('likes', function ($q) {
            $q->whereUserId(auth()->id());
        });
        $filters = [
            [
                'field' => 'id',
                'value' => $request->id,
            ],
            [
                'field' => 'name',
                'value' => $request->name,
                'query' => 'like'
            ],
            [
                'field' => 'description',
                'value' => $request->description,
                'query' => 'like'
            ],
        ];
        $data = $this->filterFields($data, $filters);
        $data = $this->setOrder($data, [$request->sortBy, $request->sort]);
        $data = $data->paginate($limit);
        return GiftResource::collection($data);
    }

    /**
     * Search and Filter Ratings Gifts
     * @param Illuminate\Http\Request $request
     * @return App\Http\Resources\Api\V1\GiftResource
     */
    public function searchRatingsGifts($request)
    {
        $limit = $request->limit ?? 10;
        $data = $this->gift->whereHas('ratings', function ($q) {
            $q->whereUserId(auth()->id());
        });
        $filters = [
            [
                'field' => 'id',
                'value' => $request->id,
            ],
            [
                'field' => 'name',
                'value' => $request->name,
                'query' => 'like'
            ],
            [
                'field' => 'description',
                'value' => $request->description,
                'query' => 'like'
            ],
        ];
        $data = $this->filterFields($data, $filters);
        $data = $this->setOrder($data, [$request->sortBy, $request->sort]);
        $data = $data->paginate($limit);
        return GiftResource::collection($data);
    }

    /**
     * Redeem Gift
     * @param array|int $ids
     * @param array|int $qtys
     * @return boolean
     */
    public function redeem($ids, $qtys)
    {
        $user = auth()->user();
        if (!is_array($ids)) {
            $ids = (array) $ids;
        }
        if (!is_array($qtys)) {
            $qtys = (array) $qtys;
        }
        $gifts = $this->gift->whereIn('id', $ids)->get();
        $data = [];
        $totalPoint = 0;
        foreach ($gifts as $gift) {
            $index = array_search($gift->id, $ids);
            $qty = $qtys[$index];
            $point = $gift->point * $qty;
            $tmp = [
                'user_id' => $user->id,
                'attachable_type' => 'Gift',
                'attachable_id' => $gift->id,
                'qty' => $qty,
                'point' => $point,
                'status' => 'ordered',
                'created_at' => time(),
                'updated_at' => time(),
            ];
            $data[] = $tmp;
            $totalPoint += $point;

            // Reduce Gift Stocks
            $gift->total_purchases += $qty;
            $gift->stock -= $qty;
            $gift->save();
        }
        $this->order->insert($data);

        // Reduce User Point
        $user->point -= $totalPoint;
        $user->save();
        return true;
    }

    /**
     * Search and Filter Orders
     * @param Illuminate\Http\Request $request
     * @return App\Http\Resources\Api\V1\GiftResource
     */
    public function searchOrders($request)
    {
        $limit = $request->limit ?? 10;
        $data = $this->order->whereUserId(auth()->id());
        $filters = [
            [
                'field' => 'name',
                'value' => $request->name,
                'query' => 'relation-like',
                'relation' => 'attachable',
            ],
            [
                'field' => 'description',
                'value' => $request->description,
                'query' => 'relation-like',
                'relation' => 'attachable',
            ],
        ];
        $data = $this->filterFields($data, $filters);
        $data = $this->setOrder($data, [$request->sortBy, $request->sort]);
        $data = $data->paginate($limit);
        return OrderResource::collection($data);
    }
}
