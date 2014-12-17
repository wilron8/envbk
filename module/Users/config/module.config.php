<?php

return array(
    'router' => array(
        'routes' => array(
            'user' => array(
                /*'type' => 'regex', //changed from segment to remove the "catch-all" URI. 
                'options' => array(
                    'regex' => '/(?<action>(join|registration|confirm|signin|logout|forgot|resetChallenge|reset|settings|requestChangeEmail))',
                    'defaults' => array(
                        'controller' => 'Users\Controller\User',
                        'action' => 'join'
                    ),
					'spec' => '/%action%'
                )*/
                'type' => 'regex',
                'options' => array(
                    // Added /* at first and after the regex *, it will lead to optional parameter - example - /*(?<key>[a-zA-Z0-9_-]*
                    'regex' => '/(?<action>(join|registration|confirm|signin|logout|forgot|resetChallenge|reset|settings|requestChangeEmail|relogin))/*(?<key>[a-zA-Z0-9_-]*)',
                    'defaults' => array(
                        'controller' => 'Users\Controller\User',
                        'action' => 'join',
                        'key' => ""
                    ),
                    'spec' => '/%action%/%key%'
                )
            )
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Users\Controller\User' => 'Users\Controller\UserController'
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'users' => __DIR__ . '/../view'
        )
    ),
    'service_manager' => array(
        
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo'
            )
        )
    )
);