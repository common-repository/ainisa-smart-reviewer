<?php
if ( ! defined( 'ABSPATH' ) ) exit;
return [
    'validations' => [
        // translators: Username is required
        'nullable' => esc_html__('%s is required', 'ainisa-smart-reviewer'),

        // translators: Username should not be empty
        'required' => esc_html__('%s should not be empty', 'ainisa-smart-reviewer'),

        // translators: Age must be required
        'integer' => esc_html__('%s must be integer', 'ainisa-smart-reviewer'),

        // translators: Number must be float
        'float' => esc_html__('%s must be float', 'ainisa-smart-reviewer'),

        // translators: Field must be numeric
        'numeric' => esc_html__('%s must be numeric', 'ainisa-smart-reviewer'),

        // translators: E-mail is wrong e-mail
        'email' => esc_html__('%s is wrong email', 'ainisa-smart-reviewer'),

        // translators: Text should contain only letters
        'alpha' => esc_html__('%s should contain only letters', 'ainisa-smart-reviewer'),

        // translators: Text should contain only letters and numbers
        'alpha_numeric' => esc_html__('%s should contain only letters and numbers', 'ainisa-smart-reviewer'),

        // translators: 12.99.ww.88 is not valie IP address
        'ip' => esc_html__('%s is not valid IP address', 'ainisa-smart-reviewer'),

        // translators: aaa,ee is not valid url
        'url' => esc_html__('%s is not valid url', 'ainisa-smart-reviewer'),

        // translators: Text can have maximum 5 letters
        'max_length' => esc_html__('%1$s can have maximum %2$s letters', 'ainisa-smart-reviewer'),

        // translators: Text can have minimum 5 letters
        'min_length' => esc_html__('%1$s can have minimum %2$s letters', 'ainisa-smart-reviewer'),

        // translators: Text can have exactly 5 letters
        'exact_length' => esc_html__('%1$s can have %2$s letters ', 'ainisa-smart-reviewer'),

        // translators: Text length can be 5 - 10 letters
        'between' => esc_html__('%1$s length can be %2$s - %3$s letters', 'ainisa-smart-reviewer'),

        // translators: Number1 and Number2 should be equal
        'equals' => esc_html__('%1$s and %2$s must be equal', 'ainisa-smart-reviewer'),

        // translators: Number can be minimum 5
        'min_number' => esc_html__('%1$s can be minimum %2$s', 'ainisa-smart-reviewer'),

        // translators: Number can be maximum 5
        'max_number' => esc_html__('%1$s can be maximum %2$s', 'ainisa-smart-reviewer'),

        // translators: Number can be between 5-10
        'between_numbers' => esc_html__('%1$s should be between %2$s and %3$s', 'ainisa-smart-reviewer'),

        // translators: Text should have between 50 and 100 characters
        'between_length' => esc_html__('%1$s should have between %2$s and %3$s characters', 'ainisa-smart-reviewer'),

        // translators: Is Valid should be 0 or 1.
        'boolean' => esc_html__('%s should be 0 or 1.', 'ainisa-smart-reviewer'),

        // translators: mail@mail.com exists
        'email_exists' => esc_html__('%s exists.', 'ainisa-smart-reviewer'),

        // translators: jjjj exists
        'username_exists' => esc_html__('%s exists.', 'ainisa-smart-reviewer'),
    ],
    'additional' => [

        // translators: Please select post
        'Please select post' => esc_html__('Please select post !', 'ainisa-smart-reviewer'),

        // translators: Select
        'select' => esc_html__('Select', 'ainisa-smart-reviewer'),
    ],

];