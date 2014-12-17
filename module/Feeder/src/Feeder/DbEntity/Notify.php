<?php

/**
 * Description of Notify
 *
 * @author kimsreng
 */

namespace Feeder\DbEntity;

use Common\DbEntity\AbstractEntity;

class Notify extends AbstractEntity {

    public $ntfy_id = NULL;
    public $ntfy_userID = NULL;
    public $ntfy_timeStamp = NULL;
    public $ntfy_isRead = 0;
    public $ntfy_body = NULL;
    public $ntfy_URL = NULL;

    public function exchangeArray($data) {
        $this->ntfy_id = (isset($data['ntfy_id'])) ? $data['ntfy_id'] : $this->ntfy_id;
        $this->ntfy_userID = (isset($data['ntfy_userID'])) ? $data['ntfy_userID'] : $this->ntfy_userID;
        $this->ntfy_timeStamp = (isset($data['ntfy_timeStamp'])) ? $data['ntfy_timeStamp'] : $this->ntfy_timeStamp;
        $this->ntfy_isRead = (isset($data['ntfy_isRead'])) ? $data['ntfy_isRead'] : $this->ntfy_isRead;
        $this->ntfy_body = (isset($data['ntfy_body'])) ? $data['ntfy_body'] : $this->ntfy_body;
        $this->ntfy_URL = (isset($data['ntfy_URL'])) ? $data['ntfy_URL'] : $this->ntfy_URL;
    }

}
