<?php

/**
 * Description of DocMetaUser
 *
 * @author kimsreng
 */
namespace DocumentManager\Model\DbEntity;

class DocMetaUser {

    public $docMetaUsr_id = NULL;
    public $docMetaUsr_fileID = NULL;
    public $docMetaUsr_folderID = NULL;
    public $docMetaUsr_userID = NULL;
    public $docMetaUsr_r = 1;
    public $docMetaUsr_w = 0;
    public $docMetaUsr_del = 0;
    public $docMetaUsr_mov = 0;
    public $docMetaUsr_visible = 1;
    public $docMetaUsr_canEmail = 0;
    public $docMetaUsr_timeStamp = NULL;
    public $docMetaUsr_lastAccess = NULL;
    public $docMetaUsr_isFilePTR = 1;

    public function exchangeArray($data) {
        $this->docMetaUsr_id = (isset($data['docMetaUsr_id'])) ? $data['docMetaUsr_id'] : $this->docMetaUsr_id;
        $this->docMetaUsr_fileID = (isset($data['docMetaUsr_fileID'])) ? $data['docMetaUsr_fileID'] : $this->docMetaUsr_fileID;
        $this->docMetaUsr_folderID = (isset($data['docMetaUsr_folderID'])) ? $data['docMetaUsr_folderID'] : $this->docMetaUsr_folderID;
        $this->docMetaUsr_userID = (isset($data['docMetaUsr_userID'])) ? $data['docMetaUsr_userID'] : $this->docMetaUsr_userID;
        $this->docMetaUsr_r = (isset($data['docMetaUsr_r'])) ? $data['docMetaUsr_r'] : $this->docMetaUsr_r;
        $this->docMetaUsr_w = (isset($data['docMetaUsr_w'])) ? $data['docMetaUsr_w'] : $this->docMetaUsr_w;
        $this->docMetaUsr_del = (isset($data['docMetaUsr_del'])) ? $data['docMetaUsr_del'] : $this->docMetaUsr_del;
        $this->docMetaUsr_mov = (isset($data['docMetaUsr_mov'])) ? $data['docMetaUsr_mov'] : $this->docMetaUsr_mov;
        $this->docMetaUsr_visible = (isset($data['docMetaUsr_visible'])) ? $data['docMetaUsr_visible'] : $this->docMetaUsr_visible;
        $this->docMetaUsr_canEmail = (isset($data['docMetaUsr_canEmail'])) ? $data['docMetaUsr_canEmail'] : $this->docMetaUsr_canEmail;
        $this->docMetaUsr_timeStamp = (isset($data['docMetaUsr_timeStamp'])) ? $data['docMetaUsr_timeStamp'] : $this->docMetaUsr_timeStamp;
        $this->docMetaUsr_lastAccess = (isset($data['docMetaUsr_lastAccess'])) ? $data['docMetaUsr_lastAccess'] : $this->docMetaUsr_lastAccess;
        $this->docMetaUsr_isFilePTR = (isset($data['docMetaUsr_isFilePTR'])) ? $data['docMetaUsr_isFilePTR'] : $this->docMetaUsr_isFilePTR;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}
