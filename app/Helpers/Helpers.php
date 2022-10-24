<?php


function customResponse($code, $message, $data, $isSuccess, $numberOfRows = null)
{
    $payload = [
        'code' => $code,
        'message' => $message,
        'data' => $data,
        'success' => $isSuccess,
        'rows'  => $numberOfRows,
    ];

    return response($payload, $code);
}

