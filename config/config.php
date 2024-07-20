<?php
header('Content-Type: application/json');


// Definir el control de acceso
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type,Authorization');


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  die();
}

include_once('../config/conConfig.php');
include_once('../config/database.php');