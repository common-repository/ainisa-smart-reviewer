<?php
if ( ! defined( 'ABSPATH' ) ) exit;
return [
    'prompt_parts' => [
        'default_prompt' => 'Create review for [post_title]. Title should have maximum 200 characters. Review should have between 200 - 1000 characters.',
        'make_grammar_mistakes' => 'Make maximum 6 grammar, spelling and letters mistakes such as lowercase, uppercase in review text and title',
        'return_type' => 'Create separate title and review. Return title and review in object not as text',
    ],
    'openai' => [
        'origin' => 'https://api.openai.com',
        'version' => 'v1',
        'model_with_groups' => [
            'GPT-3.5' => [
                'gpt-3.5-turbo',
                'gpt-3.5-turbo-16k',
                'gpt-3.5-turbo-instruct',
            ],
            'GPT-4' => [
                'gpt-4', 'gpt-4o',
                'gpt-4-turbo',
                'gpt-4-vision-preview'
            ],
        ]
    ]
];