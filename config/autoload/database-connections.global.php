<?php
/**
 * ./config/autoload/database-connections.global.php
 */
return array(
    'service_manager' => array(
        'aliases' => array(
            'zend_db_adapter' => 'envitz',
        ),
        'aliases' => array(
            'evitzDB' => 'Zend\Db\Adapter\Adapter',
        ),
    ),
);