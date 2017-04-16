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
        foreach ($this->app['dashboard.agents'] as $key => &$value){
            $req = new Request();
            $req->create($this->app['dashboard.urlDashBoard'], 'GET', array("host"=> $value['host']));
            $value = $this->parseHost($key, $req->getContent());
        }
        $tmp = $this->app['dashboard.agents'];
        usort( $tmp , function ($a , $b ){
            return $a['rate'] - $b['rate'];
        } );

        $this->app['dashboard.agents'] = $tmp;
    }


    private function parseHost($index, $content ){
        if( !isset( $content) || empty($content) ){
            $content = '{"id":"196.27.64.122-2017-04-16-00:53:19","analysisDate":"2017-04-15 20:53:19","host":"196.27.64.122","status":"OK","rate":"70","trend":"UP","vulnerabilitiesCount":4,"requiredUpdatesCount":40,"systemInformation":{"operatingSystem":"Linux","version":"Ubuntu 16.02"},"vulnerabilities":[{"type":"PENTEST","severity":"STANDARD","title":" 0 host(s) tested","description":" 0 host(s) tested"},{"type":"APPLICATION","severity":"MAJEUR","title":"Sql injection","description":"Lorem ipsum"},{"type":"APPLICATION","severity":"MAJEUR","title":"CSRF","description":"Lorem ipsum"},{"type":"ADMINISTRATION","severity":"CRITIQUE","title":"Root kit","description":"Lorem ipsum"}],"requiredUpdate":[{"application":"bind9-host","installedVersion":"","currentVersion":""},{"application":"liblwres90","installedVersion":"","currentVersion":""},{"application":"libevent-2.0-5","installedVersion":"","currentVersion":""},{"application":"initramfs-tools-bin","installedVersion":"","currentVersion":""},{"application":"linux-headers-generic","installedVersion":"","currentVersion":""},{"application":"libgnutls-openssl27","installedVersion":"","currentVersion":""},{"application":"multiarch-support","installedVersion":"","currentVersion":""},{"application":"libdns100","installedVersion":"","currentVersion":""},{"application":"libisccfg90","installedVersion":"","currentVersion":""},{"application":"libbind9-90","installedVersion":"","currentVersion":""},{"application":"tcpdump","installedVersion":"","currentVersion":""},{"application":"libicu52","installedVersion":"","currentVersion":""},{"application":"libgc1c2","installedVersion":"","currentVersion":""},{"application":"linux-image-3.13.0-116-generic","installedVersion":"","currentVersion":""},{"application":"libcups2","installedVersion":"","currentVersion":""},{"application":"libfreetype6","installedVersion":"","currentVersion":""},{"application":"linux-image-virtual","installedVersion":"","currentVersion":""},{"application":"libc-dev-bin","installedVersion":"","currentVersion":""},{"application":"libapparmor1","installedVersion":"","currentVersion":""},{"application":"libc-bin","installedVersion":"","currentVersion":""},{"application":"libc6","installedVersion":"","currentVersion":""},{"application":"linux-virtual","installedVersion":"","currentVersion":""},{"application":"dnsutils","installedVersion":"","currentVersion":""},{"application":"linux-headers-virtual","installedVersion":"","currentVersion":""},{"application":"update-notifier-common","installedVersion":"","currentVersion":""},{"application":"initramfs-tools","installedVersion":"","currentVersion":""},{"application":"w3m","installedVersion":"","currentVersion":""},{"application":"eject","installedVersion":"","currentVersion":""},{"application":"libxml2","installedVersion":"","currentVersion":""},{"application":"linux-headers-3.13.0-116-generic","installedVersion":"","currentVersion":""},{"application":"libapparmor-perl","installedVersion":"","currentVersion":""},{"application":"libgnutls26","installedVersion":"","currentVersion":""},{"application":"makedev","installedVersion":"","currentVersion":""},{"application":"apparmor","installedVersion":"","currentVersion":""},{"application":"linux-libc-dev","installedVersion":"","currentVersion":""},{"application":"linux-headers-3.13.0-116","installedVersion":"","currentVersion":""},{"application":"libxml2-utils","installedVersion":"","currentVersion":""},{"application":"libisccc90","installedVersion":"","currentVersion":""},{"application":"libc6-dev","installedVersion":"","currentVersion":""},{"application":"libisc95","installedVersion":"","currentVersion":""}],"openPorts":[{"port":8080,"protocol":"TCP","status":"open","defaultUsage":"http"},{"port":22,"protocol":"TCP","status":"open","defaultUsage":"ssh"}]}';
        }

        $content = '{"id":"196.27.64.122-2017-04-16-00:53:19","analysisDate":"2017-04-15 20:53:19","host":"196.27.64.122","status":"OK","rate":"70","trend":"UP","vulnerabilitiesCount":4,"requiredUpdatesCount":40,"systemInformation":{"operatingSystem":"Linux","version":"Ubuntu 16.02"},"vulnerabilities":[{"type":"PENTEST","severity":"STANDARD","title":" 0 host(s) tested","description":" 0 host(s) tested"},{"type":"APPLICATION","severity":"MAJEUR","title":"Sql injection","description":"Lorem ipsum"},{"type":"APPLICATION","severity":"MAJEUR","title":"CSRF","description":"Lorem ipsum"},{"type":"ADMINISTRATION","severity":"CRITIQUE","title":"Root kit","description":"Lorem ipsum"}],"requiredUpdate":[{"application":"bind9-host","installedVersion":"","currentVersion":""},{"application":"liblwres90","installedVersion":"","currentVersion":""},{"application":"libevent-2.0-5","installedVersion":"","currentVersion":""},{"application":"initramfs-tools-bin","installedVersion":"","currentVersion":""},{"application":"linux-headers-generic","installedVersion":"","currentVersion":""},{"application":"libgnutls-openssl27","installedVersion":"","currentVersion":""},{"application":"multiarch-support","installedVersion":"","currentVersion":""},{"application":"libdns100","installedVersion":"","currentVersion":""},{"application":"libisccfg90","installedVersion":"","currentVersion":""},{"application":"libbind9-90","installedVersion":"","currentVersion":""},{"application":"tcpdump","installedVersion":"","currentVersion":""},{"application":"libicu52","installedVersion":"","currentVersion":""},{"application":"libgc1c2","installedVersion":"","currentVersion":""},{"application":"linux-image-3.13.0-116-generic","installedVersion":"","currentVersion":""},{"application":"libcups2","installedVersion":"","currentVersion":""},{"application":"libfreetype6","installedVersion":"","currentVersion":""},{"application":"linux-image-virtual","installedVersion":"","currentVersion":""},{"application":"libc-dev-bin","installedVersion":"","currentVersion":""},{"application":"libapparmor1","installedVersion":"","currentVersion":""},{"application":"libc-bin","installedVersion":"","currentVersion":""},{"application":"libc6","installedVersion":"","currentVersion":""},{"application":"linux-virtual","installedVersion":"","currentVersion":""},{"application":"dnsutils","installedVersion":"","currentVersion":""},{"application":"linux-headers-virtual","installedVersion":"","currentVersion":""},{"application":"update-notifier-common","installedVersion":"","currentVersion":""},{"application":"initramfs-tools","installedVersion":"","currentVersion":""},{"application":"w3m","installedVersion":"","currentVersion":""},{"application":"eject","installedVersion":"","currentVersion":""},{"application":"libxml2","installedVersion":"","currentVersion":""},{"application":"linux-headers-3.13.0-116-generic","installedVersion":"","currentVersion":""},{"application":"libapparmor-perl","installedVersion":"","currentVersion":""},{"application":"libgnutls26","installedVersion":"","currentVersion":""},{"application":"makedev","installedVersion":"","currentVersion":""},{"application":"apparmor","installedVersion":"","currentVersion":""},{"application":"linux-libc-dev","installedVersion":"","currentVersion":""},{"application":"linux-headers-3.13.0-116","installedVersion":"","currentVersion":""},{"application":"libxml2-utils","installedVersion":"","currentVersion":""},{"application":"libisccc90","installedVersion":"","currentVersion":""},{"application":"libc6-dev","installedVersion":"","currentVersion":""},{"application":"libisc95","installedVersion":"","currentVersion":""}],"openPorts":[{"port":8080,"protocol":"TCP","status":"open","defaultUsage":"http"},{"port":22,"protocol":"TCP","status":"open","defaultUsage":"ssh"}]}';

        $data = @json_decode($content);
        $hostname = $this->app['dashboard.agents'][$index]['host'];
        $agentname = $this->app['dashboard.agents'][$index]['agent'];
        $tmp = json_decode( json_encode( HostDetail::fromJSON($data)) , true);

        $array =  $this->app['dashboard.agents'];
        $array[$index] = $tmp;
        $array[$index]['host'] = $hostname;
        $array[$index]['agent'] = $agentname;
        $this->app['dashboard.agents'] = $array;
        return $array;
    }

    private function getAgentList(){

        $this->app['dashboard.agents']= array(
            array('agent' =>  'agent-202.123.27.113', 'host' =>  '202.123.27.113', 'rate' => 89, 'trend' => 1, 'requiredUpdatesCount'=>4, 'vulnerabilitiesCount'=> 3),
            array('agent' =>  'agent-196.27.64.122', 'host' =>  '196.27.64.122', 'rate' => 67, 'trend' => -1, 'requiredUpdatesCount'=>23, 'vulnerabilitiesCount'=> 47),
            array('agent' =>  'agent-10.0.2.85', 'host' =>  '10.0.2.85', 'rate' => 95, 'trend' => 0, 'requiredUpdatesCount'=>2, 'vulnerabilitiesCount'=> 1),
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