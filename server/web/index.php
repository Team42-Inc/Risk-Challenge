<?php
/**
 * Created by PhpStorm.
 * User: patrickmorin
 * Date: 14/04/2017
 * Time: 13:24
 */

// web/index.php
require_once __DIR__.'/../vendor/autoload.php';
//$loader->add('oasix', __DIR__.'/../src');

require_once __DIR__.'/../src/agent.php';
require_once __DIR__.'/../src/login.php';
require_once __DIR__.'/../src/GoogleAuthenticator.php';
require_once __DIR__.'/../src/user.php';
require_once __DIR__.'/../src/dashboard.php';
require_once __DIR__.'/../src/model/HostDetail.php';

// HTTP
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
// Form
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
// OasiX
use oasix\agent;
use oasix\dashboard;
use oasix\login;
use oasix\user;

// Declaration on app
$app = new Silex\Application();
$app['debug'] = true;
// Provider
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider ());

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'dbs.options' => array (
        'mysql_read' => array(
            'driver'    => 'pdo_mysql',
            'host'      => 'oasixdb.ctjmjqlns8jn.eu-west-1.rds.amazonaws.com:3306',
            'dbname'    => 'oasixdb',
            'user'      => 'masteroasixdb',
            'password'  => 'JeENP2uO6F8zEmrmYbbZ',
            'charset'   => 'utf8mb4',
        ),
        'mysql_write' => array(
            'driver'    => 'pdo_mysql',
            'host'      => 'oasixdb.ctjmjqlns8jn.eu-west-1.rds.amazonaws.com:3306',
            'dbname'    => 'oasixdb',
            'user'      => 'masteroasixdb',
            'password'  => 'JeENP2uO6F8zEmrmYbbZ',
            'charset'   => 'utf8mb4',
        ),
    ),
));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$app->register(new login());



$app->get('/login', function (Request $request) use ($app) {
    // ...

    return $app['twig']->render('login.twig', array(
        'last_username' => $request->cookies->get('lastusername'),
        'error' => ''
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
        'last_username' => $request->cookies->get('lastusername'),
        'error' => $app['login.error'],
    ));
});

$app->get('/dashboard', function () use ($app) {
    $app['dashboard']->run();



    return $app['twig']->render('dashboard.twig', array(
        'hosts' => $app['dashboard.agents'],
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


$app->get('/user/add', function () use ($app) {
    // ...

    return $app['twig']->render('useradd.twig', array(
        'error' => '',
    ));
});

$app->post('/user/add', function (Request $request) use ($app) {

    $username = $request->get('_username');
    if( $app['user']->AddUser($request->get('_username') , $request->get('_password'), $request->get('_mail')  ) ){
        if( $app['user.add.logout'] ){
            return $app->redirect('/login');
        }else{
            return $app['twig']->render('user_add_ok.twig', array(
                'username'  => $username,
                'urlQRCode' => $app['user.add.otp.QRCodeUrl'],
                'secret'    => $app['user.add.otp.secret']
            ));
        }

    }

    return $app['twig']->render('useradd.twig', array(
        'error' => $app['user.add.error'],
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
    /* post data are
     *      payload : the data in json
     *      r : a alphanum random string len > 10
     *      a : agent user
     *      s : the signature of post data sha1( [A] + ".|oasix|." + [PAYLOAD] + ".|oasix|." + [R] + ".|oasix|." + [PRIVATEKEY] )
    */

    // do a check auth like signing data
    $payload = $request->get('payload', '');
    $randomString = $request->get('r','');
    $agentid = $request->get('a','');
    $signConfirmation = $request->get('s','');




    if( strlen($payload) > 10 && strlen($randomString) > 10 && strlen($signConfirmation) > 10 && strlen($agentid) > 6 ) {

        // get in base the unique agent private key
        $sql = "SELECT privatekey FROM agents WHERE agentname = ?";
        $data = $app['dbs']['mysql_read']->fetchAll( $sql, array($agentid ) );
        $private_key = isset($data['privatekey'])?$data['privatekey']:null; //"0@six@g€ntS€cr€t";

        //calculate the sign to compare
        $sign = sha1($agentid . '.|oasix|.' . $payload . '.|oasix|.' . $randomString . '.|oasix|.' . $private_key);
        if ($private_key !== null && $sign === $request->get('s', '')) {
            //authentified
            $agent = json_decode($payload);

            // @todo: inject data in database

            return new Response('Agent registered successfully!', 201);
        }
    }
    sleep(3);
    return new Response('Agent registered fail!', 403);
});

$app->run();