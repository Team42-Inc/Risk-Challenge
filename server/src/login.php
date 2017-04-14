<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 14/04/2017
 * Time: 14:13
 */

namespace oasix;


use PHPGangsta\PHPGangsta_GoogleAuthenticator;
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

            $sql = 'SELECT user, passhash, otpkey FROM admins WHERE user = ? LIMIT 1;';
            $baseData = $this->app['dbs']['mysql_read']->fetchAll($sql, array( $login ) );


            $base_login = $baseData['user']; //"admin";
            //adminadmin
            $base_passHash = $baseData['passhash']; // '$2y$10$KSCRpE.Yh/H1xuAdtLS2KuEB5GHSMUOPnrT1K9IkBVwzTcWC2GUbm';
            $base_otpKey = $baseData['otpkey'];

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
        $time = time();
        $ip = $request->getClientIp();
        $this->app['session']->set('user', array(
            'login' => $login,
            'connect_ip' =>  $ip,
            'connect_time' => $time
        ));
        $sql = "UPDATE admins SET last_connect = ?, last_ip = ? WHERE user = ? ";
        $this->app['dbs']['mysql_write']->executeUpdate( (int)$time,  $ip, $login  );

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

    private function checkOtp( $otp,  $otpKey)
    {
        if (strlen($otp) != 6)
            return false;
        //get otp service
        $googleAuth = new PHPGangsta_GoogleAuthenticator();

        // check +- 4minutes code
        if( $googleAuth->verifyCode($otpKey, $otp, 8 ) )
            return true;

        return false;
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