<?php
/**
 * Created by PhpStorm.
 * User: patrickmorin
 * Date: 14/04/2017
 * Time: 13:24
 */

// web/index.php
require_once __DIR__.'/../vendor/autoload.php';

// Declaration on app
$app = new Silex\Application();
// Provider
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$app->get('/login', function () use ($app) {
    // ...

    return $app['twig']->render('login.twig', array(
        'name' => '',
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