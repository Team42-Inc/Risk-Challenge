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
require_once __DIR__.'/../src/model/Connexion.php';
require_once __DIR__.'/../src/model/Rate.php';

// HTTP
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
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
$app->register(new Silex\Provider\RoutingServiceProvider());

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'dbs.options' => array (
        'mysql_read' => array(
            'driver'    => 'pdo_mysql',
            'host'      => 'oasixdb.ctjmjqlns8jn.eu-west-1.rds.amazonaws.com',
            'dbname'    => 'oasixdb',
            'user'      => 'masteroasixdb',
            'password'  => 'JeENP2uO6F8zEmrmYbbZ',
            'charset'   => 'utf8mb4',
        ),
        'mysql_write' => array(
            'driver'    => 'pdo_mysql',
            'host'      => 'oasixdb.ctjmjqlns8jn.eu-west-1.rds.amazonaws.com',
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
$app->register(new dashboard(), array('dashboard.urlDashBoard' => 'http://10.0.2.57:8080/servers/state'));
$app->register(new user());
$app->register(new agent(), array(
    'agent.urlConnexionsHistory' => 'http://10.0.2.57:8080/servers/history',
    'agent.urlRateHistory' => 'http://10.0.2.57:8080/servers/metrics/rates'
));


$app->get('/login', function (Request $request) use ($app) {
    // ...

    return $app['twig']->render('login.twig', array(
        'last_username' => $request->cookies->get('lastusername'),
        'error' => '',
        'admins' => array(),
        'page_name' => 'Connexion',
    ));
})->bind('login');

$app->post('/login', function(Request $request) use ($app){
    if( $app['login']->checkLogin( $request  ) ){

        //succes
        $cookie = new Cookie("lastusername", $app['login.username'] );
        $response = $app->redirect('dashboard');
        $response->headers->setCookie($cookie);
        return $response;
    }

    return $app['twig']->render('login.twig', array(
        'last_username' => $request->cookies->get('lastusername'),
        'error' => $app['login.error'],
        'admins' => array(),
        'page_name' => 'Connexion',
    ));
});

$app->get('/dashboard', function (Request $request) use ($app) {
    $app['dashboard']->run();

    $app['admins.listCurrentAdmin'] = array( array(
        "user" => "default",
        "ip"   => $request->getClientIp(),
        "country" => "MU"
    )
    );

    $listHosts = isset($app['dashboard.agents']) ? $app['dashboard.agents'] : array();

    //calculate global stats
    $i=0;
    $nb_vulnerabilities = 0;
    $package_to_update = 0;
    $global_security = 0;
    foreach ($listHosts as $host ){
        $nb_vulnerabilities += $host['vulnerabilitiesCount'];
        $package_to_update += $host['requiredUpdatesCount'];
        $global_security += $host['rate'];
        $i++;
    }
    $global_security /= $i;

    /*
     * @todo: remettre la bonne valeur pour hosts
     */
    return $app['twig']->render('dashboard.twig', array(
        'hosts' => $listHosts,
        'admins' => isset($app['admins.listCurrentAdmin']) ? $app['admins.listCurrentAdmin'] : array(),
        'page_name' => 'Dashboard',
        'username' => $app['session']->get('user')['username'],
        'nb_vulnerabilities' => $nb_vulnerabilities,
        'package_to_update' => $package_to_update,
        'global_security' => floor($global_security),

    ));
})->bind('dashboard');

$app->get('/agent-{id}', function (Request $request, $id) use ($app) {
    $app['dashboard']->run();

    $app['agent'] -> getConnexions( $id );
    $app['agent'] -> getRates( $id );
    $app['agent'] -> getHosts( $id );


    $app['admins.listCurrentAdmin'] = array( array(
        "user" => "default",
        "ip"   => $request->getClientIp(),
        "country" => "MU"
    )
    );

    $dataConnexionPort = array(
        'char_name' => 'connexions',
        'char_datas' => $app['agent.connexions.graph.port']['datas'],
        'char_options' => array(
            'title'     => 'Connexions',
            'curveType' => 'function',
            'legend'    => array( 'position' => 'bottom' )
        ),
        'char_type' => 'line'
    );

    $dataSuspiciousPays = array(
        'char_name' => 'Suspicious_pays',
        'char_datas' => $app['agent'] -> getMapGraphSuspiciousData(),
        'char_options' => array(
            'title' => 'Suspicious Connexion',
            'colorAxis' => array('colors' => array('#FF0000') ),
            'backgroundColor' => '#81d4fa',
            'datalessRegionColor' => '#FFFFFF',
            'defaultColor' => '#FFFFFF',
        ),
        'char_type' => 'geochart'
    );

    $dataRate = array(
        'char_name' => 'rates',
        'char_datas' => $app['agent']->getLineRatesData() ,
        'char_options' => array(
            'title'     => 'Safety Rates',
            'curveType' => 'function',
            'legend' => array( 'position' => 'bottom' ),
            'vAxis' => array(
                'minValue' => 0 ,
                'maxValue' => 100,
                'viewWindow' => array( 'min' => 0, 'max'=>100)
            ),
        ),
        'char_type' => 'line'
    );


    return $app['twig']->render('agent.twig', array(
        'dataraw' => json_encode(
            array(
                $dataRate,
                $dataConnexionPort,
                $dataSuspiciousPays
            )
        ),
        'error' => '',
        'page_name' => 'Agent Details : '.$id,
        'username' => $app['session']->get('user')['username'],
        'admins' => $app['admins.listCurrentAdmin'],
        'hosts' => isset($app['dashboard.agents']) ? $app['dashboard.agents'] : array(),
        'rate'                  => $app['agent.rate'],
        'vulnerabilities'       => $app['agent.vulnerabilities'],
        'vulnerabilitiesCount'  => $app['agent.vulnerabilitiesCount'],
        'requiredUpdate'        => $app['agent.requiredUpdate'],
        'requiredUpdatesCount'  => $app['agent.requiredUpdatesCount'],
    ));
})->bind('agent');

$app->get('/user-profile', function (Request $request) use ($app) {
    $app['dashboard']->run();
    // ...
    $app['admins.listCurrentAdmin'] = array( array(
        "user" => "default",
        "ip"   => $request->getClientIp(),
        "country" => "MU"
    )
    );

    return $app['twig']->render('user.twig', array(
        'error' => '',
        'page_name' => 'User > Profile',
        'hosts' => isset($app['dashboard.agents']) ? $app['dashboard.agents'] : array(),
    ));
});


$app->get('/user-add', function (Request $request) use ($app) {
    $app['dashboard']->run();
    // ...
    $app['admins.listCurrentAdmin'] = array( array(
        "user" => "default",
        "ip"   => $request->getClientIp(),
        "country" => "MU"
    )
    );

    $session_user = $app['session']->get('user');

    return $app['twig']->render('useradd.twig', array(
        'error' => '',
        'admins' => $app['admins.listCurrentAdmin'],
        'warningDefaultUser' => $session_user=='default'?'yes':'',
        'page_name' => 'User > Add',
        'hosts' => isset($app['dashboard.agents']) ? $app['dashboard.agents'] : array(),
    ));
})->bind('user-add');

$app->post('/user-add', function (Request $request) use ($app) {
    $app['dashboard']->run();

    $app['admins.listCurrentAdmin'] = array( array(
        "user" => "default",
        "ip"   => $request->getClientIp(),
        "country" => "MU"
    )
    );

    $username = $request->get('_username');
    if( $app['user']->AddUser($request->get('_username') , $request->get('_password'), $request->get('_mail')  ) ){
        if( $app['user.add.logout'] ){
            return $app->redirect('/login');
        }else{
            return $app['twig']->render('user_add_ok.twig', array(
                'username'  => $username,
                'urlQRCode' => $app['user.add.otp.QRCodeUrl'],
                'secret'    => $app['user.add.otp.secret'],
                'admins' => $app['admins.listCurrentAdmin'],
                'hosts' => isset($app['dashboard.agents']) ? $app['dashboard.agents'] : array(),
                'page_name' => 'User > Add',
            ));
        }

    }

    return $app['twig']->render('useradd.twig', array(
        'error' => $app['user.add.error'],
        'warningDefaultUser' =>'',
        'page_name' => 'User > Add',
        'admins' => $app['admins.listCurrentAdmin'],
        'hosts' => isset($app['dashboard.agents']) ? $app['dashboard.agents'] : array(),
        '' =>'',
    ));
});

$app->get('/server/profile', function () use ($app) {
    $app['dashboard']->run();
    // ...

    return $app['twig']->render('server.twig', array(
        'error' => '',
        'page_name' => 'Server > Profile',
        'hosts' => isset($app['dashboard.agents']) ? $app['dashboard.agents'] : array(),
    ));
});

$app->get('/logout', function () use ($app) {
    // ...
    $app['session']->invalidate(0);
    return  $app->redirect('login');;
})->bind('logout');

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