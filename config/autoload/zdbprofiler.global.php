<?php

/**
 * Local Configuration Override
 *
 * This configuration override file is for overriding environment-specific and
 * security-sensitive configuration information. Copy this file without the
 * .dist extension at the end and populate values as needed.
 *
 * @NOTE: This file is ignored from Git by default with the .gitignore included
 * in ZendSkeletonApplication. This is a good practice, as it prevents sensitive
 * credentials from accidentally being committed into version control.
 */
$env = getenv('APPLICATION_ENV') ? : 'production';
if ($env == 'development') {
    switch ($_SERVER['HTTP_HOST']) {
        case "ec2-50-112-151-122.us-west-2.compute.amazonaws.com":
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
            break;

        default:
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
    }//
    return array(
        'service_manager' => array(
            'factories' => array(
                'Zend\Db\Adapter\Adapter' => function ($sm) use ($databaseConfig) {
            $adapter = new BjyProfiler\Db\Adapter\ProfilingAdapter(array(
                'driver' => 'pdo',
                'dsn' => 'mysql:dbname=' . $databaseConfig['database'] . ';host=' . $databaseConfig['hostname'],
                'database' => $databaseConfig['database'],
                'username' => $databaseConfig['user'],
                'password' => $databaseConfig['password'],
                'hostname' => $databaseConfig['hostname'],
                'charset' => $databaseConfig['charset'],
                'driver_options' => $databaseConfig['driver_options'],
            ));

            if (php_sapi_name() == 'cli') {
                $logger = new Zend\Log\Logger();
                // write queries profiling info to stdout in CLI mode
                $writer = new Zend\Log\Writer\Stream('php://output');
                $logger->addWriter($writer, Zend\Log\Logger::DEBUG);
                $adapter->setProfiler(new BjyProfiler\Db\Profiler\LoggingProfiler($logger));
            } else {
                $adapter->setProfiler(new BjyProfiler\Db\Profiler\Profiler());
            }
            if (isset($databaseConfig['options']) && is_array($databaseConfig['options'])) {
                $options = $databaseConfig['options'];
            } else {
                $options = array();
            }
            $adapter->injectProfilingStatementPrototype($options);
            return $adapter;
        },
            ),
        ),
    );
}