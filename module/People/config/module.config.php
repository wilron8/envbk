<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'People\Controller\People' => 'People\Controller\PeopleController',
            'People\Controller\Education' => 'People\Controller\EducationController',
            'People\Controller\Experience' => 'People\Controller\ExperienceController',
            'People\Controller\Skill' => 'People\Controller\SkillController',
            'People\Controller\Email' => 'People\Controller\EmailController',
            'People\Controller\EmailApi' => 'People\Controller\Api\EmailController',
            'People\Controller\EducationApi' => 'People\Controller\Api\EducationController',
            'People\Controller\ExperienceApi' => 'People\Controller\Api\ExperienceController',
            'People\Controller\CertificateApi' => 'People\Controller\Api\CertificateController',
            'People\Controller\SkillApi' => 'People\Controller\Api\SkillController',
            'People\Controller\LangApi' => 'People\Controller\Api\LangController',
            'People\Controller\AddressApi' => 'People\Controller\Api\AddressController',
            'People\Controller\ContactApi' => 'People\Controller\Api\ContactController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'people' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/people[/]',
                    'defaults' => array(
                        // '__NAMESPACE__' => 'People\Controller',
                        'controller' => 'People\Controller\People',
                        'action' => 'list',
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
                                'controller' => 'People\Controller\People',
                                'action' => 'list',
                                'page' => 1,
                            ),
                        ),
                    ),
                    'profile' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => ':id',
                            'constraints' => array(
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'People\Controller\People',
                                'action' => 'show',
                            ),
                        ),
                    ),
                   /* 'account-termination' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'terminate',
                            'defaults' => array(
                                'controller' => 'People\Controller\People',
                                'action' => 'terminate',
                            ),
                        ),
                    ),*/
                    'find' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'find/:keyword',
                            'defaults' => array(
                                'controller' => 'People\Controller\People',
                                'action' => 'find',
                            ),
                        ),
                    ),
                    'following'=>array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'following',
                            'defaults' => array(
                                'controller' => 'People\Controller\People',
                                'action' => 'following',
                            ),
                        ),
                    ),
                    'follower'=>array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'follower',
                            'defaults' => array(
                                'controller' => 'People\Controller\People',
                                'action' => 'followers',
                            ),
                        ),
                    ),
                    'getUser'=>array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'api/getUser',
                            'defaults' => array(
                                'controller' => 'People\Controller\People',
                                'action' => 'getUser',
                            ),
                        ),
                    ),
                    'action-id' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => ':action/:id',
                            'constraints' => array(
                                'id' => '[0-9]+',
                            )
                        ),
                    ),
                    'part' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'part/:id/:part',
                            'constraints' => array(
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'People\Controller\People',
                                'action' => 'part',
                            ),
                        ),
                    ),
                    'skill' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'skill/:action[/][:id]',
                            'defaults' => array(
                                'controller' => 'People\Controller\Skill',
                            ),
                            'constraints' => array(
                                'id' => '[0-9]+',
                            ),
                        ),
                    ),
                    'email' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'email/:action',
                            'defaults' => array(
                                'controller' => 'People\Controller\Email',
                            ),
                        ),
                    ),
                    'confirm-email' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'email/confirm/:email',
                            'defaults' => array(
                                'controller' => 'People\Controller\Email',
                                'action' => 'confirm'
                            ),
                        ),
                    ),
                    'emailApi' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'email/api/:action',
                            'defaults' => array(
                                'controller' => 'People\Controller\EmailApi',
                            )
                        )
                    ),
                    'educationApi' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'education/api/:action',
                            'defaults' => array(
                                'controller' => 'People\Controller\EducationApi',
                            )
                        )
                    ),
                    'experienceApi' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'experience/api/:action',
                            'defaults' => array(
                                'controller' => 'People\Controller\ExperienceApi',
                            )
                        )
                    ),
                    'skillApi' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'skill/api/:action',
                            'defaults' => array(
                                'controller' => 'People\Controller\SkillApi',
                            )
                        )
                    ),
                    'certificateApi' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'certificate/api/:action',
                            'defaults' => array(
                                'controller' => 'People\Controller\CertificateApi',
                            )
                        )
                    ),
                    'langApi' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'lang/api/:action',
                            'defaults' => array(
                                'controller' => 'People\Controller\LangApi',
                            )
                        )
                    ),
                    'addressApi' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'address/api/:action',
                            'defaults' => array(
                                'controller' => 'People\Controller\AddressApi',
                            )
                        )
                    ),
                     'contactApi' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'contact/api/:action',
                            'defaults' => array(
                                'controller' => 'People\Controller\ContactApi',
                            )
                        )
                    ),
                    'createEmail' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => 'email/create',
                            'defaults' => array(
                                'controller' => 'People\Controller\Email',
                                'action'=>'create'
                            ),
                        ),
                    ),
                    'createEmailPublic' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => 'email/create-public',
                            'defaults' => array(
                                'controller' => 'People\Controller\Email',
                                'action'=>'createPublic'
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'people' => __DIR__ . '/../view',
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
