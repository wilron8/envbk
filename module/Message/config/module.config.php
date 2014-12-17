<?php

return array(
    'router' => array(
        'routes' => array(
            'message' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/message[/]',
                    'defaults' => array(
                        'controller' => 'Message\Controller\Msg',
                        'action' => 'message',
                        'page' => 1
                    ),
                    'constraints' => array(
                        'page' => '[0-9]+',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'list' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'list/page/:page',
                            'constraints' => array(
                                'page' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Message\Controller\Msg',
                                'action' => 'message',
                            ),
                        ),
                    ),
                    'find' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'find/:keyword',
                            'defaults' => array(
                                'controller' => 'Message\Controller\Msg',
                                'action' => 'find',
                            ),
                        ),
                    ),
                    'action' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => ':action',
                            'defaults' => array(
                                'controller' => 'Message\Controller\Msg',
								'action' => 'message',
								'page' => 1
                            ),
                        ),
                    ),
                    'action-id' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => ':action/:id',
                            'constraints' => array(
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                              'controller' => 'Message\Controller\Msg',
                            ),
                        ),
                    ),
                    'api' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'api/:action',
                            'defaults' => array(
                              'controller' => 'Message\Controller\MsgApi',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Message\Controller\Msg' => 'Message\Controller\MsgController',
            'Message\Controller\MsgApi' => 'Message\Controller\MsgApiController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'message' => __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
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
