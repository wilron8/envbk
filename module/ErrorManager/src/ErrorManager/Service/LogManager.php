<?php

/**
 * Description of LogManager
 *
 * @author kimsreng
 */

namespace ErrorManager\Service;

class LogManager {

    /**
     * A list of log methods to be executed
     * 
     * @var array
     */
    protected $logMethods;
    protected $service;

    public function __construct(Array $logMethods, $service) {
        $this->logMethods = $logMethods;
        $this->service = $service;
    }

    /**
     * Execute all logMethods registered in config
     * 
     * @param Exception $e
     * @throws \Exception
     */
    public function log($e) {
        foreach ($this->logMethods as $log => $config) {
            $log = new $log($this->service,$config);
            if ($log instanceof LogInterface) {
                $log->logException($e);
            } else {
                throw new \Exception(get_class($log) . " must implement LogInterface");
            }
        }
    }

}
