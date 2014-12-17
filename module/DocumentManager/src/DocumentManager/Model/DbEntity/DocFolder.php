<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DocFolder
 *
 * @author kimsreng
 */
namespace DocumentManager\Model\DbEntity;

class DocFolder {

    public $docFolder_id = NULL;
    public $docFolder_svrID = NULL;
    public $docFolder_ideaID = NULL;
    public $docFolder_projID = NULL;
    public $docFolder_name = NULL;
    public $docFolder_createdBy = NULL;
    public $docFolder_description = NULL;
    public $docFolder_nodeRight = NULL;
    public $docFolder_nodeLeft = NULL;
    public $docFolder_nodeDepth = NULL;
    public $docFolder_nodeParent = NULL;
    public $docFolder_dTime = NULL;
    public $docFolder_sync = NULL;

    public function exchangeArray($data) {
        $this->docFolder_id = (isset($data['docFolder_id'])) ? $data['docFolder_id'] : $this->docFolder_id;
        $this->docFolder_svrID = (isset($data['docFolder_svrID'])) ? $data['docFolder_svrID'] : $this->docFolder_svrID;
        $this->docFolder_ideaID = (isset($data['docFolder_ideaID'])) ? $data['docFolder_ideaID'] : $this->docFolder_ideaID;
        $this->docFolder_projID = (isset($data['docFolder_projID'])) ? $data['docFolder_projID'] : $this->docFolder_projID;
        $this->docFolder_name = (isset($data['docFolder_name'])) ? $data['docFolder_name'] : $this->docFolder_name;
        $this->docFolder_createdBy = (isset($data['docFolder_createdBy'])) ? $data['docFolder_createdBy'] : $this->docFolder_createdBy;
        $this->docFolder_description = (isset($data['docFolder_description'])) ? $data['docFolder_description'] : $this->docFolder_description;
        $this->docFolder_nodeRight = (isset($data['docFolder_nodeRight'])) ? $data['docFolder_nodeRight'] : $this->docFolder_nodeRight;
        $this->docFolder_nodeLeft = (isset($data['docFolder_nodeLeft'])) ? $data['docFolder_nodeLeft'] : $this->docFolder_nodeLeft;
        $this->docFolder_nodeDepth = (isset($data['docFolder_nodeDepth'])) ? $data['docFolder_nodeDepth'] : $this->docFolder_nodeDepth;
        $this->docFolder_nodeParent = (isset($data['docFolder_nodeParent'])) ? $data['docFolder_nodeParent'] : $this->docFolder_nodeParent;
        $this->docFolder_dTime = (isset($data['docFolder_dTime'])) ? $data['docFolder_dTime'] : $this->docFolder_dTime;
        $this->docFolder_sync = (isset($data['docFolder_sync'])) ? $data['docFolder_sync'] : $this->docFolder_sync;
    }
    
    public function getArrayCopy(){
         return get_object_vars($this);
    }

}
