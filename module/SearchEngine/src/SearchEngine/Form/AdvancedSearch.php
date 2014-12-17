<?php

/**
 * Description of AdvancedSearch
 *
 * @author kimsreng
 */

namespace SearchEngine\Form;

use Zend\Form\Form;

class AdvancedSearch extends Form {

    public function __construct($translator) {
        parent::__construct('search');
        $this->setAttribute('method', 'get');
        
        $this->add(array(
            'type' => 'Zend\Form\Element\MultiCheckbox',
            'name' => 'type',
            'attributes' => array(
            ),
            'options' => array(
                'label' => $translator->translate('Type'),
                'value_options' => $this->getTypeSelect($translator),
            ),
        ));
        
        $this->add(array(
            'name' => 'keyword',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('Keyword')
            ),
            'options' => array(
                'label' => $translator->translate('Keyword')
            ),
        ));
        $this->add(array(
            'name' => 'older',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('Older')
            ),
            'options' => array(
                'label' => $translator->translate('Older')
            ),
        ));
        $this->add(array(
            'name' => 'newer',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('Newer')
            ),
            'options' => array(
                'label' => $translator->translate('Newer')
            ),
        ));
        
        $this->add(array(
            'name' => 'matchCase',
            'attributes' => array(
                'type' => 'checkbox',
                'placeholder' => $translator->translate('Newer')
            ),
            'options' => array(
                'label' => $translator->translate('Newer')
            ),
        ));
        
        $this->add(array(
            'name' => 'matchWholeWord',
            'attributes' => array(
                'type' => 'checkbox',
                'placeholder' => $translator->translate('matchWholeWord')
            ),
            'options' => array(
                'label' => $translator->translate('matchWholeWord')
            ),
        ));
        
        $this->add(array(
            'name' => 'ignorWhiteSpace',
            'attributes' => array(
                'type' => 'checkbox',
                'placeholder' => $translator->translate('ignorWhiteSpace')
            ),
            'options' => array(
                'label' => $translator->translate('ignorWhiteSpace')
            ),
        ));
        
        $this->add(array(
            'name' => 'ignorPuctuation',
            'attributes' => array(
                'type' => 'checkbox',
                'placeholder' => $translator->translate('ignorPuctuation')
            ),
            'options' => array(
                'label' => $translator->translate('ignorPuctuation')
            ),
        ));
        
    }

    public function getTypeSelect($translator) {
        return array(
            'people' => $translator->translate('People'),
            'idea' => $translator->translate('Idea'),
            'project' => $translator->translate('Project'),
            'message' => $translator->translate('Message'),
            'idea-comment' => $translator->translate('Idea Comment'),
            'project-comment' => $translator->translate('Project Comment')
        );
    }

}
