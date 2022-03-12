<?php
namespace App\Cores;

trait ApiResponse
{
    /**
     * Renponse Variables
     * @var array
     */
    public $response = [
        'success' => [
            'data' => [
                'data' => []
            ],
            'code' => 200
        ],
        'created' => [
            'data' => [
                'data' => []
            ],
            'code' => 201
        ],
        'updated' => [
            'data' => [
                'data' => []
            ],
            'code' => 200
        ],
        'error' => [
            'data' => [
                'message' => 'Something Wrong.',
                'code' => 400
            ],
            'code' => 400
        ],
        'unauth' => [
            'data' => [
                'status' => false,
                'message' => 'Something Wrong.'
            ],
            'code' => 401
        ],
        'deleted' => [
            'data' => [
                'data' => []
            ],
            'code' => 204
        ],
        'pagination' => [
            'data' => [
                'data' => []
            ],
            'code' => 200
        ],
        'default' => [
            'data' => [
                'status' => true,
                'message' => 'OK',
                'data' => []
            ],
            'code' => 200
        ]
    ];

    /**
     * Renponse Json
     * @param String $type
     * @param String $message
     * @param Array/Object $data
     * @param String/Int $code
     * @param String/Int $sort
     * @return Json
     * @return Json
     */
    public function responseJson($type = 'default', $message = '', $data = [], $code = '', $sort = [])
    {
        switch ($type) {
            case 'success':
                $response = $this->response[$type];
                $response['data'] = $data;
                break;
            case 'created':
                $response = $this->response[$type];
                $response['data'] = $data;
                break;
            case 'updated':
                $response = $this->response[$type];
                $response['data'] = $data;
                break;
            case 'error':
                $response = $this->response[$type];
                $th = $data;
                if (!empty($th)) {
                    try {
                        $response['data']['error'] = $th->getMessage() ?? '';
                    } catch (\Throwable $th) {
                        $response['data']['error'] = '';
                    }
                }
                if (!empty($message)) {
                    $response['data']['message'] = $message;
                }
                if (!empty($code)) {
                    $response['data']['code'] = $code;
                }
                break;
            case 'unauth':
                $response = $this->response[$type];
                break;
            case 'deleted':
                $response = $this->response[$type];
                $response['data'] = $data;
                break;
            case 'pagination':
                $response = $this->response[$type];
                $response['data']['data'] = $data;
                $response['data']['pagination'] = [
                    'total' => $data->total(),
                    'totalPage' => $data->lastPage(),
                    'page' => $data->currentPage(),
                    'sort' => $sort[1] ?? null,
                    'sortBy' => $sort[0] ?? null,
                    'limit' => $data->count(),
                ];
                break;
            default:
                $response = $this->response['default'];
                if (!empty($message)) {
                    $response['data']['message'] = $message;
                }
                if (!empty($data)) {
                    $response['data']['data'] = $data;
                } else {
                    unset($response['data']['data']);
                }
                break;
        }

        if (!empty($code)) {
            $response['code'] = $code;
        }

        if (empty($data)) {
            $response['data']['message'] = $message;
            $response['data']['status'] = in_array($response['code'], [200, 201]) ? true : false;
        }

        return response()->json($response['data'], $response['code']);
    }
}
