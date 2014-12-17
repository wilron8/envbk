<?php

/**
 * Description of CertificateTagTable
 *
 * @author kimsreng
 */

namespace People\Model\DbTable;

use Common\DbTable\AbstractTable;

class CertificateTagTable extends AbstractTable {

    protected $table = 'CertificateTag';
    protected $primaryKey = 'cert_id';

    public function getByTag($tag) {
        return $this->tableGateway->select(array('cert_text' => $tag))->current();
    }

}
