<?php
class DrawerController extends BaseController
{
    private $model;

    public function __construct()
    {
        $this->model = new DrawerModel();
    }

    public function resource()
    {
        $strErrorDesc = '';
        $requestMethod = strtoupper($_SERVER["REQUEST_METHOD"]);
        $responseData = array();
        $responseHeader = '';
        $result = array();
        $uriSegments = $this->getUriSegments();

        switch ($requestMethod) {
            default:
                $strErrorDesc = 'Method not supported';
                $responseHeader = 'HTTP/1.1 422 Unprocessable Entity';
                $responseData = $this->buildResponse([], $strErrorDesc);
                break;
            case 'GET':
                try {

                    $lastSegment = $uriSegments[4];
                    $id = "";
                    if (!empty($lastSegment)) {
                        $id = $lastSegment;
                    }
                    $result = $this->model->getDrawerData($id);
                    $responseHeader = 'HTTP/1.1 200 OK';

                } catch (Error $e) {

                    $strErrorDesc = $e->getMessage() . 'Something went wrong!';

                    $responseHeader = 'HTTP/1.1 500 Internal Server Error';
                }
                break;
        }
        $responseData = $this->buildResponse($result, $strErrorDesc);
        $this->sendOutput(
            json_encode($responseData),
            array('Content-Type: application/json', $responseHeader)
        );

    }
    public function list()
    {
        $requestMethod = strtoupper($_SERVER["REQUEST_METHOD"]);
        $responseData = array();
        $responseHeader = 'HTTP/1.1 200 OK';
        $strErrorDesc = '';
        $result = [];

        switch ($requestMethod) {
            default:
                $strErrorDesc = 'Method not supported';
                $responseHeader = 'HTTP/1.1 422 Unprocessable Entity';
                break;
            case 'GET':
                try {

                    $result = $this->model->listDrawers();
                    $responseData = $this->buildResponse($result, $strErrorDesc);

                } catch (Error $e) {

                    $strErrorDesc = $e->getMessage() . 'Something went wrong!';

                    $responseHeader = 'HTTP/1.1 500 Internal Server Error';
                }
                break;
        }
        $responseData = $this->buildResponse($result, $strErrorDesc);
        $this->sendOutput(
            json_encode($responseData),
            array('Content-Type: application/json', $responseHeader)
        );

    }

    public function controller()
    {

        $requestMethod = strtoupper($_SERVER["REQUEST_METHOD"]);
        $responseData = array();
        $responseHeader = 'HTTP/1.1 200 OK';
        $strErrorDesc = '';
        $result = array();
        $uriSegments = $this->getUriSegments();

        switch ($requestMethod) {
            default:
                $strErrorDesc = 'Method not supported';
                $responseHeader = 'HTTP/1.1 422 Unprocessable Entity';
                $responseData = $this->buildResponse([], $strErrorDesc);
                break;
            case 'GET':
                try {
                    
                    $lastSegment = $uriSegments[5];
                    if ($lastSegment == 'list') {
                        $result = $this->model->listControllers();
                        $responseHeader = 'HTTP/1.1 200 OK';
                    } else {
                        $id = "";

                        if (!empty($lastSegment)) {
                            $id = $lastSegment;
                        }
                        $result = $this->model->getControllerData($id);
                        $responseHeader = 'HTTP/1.1 200 OK';
                    }


                } catch (Error $e) {

                    $strErrorDesc = $e->getMessage() . 'Something went wrong!';

                    $responseHeader = 'HTTP/1.1 500 Internal Server Error';
                }
                break;
            case 'POST':

                $json = file_get_contents('php://input');
                $data = json_decode($json, true);

                try {
                    $lastSegment = $uriSegments[5];
                    $operation = "";

                    if (!empty($lastSegment)) {
                        $operation = $lastSegment;
                    }
                    switch ($operation) {
                        case 'insert':
                            $result = $this->model->insertControllerData($data);
                            break;
                        case 'update':
                            $result = $this->model->updateControllerData($data);
                            break;
                    }


                } catch (Error $e) {

                    $strErrorDesc = $e->getMessage() . 'Something went wrong!';
                    $responseHeader = 'HTTP/1.1 500 Internal Server Error';
                }
                break;
        }

        $responseData = $this->buildResponse($result, $strErrorDesc);

        $this->sendOutput(
            json_encode($responseData),
            array('Content-Type: application/json', $responseHeader)
        );
    }

    public function insert()
    {

        $requestMethod = strtoupper($_SERVER["REQUEST_METHOD"]);
        $responseData = array();
        $responseHeader = 'HTTP/1.1 200 OK';
        $strErrorDesc = '';
        $result = array();

        switch ($requestMethod) {
            default:
                $strErrorDesc = 'Method not supported';
                $responseHeader = 'HTTP/1.1 422 Unprocessable Entity';
                break;
            case 'POST':
                try {
                    $json = file_get_contents('php://input');
                    $data = json_decode($json, true);
                    $result = $this->model->insertDrawerData($data);

                } catch (Error $e) {
                    $strErrorDesc = $e->getMessage() . 'Something went wrong!';
                    $responseHeader = 'HTTP/1.1 500 Internal Server Error';
                }
                break;
        }
        $responseData = $this->buildResponse($result, $strErrorDesc);
        $this->sendOutput(
            json_encode($responseData),
            array('Content-Type: application/json', $responseHeader)
        );

    }
    public function insertclothing()
    {

        $requestMethod = strtoupper($_SERVER["REQUEST_METHOD"]);
        $responseData = array();
        $responseHeader = 'HTTP/1.1 200 OK';
        $strErrorDesc = '';
        $result = array();

        switch ($requestMethod) {
            default:
                $strErrorDesc = 'Method not supported';
                $responseHeader = 'HTTP/1.1 422 Unprocessable Entity';
                break;
            case 'POST':
                try {
                    $json = file_get_contents('php://input');
                    $data = json_decode($json, true);
                    $result = $this->model->insertClothingData($data);

                } catch (Error $e) {
                    $strErrorDesc = $e->getMessage() . 'Something went wrong!';
                    $responseHeader = 'HTTP/1.1 500 Internal Server Error';
                }
                break;
        }
        $responseData = $this->buildResponse($result, $strErrorDesc);
        $this->sendOutput(
            json_encode($responseData),
            array('Content-Type: application/json', $responseHeader)
        );

    }
    public function delete()
    {

        $requestMethod = strtoupper($_SERVER["REQUEST_METHOD"]);
        $responseData = array();
        $responseHeader = 'HTTP/1.1 200 OK';
        $strErrorDesc = '';
        $result = array();

        switch ($requestMethod) {
            default:
                $strErrorDesc = 'Method not supported';
                $responseHeader = 'HTTP/1.1 422 Unprocessable Entity';
                break;
            case 'POST':
                try {
                    $json = file_get_contents('php://input');
                    $data = json_decode($json, true);
                    $result = $this->model->deleteDrawerData($data);

                } catch (Error $e) {
                    $strErrorDesc = $e->getMessage() . 'Something went wrong!';
                    $responseHeader = 'HTTP/1.1 500 Internal Server Error';
                }
                break;
        }
        $responseData = $this->buildResponse($result, $strErrorDesc);
        $this->sendOutput(
            json_encode($responseData),
            array('Content-Type: application/json', $responseHeader)
        );

    }

    public function update()
    {

        $requestMethod = strtoupper($_SERVER["REQUEST_METHOD"]);
        $responseData = array();
        $responseHeader = 'HTTP/1.1 200 OK';
        $strErrorDesc = '';
        $result = array();

        switch ($requestMethod) {
            default:
                $strErrorDesc = 'Method not supported';
                $responseHeader = 'HTTP/1.1 422 Unprocessable Entity';
                break;
            case 'POST':
                try {
                    $json = file_get_contents('php://input');
                    $data = json_decode($json, true);
                    $result = $this->model->updateDrawerStatus($data);

                } catch (Error $e) {
                    $strErrorDesc = $e->getMessage() . 'Something went wrong!';
                    $responseHeader = 'HTTP/1.1 500 Internal Server Error';
                }
                break;
        }
        $responseData = $this->buildResponse($result, $strErrorDesc);
        $this->sendOutput(
            json_encode($responseData),
            array('Content-Type: application/json', $responseHeader)
        );

    }
}