<?php
/**
 * Created by PhpStorm.
 * User: patrickmorin
 * Date: 14/04/2017
 * Time: 13:24
 */

// web/index.php
require_once __DIR__.'/../vendor/autoload.php';

// HTTP
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
// Form
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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

$app->get('/dashboard', function () use ($app) {
    // ...

    return $app['twig']->render('dashboard.twig', array(
        'error' => '',
    ));
});

$app->get('/agent/{id}', function ($id) use ($app) {
    // ...

    return $app['twig']->render('agent.twig', array(
        'error' => '',
    ));
});

$app->get('/user/profile', function () use ($app) {
    // ...

    return $app['twig']->render('user.twig', array(
        'error' => '',
    ));
});

$app->get('/server/profile', function () use ($app) {
    // ...

    return $app['twig']->render('server.twig', array(
        'error' => '',
    ));
});

$app->get('/logout', function () {
    // ...

    return "";
});

$app->post('/register/agent', function (Request $request) use ($app) {
    $agent = json_decode($request->get('paylod'));

    // @todo: inject data in database

    return new Response('Agent registered successfully!', 201);
});

$app->run();