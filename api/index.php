<?php
include_once '../librerias/router.php';
include_once '../librerias/responseRequest.php';
include_once '../librerias/validateDataTypes.php';
include_once '../config/config.php';
include_once '../controllers/Controller.php';
include_once '../controllers/Login.php';
// Dasdasdasdasdasdasdasd
$Router = new Router();


//consultar
$Router->post('/login', function ($req) {
    $Login = new Login();
    $user = $Login->Login($req->body);
    responseRequest($user->statusCode, $user->message, true, $user->data);
});


$Router->dafault(function () {
    responseRequest(
        404,
        'Intenta con otra ruta',
        true//era True
    );
});
