<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 15/04/2017
 * Time: 02:44
 */

namespace oasix;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class agent implements ServiceProviderInterface
{
    private $app;
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $app)
    {
        $app['agent'] = $this;
        $app['agent.connexions'] = '';
        $app['agent.connexions.listPorts'] = '';
        $app['agent.connexions.listPortsSuspicious'] = '';
        $app['agent.connexions.listIpSuspicious'] = '';
        $app['agent.connexions.listPaysSuspicious'] = '';
        $this->app = $app;
    }

    public function getConnexions( $id ){
        //TODO get connexion

        $req = new Request ();
        $req->create($this->app['agent.urlConnexionsHistory'], 'GET', array("host"=>$id));

        $datastr = $req->getContent();
        if( !isset($datastr)  || strlen($datastr) < 128 )
            $datastr = file_get_contents(__DIR__.'/../tmpData/http___10_0_2_57_8080_servers_metrics_connections_host_www_govmu_org');
        $data = json_decode($datastr);
        $this->app['agent.connexions'] = Connexion::fromJSONList($data->content);

        $this->preParseConnexion();

        $this->app['agent.connexions.graph.port'] = $this->aggregateByPort();
    }
    public function getRates( $id ){

        $req = new Request();
        $req->create($this->app['agent.urlConnexionsHistory'], 'GET', array("host"=>$id));

        $datastr = $req->getContent();
        if( !isset($datastr) || strlen($datastr) < 128)
            $datastr = file_get_contents(__DIR__.'/../tmpData/metric_rates');

        $data = json_decode($datastr);
        $this->app['agent.rates'] =  Rate::fromJSONList( $data );


    }

    private function preParseConnexion(){
        $list = $this->app['agent.connexions'];

        //list all ports in connexions
        $listPort = array();
        $listPortSuspicious = array();
        $listIpSuspicious = array();
        $listPaysSuspicious = array();
        foreach ( $list as $connexion ){
            //count total connexion per port
            $listPort[$connexion->port] = isset($listPort[$connexion->port]) ? $listPort[$connexion->port]+$connexion->count : $connexion->count;

            if( $connexion->suspicious ) {
                $listPortSuspicious[$connexion->port] = isset($listPortSuspicious[$connexion->port]) ? $listPortSuspicious[$connexion->port] + $connexion->count : $connexion->count;
                $listIpSuspicious[$connexion->ip] = isset($listIpSuspicious[$connexion->ip]) ? $listIpSuspicious[$connexion->ip] + $connexion->count : $connexion->count;
                $listPaysSuspicious[$connexion->country] = isset($listPaysSuspicious[$connexion->country]) ? $listPaysSuspicious[$connexion->country] + $connexion->count : $connexion->count;

            }
        }

        $this->app['agent.connexions.listPorts']            = $listPort;
        $this->app['agent.connexions.listPortsSuspicious']  = $listPortSuspicious;
        $this->app['agent.connexions.listIpSuspicious']     = $listIpSuspicious;
        $this->app['agent.connexions.listPaysSuspicious']   = $listPaysSuspicious;
    }
    private function aggregateByPort(){
        $list = $this->app['agent.connexions'];
        $retour = array();
        //save place
        $retour['0'] = array('time');
        $listPort = array_keys(  $this->app['agent.connexions.listPorts'] );
        $nbPort = count($listPort);
        for($i=0; $i<$nbPort ; $i++ ){
            $retour['0'][] = 'port '.$listPort[$i];
        }

        $listPos = array_flip( $listPort );
        //list all ports in connexions
        foreach ( $list as $connexion ){
            if( !isset($retour[$connexion->timestamp]) ){
                $retour[$connexion->timestamp] = array($connexion->timestamp);
                for($i=0; $i<$nbPort ; $i++ ){
                    $retour[$connexion->timestamp][] = 0;
                }
            }
            $retour[$connexion->timestamp][ $listPos[$connexion->port] + 1] += $connexion->count;
        }



        return array(
            'label_x' => $listPort,
            'datas' => array_values($retour)
        );
    }

    public function getMapGraphSuspiciousData(){
        $listPays = $this->app['agent.connexions.listPaysSuspicious'];

        $retour = array(array('Country','Attacks Count'));
        foreach ( $listPays as $pays => $nbAttack ){
            $retour[] = array( $pays , $nbAttack );
        }

        return $retour;
    }

    public function getLineRatesData(){
        $list = $this->app['agent.rates'];

        $retour =  array( array('time', 'rates') );
        foreach ( $list as $rate ){
            $retour[] = array( $rate->timestamp, $rate->rate );
        }

        return $retour;
    }

}
