<?php

declare(strict_types=1);

use Apex\Api;
use Apex\Response;
use Apex\Validator;

require_once __DIR__ . '/../vendor/autoload.php';
$config = require_once __DIR__ . '/../config/parameters.php';

Sentry\init(['dsn' => $config['sentry_key']]);

$response = new Response();

try {
    $validator = new Validator($_GET);
    $validator->check();

    $api  = new Api($validator);
    $data = $api->fetchData();

    echo $response->returnData($data);
} catch (Exception $e) {
    echo $response->returnError($e->getMessage());
}
