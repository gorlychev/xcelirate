<?php

require __DIR__ . "/inc/bootstrap.php";

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

//if ((isset($uri[2]) && $uri[2] != 'shout') || !isset($uri[3])) { // if you use url like https://xcelirate.gorlychev.com/index.php/shout/steve-jobs?limit=2
if ((isset($uri[3]) && $uri[3] != 'shout') || !isset($uri[4])) { // if you use url like http://localhost/xcelirate/index.php/shout/steve-jobs?limit=1
    header("HTTP/1.1 404 Not Found");
    exit();
}

require PROJECT_ROOT_PATH . "/Controller/Api/ShoutController.php";

$objFeedController = new ShoutController();
//$authorName = $uri[3]; // if you use url like https://xcelirate.gorlychev.com/index.php/shout/steve-jobs?limit=2
$authorName = $uri[4]; //if you use domain url like http://localhost/xcelirate/index.php/shout/steve-jobs?limit=1
$objFeedController->listAction($authorName);
?>