<?php
/**
 * Created by PhpStorm.
 * @author liangw
 * @Date: 2018/6/6
 * @Time: 20:04
 */

// This sample uses the Apache HTTP client from HTTP Components (http://hc.apache.org/httpcomponents-client-ga/)
require_once 'HTTP/Request2.php';

$request = new Http_Request2('https://southcentralus.api.cognitive.microsoft.com/customvision/v2.0/Prediction/{projectId}/image');
$url = $request->getUrl();

$headers = array(
    // Request headers
    'Prediction-Key' => '',
    'Content-Type' => 'multipart/form-data',
    'Prediction-key' => '{subscription key}',
);

$request->setHeader($headers);

$parameters = array(
    // Request parameters
    'iterationId' => '{string}',
    'application' => '{string}',
);

$url->setQueryVariables($parameters);

$request->setMethod(HTTP_Request2::METHOD_POST);

// Request body
$request->setBody("{body}");

try
{
    $response = $request->send();
    echo $response->getBody();
}
catch (HttpException $ex)
{
    echo $ex;
}

?>