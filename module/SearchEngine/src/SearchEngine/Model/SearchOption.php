<?php

/**
 * Description of SeachOption
 *
 * @author kimsreng
 */
namespace SearchEngine\Model;

class SearchOption {

    protected $matchCase = false;
    protected $matchWholeWord = false;
    protected $ignorPuctuation = false;
    protected $ignorWhiteSpace = true;

    public function fillOption($options) {
        if(isset($options['matchCase'])){
            $this->matchCase = (boolean) $options['matchCase'];
        }
        if(isset($options['matchWholeWord'])){
            $this->matchWholeWord = (boolean) $options['matchWholeWord'];
        }
        if(isset($options['ignorPuctuation'])){
            $this->ignorPuctuation = (boolean) $options['ignorPuctuation'];
        }
        if(isset($options['ignorWhiteSpace'])){
            $this->ignorWhiteSpace = (boolean) $options['ignorWhiteSpace'];
        }
    }

    public function matchCase() {
        return $this->matchCase;
    }

    public function matchWholeWord() {
        return $this->matchWholeWord;
    }

    public function ignorPunctuation() {
        return $this->ignorPuctuation;
    }

    public function ignorWhiteSpace() {
        return $this->ignorWhiteSpace;
    }

}
