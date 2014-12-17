<?php

return array(
      'router' => array(
        'routes' => array(
            'project' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/project[/]',
                    'defaults' => array(
                       'controller' => 'ProjectManagement\Controller\ProjectManager',
                        'action' => 'project',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'find' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'find/:keyword',
                            'defaults' => array(
                               'controller' => 'ProjectManagement\Controller\ProjectManager',
                                'action'=>'find'
                            ),
                        ),
                    ),
                    'action' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => ':action',
                            'defaults' => array(
                               'controller' => 'ProjectManagement\Controller\ProjectManager',
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
                               'controller' => 'ProjectManagement\Controller\ProjectManager',
                            ),
                        ),
                    ),
                    'comment' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'comment/:action[/][:id][/:comment]',
                            'constraints' => array(
                                'id' => '[0-9]+',
                                'comment' => '[0-9]+',
                            ),
                            'defaults' => array(
                               'controller' => 'ProjectManagement\Controller\ProjectWall',
                            ),
                        ),
                    ),
                    'api' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'api/:action',
                            'defaults' => array(
                               'controller' => 'ProjectManagement\Controller\ApiController',
                            ),
                        ),
                    ),
                    'role' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'role/:action',
                            'defaults' => array(
                               'controller' => 'ProjectManagement\Controller\ProjectRole',
                            ),
                        ),
                    ),
                )
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'ProjectManagement\Controller\ProjectManager' => 'ProjectManagement\Controller\ProjectManagerController',
            'ProjectManagement\Controller\ApiController' => 'ProjectManagement\Controller\Api\ProjectController',
            'ProjectManagement\Controller\ProjectWall' => 'ProjectManagement\Controller\ProjectWallController',
            'ProjectManagement\Controller\ProjectRole' => 'ProjectManagement\Controller\ProjectRoleController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'project' => __DIR__ . '/../view',
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
