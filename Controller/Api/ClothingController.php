<?php
class ClothingController extends BaseController
{
    private $model;

    public function __construct()
    {
        $this->model = new ClothingModel();
    }

    public function resource()
    {
        $strErrorDesc = '';
        $requestMethod = strtoupper($_SERVER["REQUEST_METHOD"]);
        $responseHeader = '';
        $clothing = array();
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
                    $clothing = $this->model->get($id);
                    $responseHeader = 'HTTP/1.1 200 OK';

                } catch (Error $e) {

                    $strErrorDesc = $e->getMessage() . 'Something went wrong!';

                    $responseHeader = 'HTTP/1.1 500 Internal Server Error';
                }
                break;
            case 'POST':
                try {
                    $lastSegment = $uriSegments[4];
                    $id = "";
                    if (!empty($lastSegment)) {
                        $id = $lastSegment;
                    }
                    $clothing = $this->model->delete($id);
                    $responseHeader = 'HTTP/1.1 200 OK';

                } catch (Error $e) {

                    $strErrorDesc = $e->getMessage() . 'Something went wrong!';

                    $responseHeader = 'HTTP/1.1 500 Internal Server Error';
                }
                break;
        }
        $responseData = $this->buildResponse($clothing, $strErrorDesc);
        $this->sendOutput(
            json_encode($responseData),
            array('Content-Type: application/json', $responseHeader)
        );

    }
    public function drawer()
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
                    $clothingId = $_GET['id'];
                    
                    if (empty($clothingId)) {
                        throw new Exception('No id given');
                    }
                    $result = $this->model->getDrawer($clothingId);
                    
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
        $outfitList = [];

        switch ($requestMethod) {
            default:
                $strErrorDesc = 'Method not supported';
                $responseHeader = 'HTTP/1.1 422 Unprocessable Entity';
                break;
            case 'GET':
                try {

                    $outfitList = $this->model->list();
                    $responseData = $this->buildResponse($outfitList, $strErrorDesc);

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
                $responseData = $this->buildResponse([], $strErrorDesc);
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
}