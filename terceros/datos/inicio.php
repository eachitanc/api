<?php

require '../../vendor/autoload.php';

$config = ["settings" => ["displayErrorDetails" => true]];
$app = new \Slim\App($config);

require 'src/rutas/opciones_terceros.php';

$app->run();
