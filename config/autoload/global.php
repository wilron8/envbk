<?php

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
switch ($_SERVER['HTTP_HOST']) {
    case "linkaide.com":
    case "linspira.com":
        $recaptchaKey = array(
            'private_key' => '6Lf1v-0SAAAAAKGbYdG9mSU4Gqy5Mu-4VezY8D3W',
            'public_key' => '6Lf1v-0SAAAAAKYFoUp4Qk9sjQNkXG7jdu-HOwZP'
        );
        $databaseConfig = array(
            'driver' => 'PDO',
            'dsn' => 'mysql:dbname=envitz;host=dev.cog1wizvpkm6.us-west-2.rds.amazonaws.com:3306',
            'database' => 'envitz',
            'hostname' => 'dev.cog1wizvpkm6.us-west-2.rds.amazonaws.com',
            'charset' => 'utf8',
            'user' => 'devuser',
            'password' => 'L!nkA1de2013',
            'driver_options' => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'')
        );
        break;
    case "ec2-50-112-151-122.us-west-2.compute.amazonaws.com":
        $recaptchaKey = array(
            'private_key' => '6LeO2OkSAAAAAEnl4q-I6QSiYafyx4m3mgY4bBqx',
            'public_key' => '6LeO2OkSAAAAAJr_YO58HioAgW2E34fE6I6ysryq'
        );
    case "ip-172-31-20-196":
        $databaseConfig = array(
            'driver' => 'PDO',
            'dsn' => 'mysql:dbname=envitz;host=dev.cog1wizvpkm6.us-west-2.rds.amazonaws.com:3306',
            'database' => 'envitz',
            'hostname' => 'dev.cog1wizvpkm6.us-west-2.rds.amazonaws.com',
            'charset' => 'utf8',
            'user' => 'devuser',
            'password' => 'L!nkA1de2013',
            'driver_options' => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'')
        );
        break;

    case "k1MobileDev":
        $databaseConfig = array(
            'dsn' => 'mysql:dbname=envitz;host=k1MobileDev',
            'driver' => 'PDO', // mysqli
            'hostname' => 'k1MobileDev',
            'charset' => 'utf8',
            'user' => 'root',
            'password' => 'Passw0rd!',
            'driver_options' => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'')
        );
        $recaptchaKey = array(
            'private_key' => '',
            'public_key' => ''
        );
        break;

    case "localhost":
    case "127.0.0.1":
        $databaseConfig = array(
            'driver' => 'PDO',
            'dsn' => 'mysql:dbname=envitz;host=localhost',
            'database' => 'envitz',
            'hostname' => 'localhost',
            'charset' => 'utf8',
            'user' => 'root',
            'password' => 'admin',
            'driver_options' => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'')
        );
        $recaptchaKey = array(//richiebartlett.com
            'private_key' => '',
            'public_key' => '-0k'
        );

    default:
        $databaseConfig = array(
            'driver' => 'PDO',
            'dsn' => 'mysql:dbname=envitz;host=localhost',
            'database' => 'envitz',
            'hostname' => 'localhost',
            'charset' => 'utf8',
            'user' => 'root',
            'password' => 'root',
            'driver_options' => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'')
        );
        $recaptchaKey = array(//richiebartlett.com
            'private_key' => '6LeL2OkSAAAAAPMOnKCHwI79JRjGiXoy6Ph1dgQp',
            'public_key' => '6LeL2OkSAAAAANNlH8FrdintKOvm7vC1NPCbw-0k'
        );

}//end switch _SERVER


return array(
    'db' => $databaseConfig,
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter'
            => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
    'session' => array(
        'config' => array(
            'class' => 'Zend\Session\Config\SessionConfig',
            'options' => array(
                'name' => 'PHPSESSID', //don't need linkaide session_name
            ),
        ),
        'storage' => 'Zend\Session\Storage\SessionArrayStorage',
        'validators' => array(
            array(
                'Zend\Session\Validator\RemoteAddr',
                'Zend\Session\Validator\HttpUserAgent',
            ),
        ),
    ),
    'sysConfig' => array(
        'domain' => 'http://www.linspira.com',
        'adminEmail' => array(
            'fromEmail' => array('system@linspira.com'),
            'toEmail' => array('kei@linspira.biz', 'rbartlett@linspira.biz', 'kevin@linspira.biz','wilfred@linspira.biz', 'jesrel@linspira.biz'),
           
        )
    ),
    'recaptcha' => $recaptchaKey,
    'mailServer'=>  include 'mail_server.php',
    //version template: major.minor.patch.build <beta | UAT | final | debug>
    'appVer' => "0.01.92.365 &Beta;"
);
