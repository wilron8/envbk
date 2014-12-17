<?php

/**
 * Description of userFollow
 *
 * @author kimsreng
 */

namespace IdeaManagement\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorInterface;

class Youtube extends AbstractHelper {

    /**
     * Service manager instance
     * @var Zend\ServiceManager\ServiceLocatorInterface 
     */
    protected $serviceLocator;

    public function __invoke($url) {
        //http://stackoverflow.com/questions/3392993/php-regex-to-get-youtube-video-id
//        if (stristr($url, 'youtu.be/')) {
//            preg_match('/(https|http):(\/\/www\.|\/\/)(.*?)\/(.{11})/i', $url, $final_ID);
//            $id = $final_ID[4];
//        } else {
//            preg_match('/(https|http):(\/\/www\.|\/\/)(.*?)\/(embed\/|watch\?v=|(.*?)&v=|v\/|e\/|.+\/|watch.*v=|)([a-z_A-Z0-9]{11})/i', $url, $IDD);
//            $id = $IDD[6];
//        }
        preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches);
    
        if(!isset($matches[1])){
            return false;
        }
        $id = $matches[1];
        $vm = new ViewModel(array(
            'id' => $id
        ));
        $vm->setTemplate("idea-management/helper/youtube.phtml");
        return $this->getView()->render($vm);
    }

    /**
     * Get service manager
     * 
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

}
