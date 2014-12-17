<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'ErrorManager\Controller\Error' => 'Error\Controller\ErrorController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'error-manager' => __DIR__ . '/../view',
        ),
    ),
);
