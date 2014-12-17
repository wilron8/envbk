<?php

/**
 * Description of Project
 *
 * @author kimsreng
 */

namespace ProjectManagement\Model\DbEntity;

class Project {

    public $proj_id = NULL;
    public $proj_srcIdea = NULL;
    public $proj_img = NULL;
    public $proj_title = NULL;
    public $proj_descript = NULL;
    public $proj_timeStamp = NULL;
    public $proj_lastModified = NULL;
    public $proj_progress = 0;
    public $proj_memCnt = 0;
    public $proj_hitCnt = 0;
    public $proj_violationCnt = 0;
    public $proj_newRef = NULL;
    public $proj_isClosed = 0;
    public $proj_isVisible = 1;
    public $proj_isSuccess = 0;
    public $proj_isWallPublic = 0;
    public $proj_isWallPublicWritable = 0;
    public $proj_isWallMemWritable = 1;
    public $proj_isMemberShipOpen = 0;

    public function exchangeArray($data) {
        $this->proj_id = (isset($data['proj_id'])) ? $data['proj_id'] : $this->proj_id;
        $this->proj_srcIdea = (isset($data['proj_srcIdea'])) ? $data['proj_srcIdea'] : $this->proj_srcIdea;
        $this->proj_img = (isset($data['proj_img'])) ? $data['proj_img'] : $this->proj_img;
        $this->proj_title = (isset($data['proj_title'])) ? $data['proj_title'] : $this->proj_title;
        $this->proj_descript = (isset($data['proj_descript'])) ? $data['proj_descript'] : $this->proj_descript;
        $this->proj_timeStamp = (isset($data['proj_timeStamp'])) ? $data['proj_timeStamp'] : $this->proj_timeStamp;
        $this->proj_lastModified = (isset($data['proj_lastModified'])) ? $data['proj_lastModified'] : $this->proj_lastModified;
        $this->proj_progress = (isset($data['proj_progress'])) ? $data['proj_progress'] : $this->proj_progress;
        $this->proj_memCnt = (isset($data['proj_memCnt'])) ? $data['proj_memCnt'] : $this->proj_memCnt;
        $this->proj_hitCnt = (isset($data['proj_hitCnt'])) ? $data['proj_hitCnt'] : $this->proj_hitCnt;
        $this->proj_violationCnt = (isset($data['proj_violationCnt'])) ? $data['proj_violationCnt'] : $this->proj_violationCnt;
        $this->proj_newRef = (isset($data['proj_newRef'])) ? $data['proj_newRef'] : $this->proj_newRef;
        $this->proj_isClosed = (isset($data['proj_isClosed'])) ? $data['proj_isClosed'] : $this->proj_isClosed;
        $this->proj_isVisible = (isset($data['proj_isVisible'])) ? $data['proj_isVisible'] : $this->proj_isVisible;
        $this->proj_isSuccess = (isset($data['proj_isSuccess'])) ? $data['proj_isSuccess'] : $this->proj_isSuccess;
        $this->proj_isWallPublic = (isset($data['proj_isWallPublic'])) ? $data['proj_isWallPublic'] : $this->proj_isWallPublic;
        $this->proj_isWallPublicWritable = (isset($data['proj_isWallPublicWritable'])) ? $data['proj_isWallPublicWritable'] : $this->proj_isWallPublicWritable;
        $this->proj_isWallMemWritable = (isset($data['proj_isWallMemWritable'])) ? $data['proj_isWallMemWritable'] : $this->proj_isWallMemWritable;
        $this->proj_isMemberShipOpen = (isset($data['proj_isMemberShipOpen'])) ? $data['proj_isMemberShipOpen'] : $this->proj_isMemberShipOpen;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}
