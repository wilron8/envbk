<?php


/**
 * Description of SearchForm
 *
 * @author kimsreng
 */

namespace SearchEngine\Form;

use Zend\Form\Form;

class SearchForm extends Form {

    public function __construct($sm) {
        parent::__construct('search');
        $this->setAttribute('method', 'get');
        $this->add(array(
            'name' => 'keyword',
            'attributes' => array(
                'type' => 'text',
                'required' => 'required',
                'placeholder'=>$sm->get('translator')->translate('Keyword')
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Search')
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => $sm->get('translator')->translate('Go!')
            ),
        ));
    }

}

?>
