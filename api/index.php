<?php

date_default_timezone_set('America/Mexico_City');
include_once '../librerias/router.php';
include_once '../librerias/responseRequest.php';
include_once '../librerias/validateDataTypes.php';
include_once '../config/config.php';
include_once '../controllers/Controller.php';
include_once '../controllers/Login.php';
include_once '../controllers/Token.php';
require '../vendor/autoload.php';
// Definición de la variable global
global $token;
$token = null;

// Obtener el token de la cabecera Authorization si está presente
if (isset($_SERVER['HTTP_AUTHORIZATION']) && $_SERVER['HTTP_AUTHORIZATION'] !== '') {
    $token = substr($_SERVER['HTTP_AUTHORIZATION'], 7); // Asumiendo el formato "Bearer <token>"
}

// Dasdasdasdasdasdasdasd
$Router = new Router();

$Router->post('/login', function ($req) {
    $Login = new Login();
    $user = $Login->Login($req->body);
    responseRequest($user->statusCode,$user->error, $user->message, true, $user->data);
});

$Router->post('/loginSecondary', function ($req) {
    $Login = new Login();
    $user = $Login->LoginSecondary($req->body);
    responseRequest($user->statusCode, $user->error, $user->message, true, $user->data);
});

$Router->get('/Autentication', function () {
    global $token; // Acceder a la variable global aquí
    if($token == null){
        responseRequest(401, true, 'Token no enviado', true);
    }
    $tokenC = new Token($token);
    $dataToken = $tokenC->ReadToken();
    responseRequest($dataToken->statusCode, $dataToken->error, $dataToken->message, true, $dataToken->data);
});


$Router->dafault(function () {
    responseRequest(
        404,
        true,
        'Intenta con otra ruta',
        true
    );
});
