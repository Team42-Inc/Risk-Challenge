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
        $this->app = $app;
        $app['agent'] = $this;
    }


}