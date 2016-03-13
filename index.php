<?php
// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
  header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
  header('Access-Control-Allow-Credentials: true');
  header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

  if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
    header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");         
  }

  if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
    header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
  }

  exit(0);
}

require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

// GET route
$app->get(
    '/',
    function () {
        $template = <<<EOT
          <!DOCTYPE html>
          <html>
            <head>
              <meta charset="utf-8"/>
              <title>KBC API</title>
            </head>
            <body>
              <h1>KBC API</h1>
            </body>
          </html>
EOT;
        echo $template;
    }
);

require 'app/bootstrap.php';

$app->run();

