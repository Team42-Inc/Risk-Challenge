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
use function Symfony\Component\HttpKernel\Tests\controller_func;

class dashboard implements ServiceProviderInterface
{
    public $app;

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

    }

    public function run(){
        if( !$this->getAgentList() ) {
            $this->app['dashboard.error'] = "fail to get agent list";
            die("Wrong");
            return false;
        }
        $len = $this->app['dashboard.nb_agent'];
        for( $i = 0; $i < $len ; $i++ ){
            $req = new Request();
            $req->create($this->app['dashboard.urlDashBoard'], 'GET', array("host"=>$this->app['dashboard.agents'][$i]['host']));
            $this->parseHost($i, $req->getContent());
        }
        $tmp = $this->app['dashboard.agents'];
        usort( $tmp , function ($a , $b ){
            return $a['rate'] - $b['rate'];
        } );

        $this->app['dashboard.agents'] = $tmp;
    }


    private function parseHost($index, $content ){
        $data = @json_decode($content);
        $this->app['dashboard.agents'][$index] = HostDetail::fromJSON($data);
    }

    private function getAgentList(){

        $this->app['dashboard.agents']=array(
            array('agent' =>  'agent-1.2.3.4', 'host' =>  '1.2.3.4', 'rate' => 89, 'trend' => -1),
            array('agent' =>  'agent-89.43.123.69', 'host' =>  '89.43.123.69', 'rate' => 67, 'trend' => 1),
            array('agent' =>  'agent-102.34.98.105', 'host' =>  '102.34.98.105', 'rate' => 95, 'trend' => 0),
        );
        $this->app['dashboard.nb_agent'] = count($this->app['dashboard.agents']);
        return true;
        //get the list of agents in database
        $sql = "SELECT * FROM agents";
        $data = $this->app['dbs']['mysql_read']->fetchAll($sql);
        if( isset( $data ) ) {
            $this->app['dashboard.agents'] = array();
            if ( isset( $data[0]['hosts']) ){
                $len = count( $data );
                $this->app['dashboard.nb_agent'] = $len;
                for($i = 0 ; $i < $len ; $i ++ ){
                    $hosts = str_split( $data[$i]['hosts'], ' ' );
                    $len = count( $hosts );
                    for( $j=0 ; $j<$len; $j++ ) {
                        if( empty($hosts[$j]))
                            continue;
                        $this->app['dashboard.agents'][] = array(
                            'host' => $hosts[$j],
                        );
                    }
                }
                return true;
            }
        }
        return false;
    }
}