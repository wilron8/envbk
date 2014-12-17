<?php
return array(
        'router' => array(
        'routes' => array(
            'help' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/help',
                    'defaults' => array(
                        'controller' =>  'LAhelp\Controller\Help',
                        'action' => 'help',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'LAhelp\Controller\Help' => 'LAhelp\Controller\HelpController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'lahelp' => __DIR__ . '/../view',
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