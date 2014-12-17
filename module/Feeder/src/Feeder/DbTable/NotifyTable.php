<?php

/**
 * Description of NotifyTable
 *
 * @author kimsreng
 */

namespace Feeder\DbTable;

use Common\DbTable\AbstractTable;
use Feeder\DbEntity\Notify;

class NotifyTable extends AbstractTable {

    protected $table = "notify";
    protected $primaryKey = "ntfy_id";

    public function getOutstandingNtfy($userId) {
        return $this->fetchAll(['ntfy_userID' => $userId, 'ntfy_isRead' => 0], NULL, 'ntfy_timeStamp DESC');
    }
    
    public function getAll($userId) {
        return $this->fetchAll(['ntfy_userID' => $userId], NULL, 'ntfy_timeStamp DESC');
    }

    public function markAsRead(Notify $entity) {
        $entity->ntfy_isRead = 1;
        return $this->update($entity);
    }

}
