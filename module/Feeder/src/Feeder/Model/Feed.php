<?php

/**
 * Description of Feed
 *
 * @author kimsreng
 */

namespace Feeder\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

class Feed {

    protected $adapter;

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }

    public function getFeed($userId, $limit, $offset) {
        $driver = $this->adapter->getDriver();
        $connection = $driver->getConnection();
        $result = $connection->execute("CALL feed($limit,$offset,$userId)");
        $statement = $result->getResource();
        $resultSet = $statement->fetchAll();
        return $resultSet;
    }

}
