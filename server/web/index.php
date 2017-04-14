<?php
/**
 * Created by PhpStorm.
 * User: patrickmorin
 * Date: 14/04/2017
 * Time: 13:24
 */

// web/index.php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app->get('/login', function () {
    // ...

    return "Hello World";
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