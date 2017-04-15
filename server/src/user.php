<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 15/04/2017
 * Time: 03:35
 */

namespace oasix;


use PHPGangsta\PHPGangsta_GoogleAuthenticator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class user implements ServiceProviderInterface
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
    public function register(Container $app)
    {
        $this->app = $app;
        $app['user'] = $this;
    }

    public function AddUser($username, $password, $email ){
        //check username
        if( strlen($username) < 4 || $username == "default"){
            $app['user.add.error'] = "username too short";
            return false;
        }
        //check email format
        if( strlen($email) < 8 || !filter_var($email, FILTER_VALIDATE_EMAIL) ){
            $app['user.add.error'] = "email is invalid";
            return false;
        }

        //check if it's a new user
        $sql = "SELECT user FROM admins WHERE user = ? OR email = ? LIMIT 1;";
        $baseData = $this->app['dbs']['mysql_read']->fetchAll($sql, array($username, $email) );
        if( isset($baseData, $baseData['user'] ) ){
            $app['user.add.error'] = "username allready exist";
            return false;
        }

        //check password
        if( strlen($password) < 9 ){
            $app['user.add.error'] = "password is too short (9 char minimum)";
            return false;
        }
        //check if uppercase and lower case
        if( !preg_match('/[A-Z]+[a-z]+[0-9]+/', $password) ){
            $app['user.add.error'] = "password is too week, it needs to have at least an upper case, a lower case AND a digit";
            return false;
        }

        //generate OTP token
        $googleAuth = new PHPGangsta_GoogleAuthenticator();
        $otpToken = $googleAuth->createSecret();
        $app['user.add.otp.secret'] = $otpToken;
        $app['user.add.otp.QRCodeUrl'] = $googleAuth->getQRCodeGoogleUrl("OASIX ".$username, $otpToken);

        //insert in base
        $this->app['dbs']['mysql_write']->insert('admins', array(
            'user' => $username,
            'email' => $email,
            'passhash' => password_hash($password,PASSWORD_BCRYPT, array('cost' => 12 ) ),
            'otpkey' => $otpToken
        ));

        //check if current user is admin
        $userObj =  $this->app['session']->get('user');
        if( $userObj['username'] == "default" ){
            $this->app['dbs']['mysql_write']->delete('admins', array(
                'user' => "default"
            ) );

            $this->app['session']->invalidate(0);

            $app['user.add.logout'] = true;
        }

        return true;
    }
}