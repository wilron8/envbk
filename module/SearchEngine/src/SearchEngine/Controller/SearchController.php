<?php

/**
 * Description of SearchController
 *
 * @author kimsreng
 */

namespace SearchEngine\Controller;

use SearchEngine\Model\SearchEngine;
use SearchEngine\Form\SearchForm as Form;
use SearchEngine\Form\AdvancedSearch as AdvancedForm;
use SearchEngine\Form\Filter\AdvancedSearch as Filter;
use Common\Mvc\Controller\AuthenticatedController;
use Zend\View\Model\ViewModel;
use Zend\Escaper\Escaper;

class SearchController extends AuthenticatedController {

//TODO: detect ExtJS cookie and output a JsonModel() instead of ViewModel()
    protected $nonAuthenticatedActions = ['find','advanced'];

    public function findAction() {
        $this->initView();
        $form = new Form($this->getServiceLocator());
        $this->view->form = $form;

        $keyword = $this->params()->fromRoute('keyword', false);
        if (empty($keyword)) {
            $keyword = $this->params()->fromQuery('keyword');
        }
        $keyword = trim($keyword);
        if (strlen($keyword) > 0) {
            $searchEngine = new SearchEngine($this->get('Zend\Db\Adapter\Adapter'));
            $this->view->people = $searchEngine->findPeople($keyword);
            $this->view->idea = $searchEngine->findIdea($keyword);
            if ($this->get('AuthService')->hasIdentity()) {
                $this->view->project = $searchEngine->findProject($keyword);
                $this->view->message = $searchEngine->findMessage($this->laIdentity()->getId(), $keyword);
                $this->view->userId = $this->laIdentity()->getId();
            }
            $form->get('keyword')->setValue($keyword);
        } else {
            $this->view->nokeyword = true;
        }
        return $this->view;
    }

    public function advancedAction() {

        $SE = new SearchEngine($this->get('Zend\Db\Adapter\Adapter'));
        $form = new AdvancedForm($this->get('translator'));
        $filter = new Filter($this->get('translator'));
        $form->setInputFilter($filter);

        //check all type by default
        if (!$this->request->isPost()) {
            $form->setData(['type' => [SearchEngine::SEARCH_IDEA, SearchEngine::SEARCH_MESSAGE, SearchEngine::SEARCH_PEOPLE, SearchEngine::SEARCH_PROJECT, SearchEngine::SEARCH_IDEA_COMMENT, SearchEngine::SEARCH_PROJECT_COMMENT]]);
        }

        $post = $this->params()->fromPost();
        $this->initView();
        $this->view->form = $form;
        if($this->isUserAuthenticated()){
            $this->view->userId = $this->laIdentity()->getId();
        }
        if ($this->request->isPost()) {
             $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $SE->setOptions($this->params()->fromPost());
                $SE->fillSearchType($post['type']);
                if ($SE->hasSearchType(SearchEngine::SEARCH_IDEA)) {
                    $this->view->ideas = $SE->findIdea($SE->getKeyword($post), $SE->getOlderThan($post), $SE->getNewerThan($post));
                }

                if ($SE->hasSearchType(SearchEngine::SEARCH_PEOPLE)) {
                    $this->view->people = $SE->findPeople($SE->getKeyword($post), $SE->getOlderThan($post), $SE->getNewerThan($post));
                }

                if ($SE->hasSearchType(SearchEngine::SEARCH_PROJECT)) {
                    $this->view->projects = $SE->findProject($SE->getKeyword($post), $SE->getOlderThan($post), $SE->getNewerThan($post));
                }

                if ($SE->hasSearchType(SearchEngine::SEARCH_MESSAGE)) {
                    $this->view->messages = $SE->findMessage($this->userId, $SE->getKeyword($post), $SE->getOlderThan($post), $SE->getNewerThan($post));
                }

                if ($SE->hasSearchType(SearchEngine::SEARCH_PROJECT_COMMENT)) {
                    $this->view->project_comments = $SE->findProjectComment($SE->getKeyword($post), $SE->getOlderThan($post), $SE->getNewerThan($post));
                }

                if ($SE->hasSearchType(SearchEngine::SEARCH_IDEA_COMMENT)) {
                    $this->view->idea_comments = $SE->findIdeaComment($SE->getKeyword($post), $SE->getOlderThan($post), $SE->getNewerThan($post));
                }
            }
        }
        $form->isValid();
        $this->view->data = $form->getData();
        return $this->view;
    }

}

?>
