<?php
return array(
    'router' => array(
        'routes' => array(
            'search' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/find[/][:keyword]',
                    'defaults' => array(
                        'controller' =>  'SearchEngine\Controller\Search',
                        'action' => 'find',
                    ),
                ),
            ),
            'find-advanced' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/find-advanced',
                    'defaults' => array(
                        'controller' =>  'SearchEngine\Controller\Search',
                        'action' => 'advanced',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'SearchEngine\Controller\Search' => 'SearchEngine\Controller\SearchController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'search_engine' => __DIR__ . '/../view',
        ),
    ),
     'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ),
        ),
    ),
);