<?php

return array(
    'router' => array(
        'routes' => array(
            'idea' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/idea[/]',
                    'defaults' => array(
                        'controller' => 'IdeaManagement\Controller\Idea',
                        'action' => 'idea',
                    )
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
                                'controller' => 'IdeaManagement\Controller\Idea',
                                'action' => 'idea',
                                'page' => 1,
                            )
                        )
                    ),
                    'find' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'find/:keyword',
                            'defaults' => array(
                                'controller' => 'IdeaManagement\Controller\Idea',
                                'action' => 'find'
                            )
                        )
                    ),
                    'action' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => ':action',
                            'defaults' => array(
                                'controller' => 'IdeaManagement\Controller\Idea',
                            )
                        )
                    ),
                    'action-id' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => ':action/:id',
                            'constraints' => array(
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'IdeaManagement\Controller\Idea',
                            )
                        )
                    ),
                    'api' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'api/:action',
                            'defaults' => array(
                                'controller' => 'IdeaManagement\Controller\Api',
                            )
                        )
                    ),
                    'part' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => ':id/:part',
                            'constraints' => array(
                                'id' => '[0-9]+',
                                'part' => '(comment|reference|project|follower)'
                            ),
                            'defaults' => array(
                                'controller' => 'IdeaManagement\Controller\Idea',
                                'action' => 'part'
                            )
                        )
                    )
                )
            )
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'IdeaManagement\Controller\Idea' => 'IdeaManagement\Controller\IdeaController',
            'IdeaManagement\Controller\Api' => 'IdeaManagement\Controller\Api\IdeaController',
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'idea' => __DIR__ . '/../view',
        )
    ),
    'service_manager' => array(),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            )
        )
    )
);
