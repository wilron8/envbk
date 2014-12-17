<?php

return array(
    'router' => array(
        'routes' => array(
            'process-doc' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/doc/:path',
                    'defaults' => array(
                        'controller' =>  'DocumentManager\Controller\DocumentManager',
                        'action' => 'process'
                    )
                )
            ),
            'process-image' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/image/:path',
                    'defaults' => array(
                        'controller' =>  'DocumentManager\Controller\Image',
                        'action' => 'process'
                    )
                )
            ),
            'process-thumbimage' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/imageThumb/:path',
                    'defaults' => array(
                        'controller' =>  'DocumentManager\Controller\Image',
                        'action' => 'imageThumb'
                    )
                )
            ),
            'process-tmpimage' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/tmpimg/:path',
                    'defaults' => array(
                        'controller' =>  'DocumentManager\Controller\Image',
                        'action' => 'processTmp'
                    )
                )
			)
        )
    ),
    'controllers' => array(
        'invokables' => array(
           'DocumentManager\Controller\DocumentManager' => 'DocumentManager\Controller\DocumentManagerController',
           'DocumentManager\Controller\Image' => 'DocumentManager\Controller\ImageController'
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'document_manager' => __DIR__ . '/../view',
            'image_manager' => __DIR__ . '/../view'
        )
    )
);

