<?php

require_once"../vendor/autoload.php";
// require_once"index.php";
// use Exception;
use Phalcon\Cli\Console;
use Phalcon\Cli\Dispatcher;
use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Exception as PhalconException;
use Phalcon\Loader;
use Phalcon\Db\Adapter\Pdo\Mysql;
// use App\Tasks\MainTask;

// use Throwable;

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

//REgister an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/models/",
        APP_PATH . "/tasks/"
    ]
);


$loader->registerNamespaces(
    [
       'App\Tasks' => APP_PATH."/tasks",
    ]
);
$loader->register();

$container  = new CliDI();
$dispatcher = new Dispatcher();

$dispatcher->setDefaultNamespace('App\Tasks');
$container->setShared('dispatcher', $dispatcher);

// $container->setShared('config', function () {
//     return include 'app/config/config.php';
// });


$console = new Console($container);

$container->set(
    'db',
    function () {
       // $config = $this->get('config');
        return new Mysql(
            [
               
                'host'     => 'mysql-server',
                'username' => 'root',
                'password' => 'secret',
                'dbname'   => 'store',

            ]
        );
    }
);

$arguments = [];
foreach ($argv as $k => $arg) {
    if ($k === 1) {
        $arguments['task'] = $arg;
    } elseif ($k === 2) {
        $arguments['action'] = $arg;
    } elseif ($k >= 3) {
        $arguments['params'][] = $arg;
    }
}

try {
    $console->handle($arguments);
} catch (PhalconException $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
} catch (Throwable $throwable) {
    fwrite(STDERR, $throwable->getMessage() . PHP_EOL);
    exit(1);
} catch (Exception $exception) {
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);
    exit(1);
}