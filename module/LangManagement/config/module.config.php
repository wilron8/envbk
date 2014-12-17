<?php

return array(
    'router' => array(
        'routes' => array(
            'lang' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/lang/:lang',
                    'defaults' => array(
                        'controller' => 'Lang\Controller\Lang',
                        'action' => 'setLang'
                    ),
                    'constraints' => array( //TODO: these contrains MUST NOT BE HARD-coded...
                        'lang' => '(en_US|ja_JP|fr_FR|zh_CN|es_ES)'
                    )
                )
            )
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Lang\Controller\Lang' => 'LangManagement\Controller\LangController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'lang-management' => __DIR__ . '/../view',
        ),
    ),
);
