<?php

/**
 * Description of SearchEngine
 *
 * @author kimsreng
 */

namespace SearchEngine\Model;

use Zend\Db\Adapter\Adapter;

class SearchEngine {

    const SEARCH_PEOPLE = 'people';
    const SEARCH_MESSAGE = 'message';
    const SEARCH_IDEA = 'idea';
    const SEARCH_PROJECT = 'project';
    const SEARCH_IDEA_COMMENT = 'idea-comment';
    const SEARCH_PROJECT_COMMENT = 'project-comment';

    public $searchType = [];

    /**
     * @var Adapter
     */
    protected $adapter;

    /**
     * @var SearchOption 
     */
    protected $option;

    public function __construct($dbAdapter) {
        $this->adapter = $dbAdapter;
        $this->option = new SearchOption();
    }

    public function setOptions($options) {
        $this->option->fillOption($options);
    }

    public function findPeople($keyword, $olderThan = '', $newerThan = '') {
        $search = new PeopleSearch($keyword, $this->option, $this->adapter);
        return $search->find($olderThan, $newerThan);
    }

    public function findIdea($keyword, $olderThan = '', $newerThan = '') {
        $search = new IdeaSearch($keyword, $this->option, $this->adapter);
        return $search->find($olderThan, $newerThan);
    }

    public function findProject($keyword, $olderThan = '', $newerThan = '') {
        $search = new ProjectSearch($keyword, $this->option, $this->adapter);
        return $search->find($olderThan, $newerThan);
    }

    public function findIdeaComment($keyword, $olderThan = '', $newerThan = '') {
        $search = new IdeaCommentSearch($keyword, $this->option, $this->adapter);
        return $search->find($olderThan, $newerThan);
    }

    public function findMessage($keyword, $olderThan = '', $newerThan = '') {
        $search = new MessageSearch($keyword, $this->option, $this->adapter);
        return $search->find($olderThan, $newerThan);
    }

    public function findProjectComment($keyword, $olderThan = '', $newerThan = '') {
        $search = new ProjectWallSearch($keyword, $this->option, $this->adapter);
        return $search->find($olderThan, $newerThan);
    }

    /**
     * Convert raw data from client to search type list as array
     * 
     * @param type $searchType
     */
    public function fillSearchType($searchType) {
        //TODO: get raw data from client convert to array 
        $this->searchType = $searchType;
    }

    /**
     * 
     * @param string $type
     * @return boolean
     */
    public function hasSearchType($type) {
        return in_array($type, $this->searchType);
    }

    public function getKeyword($post) {
        if (isset($post['keyword'])) {
            return trim($post['keyword']);
        } else {
            return '';
        }
    }

    public function getOlderThan($post) {
        if (isset($post['older'])) {
            return trim($post['older']);
        } else {
            return '';
        }
    }

    public function getNewerThan($post) {
        if (isset($post['newer'])) {
            return trim($post['newer']);
        } else {
            return '';
        }
    }

    /**
     * explode string with multiple delemiters
     * 
     * @param array $delimiters
     * @param string $string
     * @return array
     */
    private function multiExplode(Array $delimiters, $string) {
        //$processedString = str_replace($delimiters, $delimiters[0], $string);
        $processedString = $this->mb_str_replace($delimiters, $delimiters[0], $string);
        // $processedString =implode($delimiters[0], mb_split($delimiters, $string));
        $result = explode($delimiters[0], $processedString);
        return $result;
    }

    /**
     * Replace string for unicode char
     * 
     * @param array|string $needle
     * @param string $replacement
     * @param string $haystack
     * @return string
     */
    private function mb_str_replace($needle, $replacement, $haystack) {

        if (is_array($needle)) {
            foreach ($needle as $value) {
                $needle_len = mb_strlen($value);
                $replacement_len = mb_strlen($replacement);
                $pos = mb_strpos($haystack, $value);
                while ($pos !== false) {
                    $haystack = mb_substr($haystack, 0, $pos) . $replacement
                            . mb_substr($haystack, $pos + $needle_len);
                    $pos = mb_strpos($haystack, $value, $pos + $replacement_len);
                }
            }
        } else {
            $needle_len = mb_strlen($needle);
            $replacement_len = mb_strlen($replacement);
            $pos = mb_strpos($haystack, $needle);
            while ($pos !== false) {
                $haystack = mb_substr($haystack, 0, $pos) . $replacement
                        . mb_substr($haystack, $pos + $needle_len);
                $pos = mb_strpos($haystack, $needle, $pos + $replacement_len);
            }
        }
        return $haystack;
    }

}

?>
