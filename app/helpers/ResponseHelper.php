<?php
class ResponseHelper
{
    /**
     * Format response JSON yang konsisten
     * 
     * @param int $status HTTP status code
     * @param string $message Pesan response
     * @param array $data Data hasil
     * @param array $meta Metadata tambahan (pagination, etc)
     * @return void
     */
    public static function json($status = 200, $message = '', $data = [], $meta = [])
    {
        http_response_code($status);
        header('Content-Type: application/json');

        $response = [
            'status' => $status,
            'message' => $message,
            'result' => $data
        ];

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Success response (200)
     */
    public static function success($data = [], $message = 'Success', $meta = [])
    {
        self::json(200, $message, $data, $meta);
    }

    /**
     * Success dengan data kosong (204)
     */
    public static function successNoContent($message = 'No data found')
    {
        self::json(200, $message, []);
    }

    /**
     * Created response (201)
     */
    public static function created($data = [], $message = 'Resource created successfully')
    {
        self::json(201, $message, $data);
    }

    /**
     * Bad request (400)
     */
    public static function badRequest($message = 'Bad request', $errors = [])
    {
        $data = !empty($errors) ? ['errors' => $errors] : [];
        self::json(400, $message, $data);
    }

    public static function unauthorized($message = 'Unauthorized access')
    {
        self::json(401, $message);
    }

    public static function forbidden($message = 'Access forbidden')
    {
        self::json(403, $message);
    }

    public static function notFound($message = 'Resource not found')
    {
        self::json(404, $message);
    }

    public static function validationError($errors = [], $message = 'Validation failed')
    {
        self::json(422, $message, ['errors' => $errors]);
    }

    public static function serverError($message = 'Internal server error')
    {
        self::json(500, $message);
    }

    public static function paginate($data = [], $total = 0, $page = 1, $limit = 20, $message = 'Success')
    {
        $totalPages = ceil($total / $limit);

        $meta = [
            'pagination' => [
                'total' => (int)$total,
                'count' => count($data),
                'per_page' => (int)$limit,
                'current_page' => (int)$page,
                'total_pages' => $totalPages,
                'links' => [
                    'next_page' => $page < $totalPages ? $page + 1 : null,
                    'prev_page' => $page > 1 ? $page - 1 : null,
                    'first_page' => 1,
                    'last_page' => $totalPages
                ]
            ]
        ];

        self::json(200, $message, $data, $meta);
    }
}
