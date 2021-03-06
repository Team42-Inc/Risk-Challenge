<?php
namespace oasix;

/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 15/04/2017
 * Time: 11:09
 */

class SystemInformation{
    public $operatingSystem;
    public $version;
}
class Vulnerability{
    public $type;
    public $severity;
    public $title;
    public $description;

}

class OpenPort{
    public $port;
    public $protocol;
    public $defaultUsage;
    public $status;
}

class RequiredUpdate{
    public $application;
    public $installedVersion;
    public $currentVersion;
}

class HostDetail
{
    public $id;
    public $analysisDate;
    public $host;
    public $status;
    public $rate;
    public $trend;
    public $vulnerabilitiesCount;
    public $requiredUpdatesCount;
    // SystemInformation
    public $systemInformation;
    //array of Vulnerability
    public $vulnerabilities;
    //array of OpenPort
    public $openPorts;
    //array of RequiredUpdate
    public $requiredUpdate;

    public static function fromJSON( $data ){
        $retour = new HostDetail();
        $retour->id                     = $data->id;
        $retour->analysisDate           = $data->analysisDate;
        $retour->host                   = $data->host;
        $retour->status                 = $data->status;
        $retour->rate                   = $data->rate;
        $retour->trend                  = $data->trend=="UP"?1:($data->trend=="DOWN"?-1:0);
        $retour->vulnerabilitiesCount   = $data->vulnerabilitiesCount;
        $retour->requiredUpdatesCount   = $data->requiredUpdatesCount;

        $retour->systemInformation = new SystemInformation();
        $retour->systemInformation->operatingSystem = $data->systemInformation->operatingSystem;
        $retour->systemInformation->version         = $data->systemInformation->version;


        $retour->vulnerabilities = array();
        foreach ($data->vulnerabilities as $vulnerability ){
            $vul = new Vulnerability();
            $vul->title = $vulnerability->title;
            $vul->description = $vulnerability->description;
            $vul->severity = $vulnerability->severity;
            $vul->type = $vulnerability->type;
            $retour->vulnerabilities[] = $vul;
        }
        $retour->vulnerabilitiesCount = count($retour->vulnerabilities);

        $retour->openPorts = array();
        foreach ($data->openPorts as $port ){
            $openport = new OpenPort();
            $openport->port = $port->port;
            $openport->defaultUsage = $port->defaultUsage;
            $openport->protocol = $port->protocol;
            $openport->status = $port->status;
            $retour->openPorts[] = $openport;
        }

        $retour->requiredUpdate = array();
        foreach ($data->requiredUpdate as $update ){
            $requiredUpdate = new RequiredUpdate();
            $requiredUpdate->application = $update->application;
            $requiredUpdate->installedVersion = $update->installedVersion;
            $requiredUpdate->currentVersion = $update->currentVersion;
            $retour->requiredUpdate[] = $requiredUpdate;
        }
        $retour->requiredUpdatesCount = count($retour->requiredUpdate);
        return $retour;
    }
}