<?php

return array(
    'router' => array(
        'routes' => array(
            'presignup' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/join',
                    'defaults' => array(
                        'controller' => 'Users\Controller\User',
                        'action' => 'join',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Users\Controller\User' => 'Users\Controller\UserController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'users' => __DIR__ . '/../view',
        ),
    ),
    'service_manager' => array(),
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