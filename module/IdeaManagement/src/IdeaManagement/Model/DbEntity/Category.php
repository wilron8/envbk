<?php


/**
 * Description of Category
 *
 * @author kimsreng
 */
namespace IdeaManagement\Model\DbEntity;

use Common\DbEntity\EntityInterface;

class Category implements EntityInterface{
    public $cat_id;
    public $cat_text;
    public $cat_timeStamp;
    public $cat_isFlagged;
    public $cat_ideaCnt;
    
    public function exchangeArray($data){
        $this->cat_id = (isset($data['cat_id'])) ? $data['cat_id'] : NULL;
        $this->cat_text = (isset($data['cat_text'])) ? $data['cat_text'] : NULL;
        $this->cat_timeStamp = (isset($data['cat_timeStamp'])) ? $data['cat_timeStamp'] : NULL;
        $this->cat_isFlagged = (isset($data['cat_isFlagged'])) ? $data['cat_isFlagged'] : NULL;
        $this->cat_ideaCnt = (isset($data['cat_ideaCnt'])) ? $data['cat_ideaCnt'] : NULL;
    }
    public function getArrayCopy(){
        return get_object_vars($this);
    }
}
