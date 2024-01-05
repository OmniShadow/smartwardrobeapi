<?php
class BaseController
{
    public const AVAILABLE_METHODS = [];
    public function _call($name, $arguments)
    {
        $this->sendOutput('', array('HTTP/1.1 404 Not Found'));
    }

    protected function getUriSegments()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode('/', $uri);

        return $uri;
    }

    protected function getQueryStringParams()
    {
        $queryParams = array();
        parse_str($_SERVER['QUERY_STRING'], $queryParams);
        return $queryParams;
    }

    protected function buildResponse($data, $error)
    {
        $responseData = array(
            'data' => $data,
            'error' => $error,
        );

        return $responseData;
    }

    protected function sendOutput($data, $httpHeaders = array())
    {
        header_remove('Set-Cookie');
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: POST, OPTIONS, DELETE, GET");
        header("Access-Control-Allow-Headers: Origin,Content-Type,X-Amz-Date,Authorization,X-Api-Key,X-Amz-Security-Token,locale");

        if (is_array($httpHeaders) && count($httpHeaders)) {
            foreach ($httpHeaders as $httpHeader) {
                header($httpHeader);
            }
        }

        echo $data;
        exit;
    }
}

function isJsonRequest()
{
    // Get all headers
    $headers = getallheaders();

    // Check if "Content-Type" header is set and contains "application/json"
    if (isset($headers['Content-Type']) && stripos($headers['Content-Type'], 'application/json') !== false) {
        return true;
    }

    return false;
}