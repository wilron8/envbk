<?php

/**
 * Description of MessageTo
 *
 * @author kimsreng
 */

namespace Message\Model\DbEntity;

class MessageTo {

    public $msg2_id;
    public $msg2_messageID;
    public $msg2_recepientID;
    public $msg2_isRead;
    public $msg2_readTime;
    public $msg2_remindDate;
    public $msg2_remindShow;
    public $msg2_reportedAsSpam;
    public $msg2_isVisible;
    public $msg2_hideTime;

    public function exchangeArray($data) {
        $this->msg2_id = (isset($data['msg2_id'])) ? $data['msg2_id'] : NULL;
        $this->msg2_messageID = (isset($data['msg2_messageID'])) ? $data['msg2_messageID'] : NULL;
        $this->msg2_recepientID = (isset($data['msg2_recepientID'])) ? $data['msg2_recepientID'] : NULL;
        $this->msg2_isRead = (isset($data['msg2_isRead'])) ? $data['msg2_isRead'] : NULL;
        $this->msg2_readTime = (isset($data['msg2_readTime'])) ? $data['msg2_readTime'] : NULL;
        $this->msg2_remindDate = (isset($data['msg2_remindDate'])) ? $data['msg2_remindDate'] : NULL;
        $this->msg2_remindShow = (isset($data['msg2_remindShow'])) ? $data['msg2_remindShow'] : NULL;
        $this->msg2_reportedAsSpam = (isset($data['msg2_reportedAsSpam'])) ? $data['msg2_reportedAsSpam'] : NULL;
        $this->msg2_isVisible = (isset($data['msg2_isVisible'])) ? $data['msg2_isVisible'] : NULL;
        $this->msg2_hideTime = (isset($data['msg2_hideTime'])) ? $data['msg2_hideTime'] : NULL;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}
