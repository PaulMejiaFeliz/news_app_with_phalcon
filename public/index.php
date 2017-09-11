<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Phalcon\Loader;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlProvider;
use Phalcon\Mvc\Application;
use Phalcon\Session\Adapter\Files as Session;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('WEB_URL', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
// ...

$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
        APP_PATH . '/helpers/',
    ]
);

$loader->registerNamespaces(
    [
        'Newsapp\Models\Validations' => APP_PATH . '/models/validations/',
        'Newsapp\Controllers' => APP_PATH . '/controllers/',
        'Newsapp\Helpers' => APP_PATH . '/helpers/'
    ]
);

$loader->register();

// Create a DI
$di = new FactoryDefault();


// Setup the view component
$di->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$di->set(
    'url',
    function () {
        $url = new UrlProvider();
        $url->setBaseUri('/');
        return $url;
    }
);

$di->setShared(
    'session',
    function () {
        $session = new Session();

        $session->start();

        return $session;
    }
);

$di->set(
    'db',
    function () {
        return new DbAdapter(
            [
                'host'     => '127.0.0.1',
                'username' => 'root',
                'password' => '1234',
                'dbname'   => 'news_app_db',
            ]
        );
    }
);

$di->set(
    'request',
    'Phalcon\Http\Request'
);

$di->set(
    'customTags',
    new Newsapp\Helpers\CustomTags($di->get('request'))
);

$application = new Application($di);


try {
    // Handle the request
    $response = $application->handle();

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}

// require 'vendor/autoload.php';
// require 'core/bootstrap.php';

// use newsapp\core\App;
// use newsapp\core\Router;
// use newsapp\core\Request;
// use newsapp\core\database\Connection;

// App::bind('router', new Router('Home@notFound'));

// require 'routes.php';

// App::get('router')->direct(Request::uri(), Request::method());
// Connection::closeConnection();
