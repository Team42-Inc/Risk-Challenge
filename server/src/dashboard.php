<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 15/04/2017
 * Time: 09:27
 */

namespace oasix;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class dashboard implements ServiceProviderInterface
{
    public $app;

    public $url = "";
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $app A container instance
     */
    public function register(Container $app, $params = array())
    {
        $this->app = $app;
        $app['dashboard'] = $this;
        $this->url = isset( $params, $params['urlDashBoard'] ) ? $params['urlDashBoard'] : $this->url;
    }

    public function run(){
        if( !$this->getAgentList() ) {
            $this->app['dashboard.error'] = "fail to get agent list";
            return false;
        }
        $len = $this->app['dashboard.nb_agent'];
        for( $i = 0; $i < $len ; $i++ ){
            $req = new Request();
            $req->create($this->url, 'GET', array("host"=>app['dashboard.agent'][$i]['agentname']));
        }
    }

    private function getAgentList(){
        //get the list of agents in database
        $sql = "SELECT agentname FROM agents";
        $data = $this->app['dbs']['mysql_read']->fetchAll($sql);
        if( isset( $data ) ) {
            $this->app['dashboard.agent'] = array();
            //in case of only one agent
            if (isset($data['agentname'])) {
                $this->app['dashboard.nb_agent'] = 1;
                $this->app['dashboard.agent'][0] = array(
                    'agentname' => $data['agentname']
                );
                return true;
            }elseif ( isset( $data[0]['agentname']) ){
                $len = count( $data );
                $this->app['dashboard.nb_agent'] = $len;
                for($i = 0 ; $i < $len ; $i ++ ){
                    $this->app['dashboard.agent'][$i] = array(
                        'agentname' => $data[$i]['agentname']
                    );
                }
                return true;
            }
        }
        return false;
    }
}