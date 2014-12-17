<?php

/**
 *
 * @author kimsreng
 */
namespace DocumentManager\View\Helper;

use Zend\View\Helper\AbstractHelper;
use DocumentManager\Model\PathManager as Manager;

class PathManager extends AbstractHelper{
    /**
     * @var Manager
     */
    protected $pathManager;

    
    public function __invoke()
    {
        return $this->pathManager;
    }
    /**
     * Set authService.
     *
     * @param AuthenticationService $authService
     * @return \Users\View\Helper\LaIdentity
     */
    public function setPathManager(Manager $pathManager)
    {
        $this->pathManager = $pathManager;
    }
}
