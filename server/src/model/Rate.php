<?php
namespace oasix;
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 15/04/2017
 * Time: 21:43
 */
class Rate
{
    public $id;
    public $host;
    public $timestamp;
    public $rate;

    public static function fromJSONList( $data ){
        if( !is_array( $data ) )
            return array();
        $list = array();
        foreach( $data as $rate ){
            $tmp = new Rate();
            $tmp->id         = $rate->id;
            $tmp->host       = $rate->host;
            $tmp->timestamp  = $rate->timestamp;
            $tmp->rate       = (float)$rate->rate;

            $list[] = $tmp;
        }
        return $list;
    }
}