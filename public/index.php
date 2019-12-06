<?php

ini_set('display_errors',1);
ini_set('display_starup_error',1);
error_reporting(E_ALL);

require_once '../vendor/autoload.php';
require_once '../Controllers/IndexController.php';
require_once '../Controllers/JobsController.php';
require_once '../Controllers/UsersController.php';
require_once '../Controllers/AuthController.php';
require_once '../Controllers/AdminController.php';

session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

use Illuminate\Database\Capsule\Manager as Capsule;
use Aura\Router\RouterContainer;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => getenv('DB_HOST'),
    'database'  => getenv('DB_NAME'),
    'username'  => getenv('DB_USER'),
    'password'  => getenv('DB_PASS'),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

$request = Zend\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

$routerContainer = new RouterContainer();
$map = $routerContainer->getMap();
// $map->get('index', '/{id}', function ($request, $response) {
//     $id = (int) $request->getAttribute('id');
//     $response->getBody()->write("You asked for blog entry {$id}.");
//     return $response;
// });
$map->get('index','/personal_webpage/',[
    'controller' => 'Controllers\IndexController',
    'action' => 'indexAction'
]);
$map->get('addJobs','/personal_webpage/jobs/add',[
    'controller' => 'Controllers\JobsController',
    'action' => 'getAddJobAction'
]);

$map->post('saveJobs','/personal_webpage/jobs/add',[
    'controller' => 'Controllers\JobsController',
    'action' => 'getAddJobAction'
]);

$map->get('addUsers','/personal_webpage/users/add',[
    'controller' => 'Controllers\UsersController',
    'action' => 'getAddUserAction'
]);

$map->post('saveUsers','/personal_webpage/users/add',[
    'controller' => 'Controllers\UsersController',
    'action' => 'postAddUserAction'
]);

$map->get('loginForm','/personal_webpage/login',[
    'controller' => 'Controllers\AuthController',
    'action' => 'getLogin'
]);

$map->get('logout','/personal_webpage/logout',[
    'controller' => 'Controllers\AuthController',
    'action' => 'getLogout'
]);

$map->post('auth','/personal_webpage/login',[
    'controller' => 'Controllers\AuthController',
    'action' => 'postLogin'
]);

$map->get('admin','/personal_webpage/admin',[
    'controller' => 'Controllers\AdminController',
    'action' => 'getIndex',
    'auth' => true
]);

$matcher = $routerContainer->getMatcher();
$route = $matcher->match($request);

function printElement($job){
    echo  '<li class="work-position">';
    echo '<p>'.$job->description.'</p>';
    echo '<p>'.$job->getDurationAsString().'</p>';
    echo '<strong>Achievements:</strong>';
    echo '<ul>';
    echo '<li>Lorem ipsum dolor sit amet, 80% consectetuer adipiscing elit.</li>';
    echo '<li>Lorem ipsum dolor sit amet, 80% consectetuer adipiscing elit.</li>';
    echo '<li>Lorem ipsum dolor sit amet, 80% consectetuer adipiscing elit.</li>';
    echo '</ul>';
    echo '</li>';
    echo '<h5>'.$job->title.'</h5>';
}

if (!$route) {
    echo 'No route';
}
else{
    $actionName = $route->handler['action'];
    $needsAuth = $route->handler['auth'] ?? false;

    $sessionUserId = $_SESSION['userId'] ?? null;
    if ($needsAuth && !$sessionUserId) {
        echo 'Protected route';
        die;
    }

    $controller = new $route->handler['controller'];
    $response = $controller->$actionName($request);
    
    
    
    foreach ($response->getHeaders() as $name => $values) {
        foreach ($values as $value) {
            header(sprintf('%s: %s',$name,$value),false);
        }
    }

    http_response_code($response->getStatusCode());

    echo $response->getBody();
}  