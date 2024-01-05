<?php
require __DIR__ . "/inc/bootstrap.php";
require PROJECT_ROOT_PATH . "/Controller/Api/ClothingController.php";
require PROJECT_ROOT_PATH . "/Controller/Api/OutfitController.php";
require PROJECT_ROOT_PATH . "/Controller/Api/DrawerController.php";
require PROJECT_ROOT_PATH . "/Controller/Api/ImageGenerationController.php";


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

if (!isset($uri[3])) {
    header("HTTP/1.1 404 Not Found");
    exit();
}
$resource = $uri[3];
$methodName = $uri[4];
$objFeedController;
switch ($resource) {
    case 'clothing':
        $objFeedController = new ClothingController();
        break;
    case 'outfit':
        $objFeedController = new OutfitController();
        break;
    case 'drawer':
        $objFeedController = new DrawerController();
        break;
    case 'imagegen':
        $objFeedController = new ImageGenerationController();
        break;
    default:
        header("HTTP/1.1 404 Not Found");
        exit();
}

if (!empty($methodName)) {
    if (!in_array($methodName, get_class_methods(get_class($objFeedController))))
        $methodName = "resource";

    $objFeedController->{$methodName}();
} else {
    header("HTTP/1.1 404 Not Found");
    exit();
}

?>