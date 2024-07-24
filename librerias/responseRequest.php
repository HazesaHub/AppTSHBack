<?php

function responseRequest(int $code, bool $error, string $message, bool $finishConnection = false, array $data = []){
    http_response_code($code);
    echo json_encode(["status"=> $code, "error" => $error, "message" => $message, "data" => $data]);
    if($finishConnection){
        die();
    }
}
?>