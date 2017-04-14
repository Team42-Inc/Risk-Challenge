<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 14/04/2017
 * Time: 14:13
 */

namespace oasix;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class login implements ServiceProviderInterface
{
    private  $app;
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
    }

    public function checkLogin(Request $request){


        if( $request->get('_username', null ) !== null &&
            $request->get('_password', null)  !== null &&
            $request->get('_otp', null) !== null )
        {
            //get the post data
            $login = $request->get('_username', null );
            $password = $request->get('_password', null );
            $otp = $request->get('_otp', null );

            //check of data integrety
            if( !$this->validateLoginFormat($login) ||
                !$this->validatePasswordFormat($password) ||
                !$this->validateOTPFormat($otp)
            ) {
                $this->onLoginFail();
                return false;
            }

            //TODO get the data from database
            $base_login = "admin";
            //adminadmin
            $base_passHash = '$2y$10$KSCRpE.Yh/H1xuAdtLS2KuEB5GHSMUOPnrT1K9IkBVwzTcWC2GUbm';
            $base_otpKey = "";

            if( $base_login === $login && password_verify($password,$base_passHash) ){
                //check otp
                if( $this->checkOtp($otp, $base_otpKey) ){
                    if( $this->onLoginValide($login, $request) )
                      return true;
                }
            }

        }
        $this->onLoginFail();
        return false;
    }

    private function onLoginValide( $login, Request $request){
        //stock in session userid, clientip
        if( !$this->app['session']->start() )
            return false;
        $this->app['session']->set('user', array(
            'login' => $login,
            'connect_ip' => $request->getClientIp(),
            'connect_time' => time()
        ));


        $this->app['login.username'] = $login;
        return true;
    }

    public function CheckSessionLogin(Request $request){
        $session_user = $this->app['session']->get('user');
        if( !isset( $session_user))
            return false;
        if( $session_user['connect_ip'] != $request->getClientIp() )
            return false;
        if( $session_user['connect_time'] + 1200 < time() )
            return false;
        return true;
    }

    private function onLoginFail(){
        //destroy session
        $this->app['session']->invalidate(0);
        //set the error
        $this->app['login.error'] = "error login fail";
        //antibrute force
        sleep( 3 );
    }

    private function checkOtp( $otp,  $otpKey){
        if( strlen($otp) != 6 )
            return false;
        //TODO get otp service and check +- 4minutes code

        //HACK
        if( substr($otp, 4) == '42' )
            return true;
    }

    private function validateLoginFormat($login){
        return isset($login) && strlen($login) >= 4 ;
    }
    private function validatePasswordFormat($password){
        return isset($password) && strlen($password) >= 9 ;
    }
    private function validateOTPFormat($otp){
        return isset($otp) && strlen($otp) == 6 ;
    }
}