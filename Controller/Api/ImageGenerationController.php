<?php
class ImageGenerationController extends BaseController
{
    private $model;

    public function __construct()
    {
        $this->model = new ImageGenerationModel();
    }

    public function resource()
    {
        $strErrorDesc = '';
        $requestMethod = strtoupper($_SERVER["REQUEST_METHOD"]);
        $responseData = array();
        $responseHeader = '';
        $outfit = array();

        switch ($requestMethod) {
            default:
                $strErrorDesc = 'Method not supported';
                $responseHeader = 'HTTP/1.1 422 Unprocessable Entity';
                $responseData = $this->buildResponse([], $strErrorDesc);
                break;

        }
        $responseData = $this->buildResponse($outfit, $strErrorDesc);
        $this->sendOutput(
            json_encode($responseData),
            array('Content-Type: application/json', $responseHeader)
        );

    }
    public function generate()
    {
        $requestMethod = strtoupper($_SERVER["REQUEST_METHOD"]);
        $responseHeader = 'HTTP/1.1 200 OK';
        $strErrorDesc = '';
        $imageBase64 = "";

        switch ($requestMethod) {
            default:
                $strErrorDesc = 'Method not supported';
                $responseHeader = 'HTTP/1.1 422 Unprocessable Entity';
                break;
            case 'POST':

                try {
                    $json = file_get_contents('php://input');
                    $data = json_decode($json, true);
                    $prompt = $data['prompt'];

                    $imageBase64 = $this->model->generate($prompt);

                } catch (Error $e) {

                    $strErrorDesc = $e->getMessage() . 'Something went wrong!';

                    $responseHeader = 'HTTP/1.1 500 Internal Server Error';
                }
                break;
        }

        $this->sendOutput(
            '{
                "data" :' . '"' . $imageBase64 . '"' . ',
                "error" :' . '"' . $strErrorDesc . '"' . '
            }',
            array('Content-Type: application/json', $responseHeader)
        );

    }

    public function description()
    {

        $requestMethod = strtoupper($_SERVER["REQUEST_METHOD"]);
        $responseHeader = 'HTTP/1.1 200 OK';
        $strErrorDesc = '';
        $imageDescription = "";
        $responseData = array();

        switch ($requestMethod) {
            default:
                $strErrorDesc = 'Method not supported';
                $responseHeader = 'HTTP/1.1 422 Unprocessable Entity';
                break;
            case 'POST':
                try {
                    $json = file_get_contents('php://input');
                    // Find the position of the base64-encoded image data
                    $startPosition = strpos($json, '"image": "') + strlen('"image": "');
                    $endPosition = strpos($json, '"', $startPosition);

                    // Extract the base64-encoded image data
                    $base64ImageData = substr($json, $startPosition, $endPosition - $startPosition);

                    $imageBase64BackgroundRemoved = $this->model->removeBackground($base64ImageData);
                    $imageDescription = $this->model->getDescription($imageBase64BackgroundRemoved);
                } catch (Error $e) {
                    $strErrorDesc = $e->getMessage() . 'Something went wrong!';
                    $responseHeader = 'HTTP/1.1 500 Internal Server Error';
                }
                break;
        }
        $responseData = $this->buildResponse($imageDescription, $strErrorDesc);
        $this->sendOutput(
            json_encode($responseData),
            array('Content-Type: application/json', $responseHeader)
        );
    }

    public function suggestion()
    {

        $requestMethod = strtoupper($_SERVER["REQUEST_METHOD"]);
        $responseHeader = 'HTTP/1.1 200 OK';
        $strErrorDesc = '';
        $suggestionMap = array();
        $responseData = array();

        switch ($requestMethod) {
            default:
                $strErrorDesc = 'Method not supported';
                $responseHeader = 'HTTP/1.1 422 Unprocessable Entity';
                break;
            case 'POST':
                try {
                    $json = file_get_contents('php://input');
                    $jsonMap = json_decode($json, true);
                    $suggestion = $this->model->getSuggestion($jsonMap['description']);
                    $suggestionMap = json_decode($suggestion, true);
                } catch (Error $e) {
                    $strErrorDesc = $e->getMessage() . 'Something went wrong!';
                    $responseHeader = 'HTTP/1.1 500 Internal Server Error';
                }
                break;
        }
        $responseData = $this->buildResponse($suggestionMap, $strErrorDesc);
        $this->sendOutput(
            json_encode($responseData),
            array('Content-Type: application/json', $responseHeader)
        );
    }

    public function removebg()
    {

        $requestMethod = strtoupper($_SERVER["REQUEST_METHOD"]);
        $responseHeader = 'HTTP/1.1 200 OK';
        $strErrorDesc = '';
        $imageBase64BackgroundRemoved = '';

        switch ($requestMethod) {
            default:
                $strErrorDesc = 'Method not supported';
                $responseHeader = 'HTTP/1.1 422 Unprocessable Entity';
                break;
            case 'POST':
                try {
                    $json = file_get_contents('php://input');
                    // Find the position of the base64-encoded image data
                    $startPosition = strpos($json, '"image": "') + strlen('"image": "');
                    $endPosition = strpos($json, '"', $startPosition);

                    // Extract the base64-encoded image data
                    $base64ImageData = substr($json, $startPosition, $endPosition - $startPosition);
                    $imageBase64BackgroundRemoved = $this->model->removeBackground($base64ImageData);
                } catch (Error $e) {
                    $strErrorDesc = $e->getMessage() . 'Something went wrong!';
                    $responseHeader = 'HTTP/1.1 500 Internal Server Error';
                }
                break;
        }
        $this->sendOutput(
            '{
                "data" :' . '"' . $imageBase64BackgroundRemoved . '"' . ',
                "error" :' . '"' . $strErrorDesc . '"' . '
            }',
            array('Content-Type: application/json', $responseHeader)
        );

    }

}