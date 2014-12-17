<?php

/**
 *
 * @author kimsreng
 */
namespace Common\DbEntity;

interface EntityInterface {

    public function exchangeArray($data);

    public function getArrayCopy();
}
