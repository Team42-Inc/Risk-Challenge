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
            return false;
        }
        echo ("url ".$this->app['dashboard.urlDashBoard']);
        $len = $this->app['dashboard.nb_agent'];
        for( $i = 0; $i < $len ; $i++ ){
            $req = new Request();
            $req->create($this->app['dashboard.urlDashBoard'], 'GET', array("host"=>$this->app['dashboard.agents'][$i]['host']));
            echo( "content : " . $req->getContent() );
            $this->parseHost($i, $req->getContent());
        }
    }

    private function parseHost($index, $content ){
        $data = @json_decode($content);
        $this->app['dashboard.agents'][$index] = \HostDetail::fromJSON($data);
    }

    private function getAgentList(){

        $this->app['dashboard.agents']=array(
            array('host' =>  'www.mra.mu'),
            array('host' =>  'www.govmu.mu'),
            array('host' =>  'ta.gov-mu.org'),
        );
        $this->app['dashboard.nb_agent'] = count($this->app['dashboard.agents']);
        return true;
        //get the list of agents in database
        $sql = "SELECT hosts FROM agents";
        $data = $this->app['dbs']['mysql_read']->fetchAll($sql);
        if( isset( $data ) ) {
            $this->app['dashboard.agents'] = array();
            if ( isset( $data[0]['hosts']) ){
                $len = count( $data );
                $this->app['dashboard.nb_agent'] = $len;
                for($i = 0 ; $i < $len ; $i ++ ){
                    $hosts = str_split( $data[$i]['hosts'], ',' );
                    $len = count( $hosts );
                    for( $j=0 ; $j<$len; $j++ ) {
                        if( empty($hosts[$j]))
                            continue;
                        $this->app['dashboard.agents'][] = array(
                            'host' => $hosts[$j]
                        );
                    }
                }
                return true;
            }
        }
        return false;
    }
}