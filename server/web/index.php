<?php
/**
 * Created by PhpStorm.
 * User: patrickmorin
 * Date: 14/04/2017
 * Time: 13:24
 */

// web/index.php
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Declaration on app
$app = new Silex\Application();
// Provider
$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'dbs.options' => array (
        'mysql_read' => array(
            'driver'    => 'pdo_mysql',
            'host'      => 'mysql_read.someplace.tld',
            'dbname'    => 'my_database',
            'user'      => 'my_username',
            'password'  => 'my_password',
            'charset'   => 'utf8mb4',
        ),
        'mysql_write' => array(
            'driver'    => 'pdo_mysql',
            'host'      => 'mysql_write.someplace.tld',
            'dbname'    => 'my_database',
            'user'      => 'my_username',
            'password'  => 'my_password',
            'charset'   => 'utf8mb4',
        ),
    ),
));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$app->register(new \oasix\login());



$app->get('/login', function () use ($app) {
    // ...

    return $app['twig']->render('login.twig', array(
        'last_username' => $app['session.cookie']->get('lastusername'),
    ));
});

$app->post('/login', function(Request $request) use ($app){
    if( $app['login']->checkLogin( $request  ) ){
        //succes
        $cookie = new Cookie("lastusername", $app['login.username'] );
        $response = $app->redirect('/dashboard');
        $response->headers->setCookie($cookie);
        return $response;
    }

    return $app['twig']->render('login.twig', array(
        'last_username' => $app['session.cookie']->get('lastusername'),
        'error' => $app['login.error'],
    ));
});

$app->get('/dashboard', function () {
    // ...

    return "";
});

$app->get('/agent/{id}', function ($id) {
    // ...

    return "";
});

$app->get('/user/profile', function () {
    // ...

    return "";
});

$app->get('/server/profile', function () {
    // ...

    return "";
});

$app->get('/logout', function () {
    // ...

    return "";
});

$app->run();