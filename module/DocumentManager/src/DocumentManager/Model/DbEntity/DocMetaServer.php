<?php

/**
 * Description of DocMetaServer
 *
 * @author kimsreng
 */
namespace DocumentManager\Model\DbEntity;

class DocMetaServer {

    public $docMetaSvr_id = NULL;
    public $docMetaSvr_fileID = NULL;
    public $docMetaSvr_timeStamp = NULL;
    public $docMetaSvr_lastModified = NULL;
    public $docMetaSvr_sync = 0;
    public $docMetaSvr_isAvailable = 1;
    public $docMetaSvr_locServer = NULL;
    public $docMetaSvr_locPath = NULL;
    public $docMetaSvr_country = NULL;
    public $docMetaSvr_continent = NULL;
    public $docMetaSvr_comment = NULL;

    public function exchangeArray($data) {
        $this->docMetaSvr_id = (isset($data['docMetaSvr_id'])) ? $data['docMetaSvr_id'] : $this->docMetaSvr_id;
        $this->docMetaSvr_fileID = (isset($data['docMetaSvr_fileID'])) ? $data['docMetaSvr_fileID'] : $this->docMetaSvr_fileID;
        $this->docMetaSvr_timeStamp = (isset($data['docMetaSvr_timeStamp'])) ? $data['docMetaSvr_timeStamp'] : $this->docMetaSvr_timeStamp;
        $this->docMetaSvr_lastModified = (isset($data['docMetaSvr_lastModified'])) ? $data['docMetaSvr_lastModified'] : $this->docMetaSvr_lastModified;
        $this->docMetaSvr_sync = (isset($data['docMetaSvr_sync'])) ? $data['docMetaSvr_sync'] : $this->docMetaSvr_sync;
        $this->docMetaSvr_isAvailable = (isset($data['docMetaSvr_isAvailable'])) ? $data['docMetaSvr_isAvailable'] : $this->docMetaSvr_isAvailable;
        $this->docMetaSvr_locServer = (isset($data['docMetaSvr_locServer'])) ? $data['docMetaSvr_locServer'] : $this->docMetaSvr_locServer;
        $this->docMetaSvr_locPath = (isset($data['docMetaSvr_locPath'])) ? $data['docMetaSvr_locPath'] : $this->docMetaSvr_locPath;
        $this->docMetaSvr_country = (isset($data['docMetaSvr_country'])) ? $data['docMetaSvr_country'] : $this->docMetaSvr_country;
        $this->docMetaSvr_continent = (isset($data['docMetaSvr_continent'])) ? $data['docMetaSvr_continent'] : $this->docMetaSvr_continent;
        $this->docMetaSvr_comment = (isset($data['docMetaSvr_comment'])) ? $data['docMetaSvr_comment'] : $this->docMetaSvr_comment;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}
