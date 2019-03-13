<?php

use Apex\Api;
use Apex\Response;
use Apex\Validator;

require_once __DIR__ . '/../vendor/autoload.php';

$response = new Response();

try {
    $validator = new Validator($_GET);
    $validator->check();

    $api  = new Api($validator);
    $data = $api->fetchData();

    echo $response->returnData($validator, $data);
} catch (Exception $e) {
    echo $response->returnError($e->getMessage());
}
