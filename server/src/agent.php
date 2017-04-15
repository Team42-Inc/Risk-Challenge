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
        $this->app = $app;
    }

    public function getConnexions(){
        //TODO get connexion

        $datastr = file_get_contents(__DIR__.'/../src/http___10_0_2_57_8080_servers_metrics_connections_host_www_govmu_org');
        $data = json_decode($datastr);
        $this->app['agent.connexions'] = \Connexion::fromJSONList($data->content);

        $this->preParseConnexion();

        $this->app['agent.connexions.graph.port'] = $this->aggregateByPort();
    }
    private function preParseConnexion(){
        $list = $this->app['agent.connexions'];

        //list all ports in connexions
        $listPort = array();
        $listPortSuspicious = array();
        $listIpSuspicious = array();
        foreach ( $list as $connexion ){
            //count total connexion per port
            $listPort[$connexion->port] = isset($listPort[$connexion->port]) ? $listPort[$connexion->port]+$connexion->count : $connexion->count;

            if( $connexion->suspicious ) {
                $listPortSuspicious[$connexion->port] = isset($listPortSuspicious[$connexion->port]) ? $listPortSuspicious[$connexion->port] + $connexion->count : $connexion->count;
                $listIpSuspicious[$connexion->ip] = isset($listIpSuspicious[$connexion->ip]) ? $listIpSuspicious[$connexion->ip] + $connexion->count : $connexion->count;
            }
        }

        $this->app['agent.connexions.listPorts'] = $listPort;
        $this->app['agent.connexions.listPortsSuspicious'] = $listPortSuspicious;
        $this->app['agent.connexions.listIpSuspicious'] = $listIpSuspicious;
    }
    private function aggregateByPort(){
        $list = $this->app['agent.connexions'];
        $retour = array();

        //list all ports in connexions
        foreach ( $list as $connexion ){
         //   if( isset( ))
        }

        return array(
            'label_x' => array_keys(  $this->app['agent.connexions.listPorts'] ),
            'datas' => $retour
        );
    }

}
