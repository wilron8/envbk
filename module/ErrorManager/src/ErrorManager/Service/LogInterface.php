<?php

/**
 * Description of LogInterface
 *
 * @author kimsreng
 */

namespace ErrorManager\Service;

use Zend\ServiceManager\ServiceLocatorInterface;

interface LogInterface {

    /**
     * Inject service locator to each log method
     * 
     * @param type $service
     */
    public function __construct(ServiceLocatorInterface $service, Array $config);

    public function logException($e);
}
