<?php

/**
 * Description of AbstractSearch
 *
 * @author kimsreng
 */

namespace SearchEngine\Model;

use SearchEngine\Model\SearchOption;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

abstract class AbstractSearch {

    protected $option;
    protected $adapter;
    protected $query;
    protected $queryFields = [];
    protected $timeField = "";
    protected $tableName = "";
    
    /**
     *Condition to append to find query
     * 
     * @var String|Array 
     */
    protected $condition = NULL;

    public function __construct($query, SearchOption $option, $adapter) {
        $this->query = $query;
        $this->option = $option;
        $this->adapter = $adapter;
        $this->processQuery();
    }
    
    protected function processSelect(&$select){
        
    }

    public function find($older, $newer) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from($this->tableName);
        //get select object to adjust query
        $this->processSelect($select);
        $me = $this;
        $select->where(function(Where $where) use($me) {
            $where = $where->nest();
            foreach ($me->query as $word) {
                foreach ($this->queryFields as $field) {
                    $me->getSql($field, $word, $where, $me->option->matchWholeWord(), $me->option->matchCase());
                }
            }
            $where->unnest();
        });

        if ($older !== '') {
            //format appropriate date format for mysql
            $older = date('Y-m-d', strtotime($older));
            $select->where->lessThan($this->timeField, $older);
        }

        if ($newer !== '') {
            $newer = date('Y-m-d', strtotime($newer));
            $select->where->greaterThan($this->timeField, $newer);
        }
        
        if($this->condition !== NULL){
            $select->where($this->condition);
        }
        // echo $select->getSqlString();die();
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();

        return $results;
    }

    protected function processQuery() {
        if ($this->option->ignorPunctuation()) {
            $this->query = $this->removePunctuation($this->query);
        }
        if (!$this->option->ignorWhiteSpace()) {
            $this->query = $this->multiExplode(array(' ', '　'), $this->query);
        } else {
            $this->query = [$this->query];
        }
    }

    public function removePunctuation($query) {
        $cleanQuery = preg_replace("/[^a-zA-Z 0-9]+/", "", $query);
        return $cleanQuery;
    }

    public function getSql($field, $keyWord, &$where, $wholeWord = false, $caseSensitive = false) {

        if ($wholeWord && $caseSensitive) {
            $where->or->literal("$field RLIKE BINARY '[[:<:]]" . $keyWord . "[[:>:]]'");
        } elseif ($wholeWord) {
            $where->or->literal("$field RLIKE '[[:<:]]" . $keyWord . "[[:>:]]'");
        } elseif ($caseSensitive) {
            $where->or->literal("$field LIKE BINARY '%" . $keyWord . "%'");
        } else {
            $where->or->like($field, '%' . $keyWord . '%');
        }
    }

}
