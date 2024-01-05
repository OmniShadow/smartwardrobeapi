<?php
class BookController extends BaseController
{


    public function __construct()
    {
        $this->AVAILABLE_METHODS = ["list", "search", "add", "ownedBook",];
    }

    public function listAction()
    {
        $strErrorDesc = '';
        $requestMethod = strtoupper($_SERVER["REQUEST_METHOD"]);
        $arrQueryStringParams = $this->getQueryStringParams();
        $responseData = array();

        switch ($requestMethod) {
            default:
                $strErrorDesc = 'Method not supported';
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
                break;
            case 'GET':
                try {
                    $bookModel = new BookModel();
                    $q = "";
                    $limit = 10;

                    if (isset($arrQueryStringParams['q'])) {
                        $q = $arrQueryStringParams['q'];
                    }

                    if (isset($arrQueryStringParams['limit'])) {
                        $limit = $arrQueryStringParams['limit'];
                    }

                    $responseData = $bookModel->getBooksFromApi($q, $limit);


                } catch (Error $e) {
                    $strErrorDesc = $e->getMessage() . 'Something went wrong!';
                    $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                }
                break;
        }

        if (!$strErrorDesc) {
            $this->sendOutput(
                json_encode($responseData),
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(
                json_encode(array('error' => $strErrorDesc)),
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }

    public function ownedBookAction()
    {
        $strErrorDesc = '';
        $requestMethod = strtoupper($_SERVER["REQUEST_METHOD"]);
        $arrQueryStringParams = $this->getQueryStringParams();
        $responseData = array();

        switch ($requestMethod) {
            default:
                $strErrorDesc = 'Method not supported';
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
                break;
            case 'GET':
                try {
                    $bookModel = new BookModel();
                    $possessoId = "";
                    if (isset($arrQueryStringParams['id'])) {
                        $possessoId = $arrQueryStringParams['id'];
                    } else
                        throw new Error("id non specificato ");

                    $responseData = $bookModel->getOwnedBook($possessoId);
                } catch (Error $e) {
                    $strErrorDesc = $e->getMessage() . 'Something went wrong!';
                    $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                }
                break;
        }

        if (!$strErrorDesc) {
            $this->sendOutput(
                json_encode($responseData),
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(
                json_encode(array('error' => $strErrorDesc)),
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }



    public function searchAction()
    {
        $strErrorDesc = '';
        $requestMethod = strtoupper($_SERVER["REQUEST_METHOD"]);
        $arrQueryStringParams = $this->getQueryStringParams();
        $responseData = array();

        switch ($requestMethod) {
            default:
                $strErrorDesc = 'Method not supported';
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
                break;
            case 'GET':
                try {
                    $bookModel = new BookModel();
                    $q = "";
                    $limit = 200;

                    if (isset($arrQueryStringParams['q'])) {
                        $q = $arrQueryStringParams['q'];
                    }

                    if (isset($arrQueryStringParams['limit'])) {
                        $limit = $arrQueryStringParams['limit'];
                    }

                    $responseData = $bookModel->getOwnedBooks($q, $limit);


                } catch (Error $e) {
                    $strErrorDesc = $e->getMessage() . 'Something went wrong!';
                    $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                }
                break;
        }

        if (!$strErrorDesc) {
            $this->sendOutput(
                json_encode($responseData),
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(
                json_encode(array('error' => $strErrorDesc)),
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }

    public function addAction()
    {
        $strErrorDesc = '';
        $uriSegments = $this->getUriSegments();
        $requestMethod = strtoupper($_SERVER["REQUEST_METHOD"]);
        $responseData = array();
        $redirect = false;
        $bookModel = new BookModel();

        switch ($requestMethod) {
            default:
                $strErrorDesc = 'Method not supported';
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
                break;
            case 'POST':
                try {
                    $bookModel = new BookModel();
                    $userId = $_POST["userId"];
                    if (!isset($_SESSION["user"]))
                        throw new Error("user not logged in");
                    if ($_SESSION["user"]["id"] != $userId)
                        throw new Error("user not authorized");
                    $description = $_POST["descrizione"];
                    $file_name = $_FILES["copertina"]["name"];
                    if (empty($file_name)) {
                        $target_file = $_POST["copertina-url"];
                    } else {
                        $target_dir = "imgs/bookcovers/";
                        $target_file = $target_dir . $_POST["id"] . "copertina." . strtolower(pathinfo($_FILES["copertina"]["name"], PATHINFO_EXTENSION));
                        move_uploaded_file($_FILES["copertina"]["tmp_name"], $target_file);
                        $target_file = "/bookexchange/$target_file";
                    }
                    $bookData = array(
                        'titolo' => $_POST["titolo"],
                        'editore' => $_POST["editore"],
                        'id' => $_POST["id"],
                        'lingua' => $_POST["lingua"],
                        'anno' => $_POST["anno"],
                        'autori' => $_POST["autore"],
                        'copertina' => $target_file,
                        'categorie' => $_POST["categoria"],
                    );



                    $responseData["status"] = $bookModel->addBook($userId, $bookData, $description);
                    if ($responseData["status"])
                        $responseData["message"] = "Book added successfully";
                    else
                        $responseData["message"] = "Book not added due to error";
                    $redirect = true;
                } catch (Error $e) {
                    $strErrorDesc = $e->getMessage() . 'Something went wrong!';
                    $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                }
                break;
        }

        if (!$strErrorDesc) {
            if (!$redirect)
                $this->sendOutput(
                    json_encode($responseData),
                    array('Content-Type: application/json', 'HTTP/1.1 200 OK')
                );
            else {
                $this->sendOutput(
                    json_encode($responseData),
                    array('Content-Type: application/json', "refresh:0; url=/bookexchange/api.php/user/$userId/profile/books", 'HTTP/1.1 200 OK'),
                );
            }

        } else {
            $this->sendOutput(
                json_encode(array('error' => $strErrorDesc)),
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }

    public function resourceAction()
    {
        $strErrorDesc = '';
        $uriSegments = $this->getUriSegments();
        $requestMethod = strtoupper($_SERVER["REQUEST_METHOD"]);
        $responseData = array();
        $redirect = false;
        $bookModel = new BookModel();
        switch ($requestMethod) {
            default:
                $strErrorDesc = 'Method not supported';
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
                break;
            case 'GET':
                if (isset($uriSegments[5])) {
                    $subMethod = $uriSegments[5];
                    switch ($subMethod) {
                        case "authors":
                            $responseData = $bookModel->getBookAuthors($uriSegments[4]);
                            break;
                        case "categories":
                            $responseData = $bookModel->getBookCategories($uriSegments[4]);
                            break;
                        case "owners":
                            $responseData = $bookModel->getBookOwners($uriSegments[4]);
                            break;
                        default:
                            $strErrorDesc = 'Method not supported';
                            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
                            break;
                    }
                } else {
                    try {

                        $lastSegment = $uriSegments[4];
                        $bookId = "";
                        if (!empty($lastSegment)) {
                            $bookId = $lastSegment;
                        }
                        $responseData = $bookModel->getBook($bookId);

                    } catch (Error $e) {
                        $strErrorDesc = $e->getMessage() . 'Something went wrong!';
                        $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                    }
                }
                break;
            case 'POST':

                if (isset($uriSegments[5])) {
                    $subMethod = $uriSegments[5];
                    switch ($subMethod) {

                        default:
                            $strErrorDesc = 'Method not supported';
                            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
                            break;
                    }
                } else if (!isset($uriSegments[4])) {
                    try {
                        $bookModel = new BookModel();
                        $userId = $_POST["userId"];
                        if (!isset($_SESSION["user"]))
                            throw new Error("user not logged in");
                        if ($_SESSION["user"]["id"] != $userId)
                            throw new Error("user not authorized");
                        $description = $_POST["descrizione"];
                        $file_name = $_FILES["copertina"]["name"];
                        if (empty($file_name)) {
                            $target_file = $_POST["copertina-url"];
                        } else {
                            $target_dir = "imgs/bookcovers/";
                            $target_file = $target_dir . $_POST["id"] . "copertina." . strtolower(pathinfo($_FILES["copertina"]["name"], PATHINFO_EXTENSION));
                            move_uploaded_file($_FILES["copertina"]["tmp_name"], $target_file);
                            $target_file = "/bookexchange/$target_file";
                        }
                        $bookData = array(
                            'titolo' => $_POST["titolo"],
                            'editore' => $_POST["editore"],
                            'id' => $_POST["id"],
                            'lingua' => $_POST["lingua"],
                            'anno' => $_POST["anno"],
                            'autori' => $_POST["autore"],
                            'copertina' => $target_file,
                            'categorie' => $_POST["categoria"],
                        );



                        $responseData["status"] = $bookModel->addBook($userId, $bookData, $description);
                        if ($responseData["status"])
                            $responseData["message"] = "Book added successfully";
                        else
                            $responseData["message"] = "Book not added due to error";
                        $redirect = true;

                    } catch (Error $e) {
                        $strErrorDesc = $e->getMessage() . 'Something went wrong!';
                        $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                    }
                } else {
                    $strErrorDesc = 'Method not supported';
                    $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
                }
                break;
        }
        if (!$strErrorDesc) {
            if (!$redirect)
                $this->sendOutput(
                    json_encode($responseData),
                    array('Content-Type: application/json', 'HTTP/1.1 200 OK')
                );
            else {
                $this->sendOutput(
                    json_encode($responseData),
                    array('Content-Type: application/json', "refresh:0; url=/bookexchange/api.php/user/$userId/profile/books", 'HTTP/1.1 200 OK'),
                );
            }

        } else {
            $this->sendOutput(
                json_encode(array('error' => $strErrorDesc)),
                array('Content-Type: application/json', $strErrorHeader)
            );
        }

    }
}