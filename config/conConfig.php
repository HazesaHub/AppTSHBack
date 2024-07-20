<?php
$servername = getenv("BD_servername");
$username = getenv("BD_username");
$password = getenv("BD_password");
$dbname = getenv("BD_dbname");

$base_url = __DIR__ ."/../../";
// definir cariables

define("DB_SERVERNAME", $servername);
define("DB_USERNAME", $username);
define("DB_PASSWORD", $password);
define("DB_NAME", $dbname);
define("Base_URL", $base_url);