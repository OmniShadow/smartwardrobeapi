<?php
define("PROJECT_ROOT_PATH", __DIR__ . "/../");
// include main configuration file 
require_once PROJECT_ROOT_PATH . "/inc/config.php";
// include the base controller file 
require_once PROJECT_ROOT_PATH . "/Controller/Api/BaseController.php";
// include the model files 
require_once PROJECT_ROOT_PATH . "/Model/Database.php";
require_once PROJECT_ROOT_PATH . "/Model/OutfitModel.php";
require_once PROJECT_ROOT_PATH . "/Model/DrawerModel.php";
require_once PROJECT_ROOT_PATH . "/Model/ClothingModel.php";
require_once PROJECT_ROOT_PATH . "/Model/ImageGenerationModel.php";
?>