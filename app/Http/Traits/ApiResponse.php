<?php

namespace App\Http\Traits;

trait ApiResponse

{



    // success response for successful data founded
    protected function successResponse($data, $status = 200, $message = "Data Founded Successfully")
    {
        return response()->json([
            'data' => $data,
            'message' => $message
        ], $status);
    }


    // success pagination response for successful data founded
    protected function paginationResponse($data, $status = 200, $message = "")
    {
        return response()->json([
            'data' => $data->items(),
            'pagination' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'next_page_url' => $data->nextPageUrl(),
                'prev_page_url' => $data->previousPageUrl(),
            ],
            'message' => $message,
        ], $status);
    }




    // success response for no content
    protected function noContentResponse()
    {
        return response()->json(null, 404);
    }



    // success response for no model founded
    protected function notFoundResponse($message = "Resource not found")
    {
        return response()->json([
            'status' => false,
            'message' => $message,
        ], 404);
    }



    // error response for failed request
    protected function errorResponse($message, $status = 500)
    {
        return response()->json([
            'message' => $message,
        ], $status);
    }
}
