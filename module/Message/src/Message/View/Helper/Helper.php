<?php

/**
 * Description of Helper
 *
 * @author kimsreng
 */

namespace Message\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorInterface;

class Helper extends AbstractHelper {

    protected $sl;

    public function __invoke() {
        return $this;
    }

    /**
     * Get service manager
     * 
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->sl = $serviceLocator;
    }

    public function getRecipients($msgId) {
        $recipients = $this->sl->get('MessageToTable')->getRecepients($msgId);
        $vm = new ViewModel(array(
            'recipients' => $recipients
        ));
        $vm->setTemplate("message/helper/recipients.phtml");
        return $this->getView()->render($vm);
    }

    public function getUser($userId) {
        $user = $this->sl->get('UserTable')->getById($userId);
        if ($user) {
            return $user;
        }
        throw new \Exception("User cannot be found");
    }

}
