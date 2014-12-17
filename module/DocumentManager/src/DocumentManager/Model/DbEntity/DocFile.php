<?php

/**
 * Description of DocFile
 *
 * @author kimsreng
 */
namespace DocumentManager\Model\DbEntity;

class DocFile {

    public $docFile_id = NULL;
    public $docFile_owner = NULL;
    public $docFile_folder = 0;
    public $docFile_name = NULL;
    public $docFile_mimeType = NULL;
    public $docFile_fsize = NULL;
    public $docFile_width = NULL;
    public $docFile_height = NULL;
    public $docFile_aspect = NULL;
    public $docFile_propertyCaption = NULL;
    public $docFile_lastModified = NULL;
    public $docFile_timeStamp = NULL;
    public $docFile_priority = 0;
    public $docFile_MD5 = NULL;
    
    public function exchangeArray($data){
         $this->docFile_id = (isset($data['docFile_id'])) ? $data['docFile_id'] : $this->docFile_id ;
         $this->docFile_owner = (isset($data['docFile_owner'])) ? $data['docFile_owner'] : $this->docFile_owner ;
         $this->docFile_folder = (isset($data['docFile_folder'])) ? $data['docFile_folder'] : $this->docFile_folder ;
         $this->docFile_name = (isset($data['docFile_name'])) ? $data['docFile_name'] : $this->docFile_name ;
         $this->docFile_mimeType = (isset($data['docFile_mimeType'])) ? $data['docFile_mimeType'] : $this->docFile_mimeType ;
         $this->docFile_fsize = (isset($data['docFile_fsize'])) ? $data['docFile_fsize'] : $this->docFile_fsize ;
         $this->docFile_width = (isset($data['docFile_width'])) ? $data['docFile_width'] : $this->docFile_width ;
         $this->docFile_height = (isset($data['docFile_height'])) ? $data['docFile_height'] : $this->docFile_height ;
         $this->docFile_aspect = (isset($data['docFile_aspect'])) ? $data['docFile_aspect'] : $this->docFile_aspect ;
         $this->docFile_propertyCaption = (isset($data['docFile_propertyCaption'])) ? $data['docFile_propertyCaption'] : $this->docFile_propertyCaption ;
         $this->docFile_lastModified = (isset($data['docFile_lastModified'])) ? $data['docFile_lastModified'] : $this->docFile_lastModified ;
         $this->docFile_timeStamp = (isset($data['docFile_timeStamp'])) ? $data['docFile_timeStamp'] : $this->docFile_timeStamp ;
         $this->docFile_priority = (isset($data['docFile_priority'])) ? $data['docFile_priority'] : $this->docFile_priority ;
         $this->docFile_MD5 = (isset($data['docFile_MD5'])) ? $data['docFile_MD5'] : $this->docFile_MD5 ;
    }

    public function getArrayCopy(){
         return get_object_vars($this);
    }
}
