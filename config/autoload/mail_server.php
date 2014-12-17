<?php

return [
    'default' => 3,// 8,
    'list' => [
        10 => [//APAC
            0 =>
            [
                'enable' => false,
                'config' => [
                    'name' => 'email-smtp.us-west-2.amazonaws.com',
                    'host' => 'email-smtp.us-west-2.amazonaws.com',
                    'port' => 587,
                    'connection_class' => 'login',
                    'connection_config' => [
                        'username' => 'ses-smtp-user.linspira-test',
                        'password' => 'AKIAIKS7MBKL6DILMUIA',
                        'ssl' => 'tls',
                    ]
                ]
            ],
            1 => [
                'enable' => false,
                'config' => [
                    'name' => 'linkaide.com',
                    'host' => 'linkaide.com',
                    'port' => 587,
                    'connection_class' => 'login',
                    'connection_config' => [
                        'username' => 'system@linkaide.com',
                        'password' => 'fVckM3!fuCan',
                        'ssl' => 'tls',
                    ]
                ]
            ]
        ],
        8 => [//America
            /// http://support.godaddy.com/help/article/4714/setting-up-your-email-address-with-imap
            0 => [
                'enable' => false,
                'config' => [
                    'name' => 'linspira.com',
                    'host' => 'smtpout.secureserver.net',
                    'port' => 3535,
                    'connection_class' => 'login',
                    'connection_config' => [
                        'username' => 'system@linspira.com',
                        'password' => 'fVckM3!fuCan',
                    // 'ssl' => 'tls',
                    ]
                ]
            ],
        ],
        3 => [//Localhost
            
            0 => [
                'enable' => true,
                'config' => [
                    'name' => 'localhost',
                    'host' => 'smtp.gmail.com',
                    'port' => 587,
                    'connection_class' => 'login',
                    'connection_config' => [
                        'username' => 'youremail@gmail.com',
                        'password' => 'your_gmail_password',
                     'ssl' => 'tls',
                    ]
                ]
            ],
        ],
    ],
];
