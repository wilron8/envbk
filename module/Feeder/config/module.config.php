<?php

return array(
    'router' => array(
        'routes' => array(
            'feed' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/feed[/]',
                    'defaults' => array(
                        'controller' => 'Feeder\Controller\Feed',
                        'action' => 'newsFeed',
                    ),
                ),
            ),
            'notification' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/notify[/]',
                    'defaults' => array(
                        'controller' => 'Feeder\Controller\Feed',
                        'action' => 'feed',
                    ),
                ),
            ),
            'feed-link' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/feed-link/:id/:url',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Feeder\Controller\Feed',
                        'action' => 'feedLink',
                    ),
                ),
            ),
            'discover' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/discover[/]',
                    'defaults' => array(
                        'controller' => 'PublicFeed',
                        'action' => 'discover',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Feeder\Controller\Feed' => 'Feeder\Controller\FeedController'
        ),
        'factories' => [
            'PublicFeed' => function($sm) {
        $controller = new \Feeder\Controller\PublicFeedController($sm->getServiceLocator()->get('IdeaTable'));
        return $controller;
    }
        ]
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'feeder' => __DIR__ . '/../view',
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
