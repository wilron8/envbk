<?php

/**
 * Description of Message
 *
 * @author kimsreng
 */

namespace Message\Model\DbEntity;

class Message {

    public $msg_id;
    public $msg_threadID;
    public $msg_senderID;
    public $msg_subject;
    public $msg_body;
    public $msg_timeStamp;
    public $msg_isDraft;
    public $msg_priority;
    public $msg_flagColor;

    public function exchangeArray($data) {
        $this->msg_id = (isset($data['msg_id'])) ? $data['msg_id'] : NULL;
        $this->msg_threadID = (isset($data['msg_threadID'])) ? $data['msg_threadID'] : NULL;
        $this->msg_senderID = (isset($data['msg_senderID'])) ? $data['msg_senderID'] : NULL;
        $this->msg_subject = (isset($data['msg_subject'])) ? $data['msg_subject'] : NULL;
        $this->msg_body = (isset($data['msg_body'])) ? $data['msg_body'] : NULL;
        $this->msg_timeStamp = (isset($data['msg_timeStamp'])) ? $data['msg_timeStamp'] : NULL;
        $this->msg_isDraft = (isset($data['msg_isDraft'])) ? $data['msg_isDraft'] : NULL;
        $this->msg_priority = (isset($data['msg_priority'])) ? $data['msg_priority'] : NULL;
        $this->msg_flagColor = (isset($data['msg_flagColor'])) ? $data['msg_flagColor'] : NULL;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}
