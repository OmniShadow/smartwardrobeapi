<?php
class OutfitController extends BaseController
{
    private $model;

    public function __construct()
    {
        $this->model = new OutfitModel();
    }

    public function resource()
    {
        $strErrorDesc = '';
        $requestMethod = strtoupper($_SERVER["REQUEST_METHOD"]);
        $responseData = array();
        $responseHeader = '';
        $outfit = array();
        $uriSegments = $this->getUriSegments();

        switch ($requestMethod) {
            default:
                $strErrorDesc = 'Method not supported';
                $responseHeader = 'HTTP/1.1 422 Unprocessable Entity';
                break;
            case 'GET':
                try {
                    $lastSegment = $uriSegments[4];
                    $id = "";
                    if (!empty($lastSegment)) {
                        $id = $lastSegment;
                    }
                    $outfit = $this->model->get($id);
                    $responseHeader = 'HTTP/1.1 200 OK';

                } catch (Error $e) {

                    $strErrorDesc = $e->getMessage() . 'Something went wrong!';

                    $responseHeader = 'HTTP/1.1 500 Internal Server Error';
                }
                break;
        }
        $responseData = $this->buildResponse($outfit, $strErrorDesc);
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
        $outfitList = [];

        switch ($requestMethod) {
            default:
                $strErrorDesc = 'Method not supported';
                $responseHeader = 'HTTP/1.1 422 Unprocessable Entity';
                break;
            case 'GET':
                try {
                    $outfitList = $this->model->list();

                } catch (Error $e) {

                    $strErrorDesc = $e->getMessage() . 'Something went wrong!';
                    $responseHeader = 'HTTP/1.1 500 Internal Server Error';
                }
                break;
        }
        $responseData = $this->buildResponse($outfitList, $strErrorDesc);
        $this->sendOutput(
            json_encode($responseData),
            array('Content-Type: application/json', $responseHeader)
        );

    }

    public function schema()
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
                    $result = $this->model->getSchema();

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

    public function search()
    {

        $requestMethod = strtoupper($_SERVER["REQUEST_METHOD"]);
        $arrQueryStringParams = $this->getQueryStringParams();
        $responseData = array();
        $responseHeader = 'HTTP/1.1 200 OK';
        $strErrorDesc = '';
        $outfitList = [];

        switch ($requestMethod) {
            default:
                $strErrorDesc = 'Method not supported';
                $responseHeader = 'HTTP/1.1 422 Unprocessable Entity';
                break;
            case 'GET':
                try {
                    $q = "";

                    if (isset($arrQueryStringParams['q'])) {
                        $q = $arrQueryStringParams['q'];
                    }
                    $outfitList = $this->model->search($q);

                } catch (Error $e) {

                    $strErrorDesc = $e->getMessage() . 'Something went wrong!';
                    $responseHeader = 'HTTP/1.1 500 Internal Server Error';
                }
                break;
        }

        $responseData = $this->buildResponse($outfitList, $strErrorDesc);

        $this->sendOutput(
            json_encode($responseData),
            array('Content-Type: application/json', $responseHeader)
        );
    }

    public function add()
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
                    $result = $this->model->insert($data);

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
                    $result = $this->model->delete($data);

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