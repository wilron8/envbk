<?php

/**
 * Description of EmailController
 *
 * @author kimsreng
 */

namespace People\Controller\Api;

use Common\Mvc\Controller\AuthenticatedController;
use Zend\View\Model\JsonModel;

class EmailController extends AuthenticatedController {

    public function getAction() {
        $data = $this->getTable()->getByUserId($this->userId)->toArray();
       // var_dump($data);
        //add isPrimary flag
        for ($i=0;$i<count($data);$i++) {
            foreach ($data[$i] as $key => $value) {
                if ($key == "uEmail_email") {
                    if ($value == $this->laIdentity()->getUsername()) {
                        $data[$i]['isPrimary'] = 1;
                    } else {
                        $data[$i]['isPrimary'] = 0;
                    }
                }
            }
        }
        return new JsonModel(array(
            'data' => $data
        ));
    }
    
    public function contactAction(){
        
        $emails = $this->getTable()->getByUserId($this->userId, false);
        $phones = $this->getPhoneTable()->getByUserId($this->userId);
        
        $data= [];
        foreach ($emails as $value) {
            $da=[];
            $da['type']='Email';
            $da['value']=$value->uEmail_email;
            $data[]=$da;
        }
        
        foreach ( $phones as $value){
            if($value->uPhon_type==0){
                 $da['type']='Home Phone';
            }elseif($value->uPhon_type==4){
                 $da['type']='Mobile';
            }
            $da['value']=$value->uPhon_number;
            $data[]=$da;
        }
        
        return new JsonModel(array(
            'data'=>$data,
        ));
    }

    /**
     * 
     * @return \Users\Model\DbTable\UserEmailTable
     */
    protected function getTable() {
        return $this->get('UserEmailTable');
    }
    
    /**
     * 
     * @return \Users\Model\DbTable\UserPhoneTable
     */
    protected function getPhoneTable() {
        return $this->get('UserPhoneTable');
    }

}
