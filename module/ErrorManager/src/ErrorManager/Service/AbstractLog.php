<?php

/**
 * Description of AbstractLog
 *
 * @author kimsreng
 */

namespace ErrorManager\Service;

use Zend\Http\PhpEnvironment\Request;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractLog implements LogInterface {

    protected $config;

    /**
     * @var Request
     */
    protected $request;

    public function __construct(ServiceLocatorInterface $service, Array $config) {
        $this->request = $service->get('Request');
        $this->config = $config;
    }

    abstract public function logException($e);

    /**
     * Get all exception information including the previous ones
     * 
     * @param Exception $e
     * @return string exception information
     */
    protected function getExceptionContent($e) {
        if ($e instanceof \Exception) {
            $content = 'URI: ' . $this->request->getServer('REQUEST_URI') . PHP_EOL;
            $content .= 'Date: ' . date('Y-m-d H:i:s') . PHP_EOL;
            $content .= 'File: ' . $e->getFile() . PHP_EOL;
            $content .= 'Message: ' . $e->getMessage() . PHP_EOL;
            $content .= 'Stack trace: ' . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
            $content .= 'Previous exceptions: ' . PHP_EOL;
        }
        $e = $e->getPrevious();
        if ($e) {
            while ($e) {
                $content .= '<<======' .  PHP_EOL;
                $content .= 'File: ' . $e->getFile() . PHP_EOL;
                $content .= 'Message: ' . $e->getMessage() . PHP_EOL;
                $content .= 'Stack trace: ' . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
                $e = $e->getPrevious();
            }
        }
        return $content;
    }

}
