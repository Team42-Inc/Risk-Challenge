<?php

/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 15/04/2017
 * Time: 18:28
 */
namespace oasix;

class Connexion
{
    public $id;
    public $timestamp;
    public $count;
    public $protocol;
    public $port;
    public $ip;
    public $country;
    public $suspicious;

    public static function fromJSONList( $data ){
        $retour = array();
        $len = count($data);
        for($i = 0; $i< $len ; $i++ ){
            $tmp = new Connexion();
            $tmp->id           = $data[$i]->id;
            $tmp->timestamp    = $data[$i]->timestamp;
            $tmp->count        = $data[$i]->count;
            $tmp->protocol     = $data[$i]->protocol;
            $tmp->port         = $data[$i]->port;
            $tmp->ip           = $data[$i]->ip;
            $tmp->country      = $data[$i]->country;
            $tmp->suspicious   = $data[$i]->suspicious;
            $retour[] = $tmp;
        }
        return $retour;
    }

}