<?php declare(strict_types = 1);

return [
    [
        'GET',
        '/',
        ['Nelwhix\ContactForm\Handlers\MailHandler', 'index'],
    ],
    [
        'POST',
        '/',
        ['Nelwhix\ContactForm\Handlers\MailHandler', 'send']
    ]
];