<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Custom validator class by AiNisa. Can be used to make validations easier.Has below validation rules:
 * nullable, required, numeric, boolean, integer, min_length, max_length, exact_length, between_numbers, between,
 * min_number, max_number, float, email, ip, url, alpha, alpha_numeric, equals
 * Use validate method in order to run validation for given inputs
 */
class AiNisaValidator {
    /**
     * Array of errors after validation
     * @var array
     */
    public $errors = [];

    /**
     * Array of error parameters after validation
     * @var array
     */
    public $errorParameters = [];

    /**
     * Input data to validate
     * @var array
     */
    public $inputs = [];

    /**
     * Rules which used during validation
     * @var array
     */
    public $rules = [];

    /**
     * Translation domain
     * @var string
     */
    public $domain = '';

    /**
     * Namings for input names
     * @var array
     */
    public $inputNamings = [];

    /**
     * Class constructor
     * @param array $inputs
     * @param string $domain
     * @param array $inputNamings
     */
    public function __construct(array $inputs, string $domain, array $inputNamings)
    {
        $this->inputs = $inputs;
        $this->domain = $domain;
        $this->inputNamings = $inputNamings;
    }

    /**
     * Get array of validation translations
     * @return array
     */
    public function getTranslations()
    {
        $domain = $this->domain;
        $translations = require AINISA_SMART_REVIEWER_PATH.'config/validations.php';
        return $translations['validations'];
    }

    /**
     * Validation method
     * @param array $rules
     * @return array
     */
    public function validate(array $rules)
    {
        $this->rules = $rules;

        foreach ($rules as $key => $rules) {
            foreach ($rules as $rule) {
                $args = [$key];
                if(str_contains($rule, ':') !== false) {
                    $argsArray = explode(':', $rule);
                    if(str_contains($argsArray[1], ',') !== false) {
                        $subArgsArray = explode(',', $argsArray[1]);
                        foreach ($subArgsArray as $subArg) {
                            $args[] = $subArg;
                        }
                    } else {
                        $args[] = $argsArray[1];
                    }

                    $rule = $argsArray[0];
                }
                $isInvalid = call_user_func_array([$this, $rule], $args);
                if($isInvalid === true) {
                    if(!array_key_exists($key, $this->errors)) {
                        $this->errors[$key] = [];
                    }
                    $this->errors[$key][] = $rule;
                    $args[0] = $this->inputNamings[$args[0]];
                    $this->errorParameters[$key] = $args;
                }
            }

        }

        return $this->errors;
    }

    /**
     * Check if there is any error
     * @return bool
     */
    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    /**
     * Get errors array
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param $setting
     * @param $code
     * @param string $type - error or warning
     * @return void
     */
    public function addToWpSettingsError($setting, $code, $type = 'error')
    {
        $tr = $this->getTranslations();
        foreach ($this->getErrors() as $key => $errors) {
            foreach ($errors as $error) {
                $inputArgs = $this->errorParameters[$key];
                add_settings_error( $setting, $code, vsprintf($tr[$error], $inputArgs), $type );
            }
        }
    }

    /**
     * Create array of errors
     * Can be used with rest apis
     * @return array
     */
    public function createErrorsList()
    {
        $tr = $this->getTranslations();
        $errorsList = [];
        foreach ($this->getErrors() as $key => $errors) {
            foreach ($errors as $error) {
                $inputArgs = $this->errorParameters[$key];
                $errorsList[] = vsprintf($tr[$error], $inputArgs);
            }
        }

        return $errorsList;
    }

    /**
     * Check if field is not required
     * @param string|int $key
     * @return bool
     */
    protected function nullable($key) {
        if(!array_key_exists($key, $this->inputs)) {
            return true;
        }
        return false;
    }

    /**
     * Check if field is required
     * @param string|int $key
     * @return bool
     */
    protected function required($key)
    {
        if(empty($this->inputs[$key])) {
            return true;
        }
        return false;
    }

    /**
     * Validate maximum length of string
     * @param string|int $key
     * @param int $length
     * @return bool
     */
    protected function max_length($key, int $length)
    {
        if(mb_strlen($this->inputs[$key], 'utf-8') > $length) {
            return true;
        }
        return false;
    }

    /**
     * Validate minimum length of string
     * @param string|int $key
     * @param int $length
     * @return bool
     */
    protected function min_length($key, int $length)
    {
        if(mb_strlen($this->inputs[$key], 'utf-8') < $length) {
            return true;
        }
        return false;
    }

    /**
     * Validate length of string
     * @param string|int $key
     * @param int $param1
     * @param int $param2
     * @return bool
     */
    protected function between_length($key, int $param1, int $param2)
    {
        if(mb_strlen($this->inputs[$key], 'utf-8') >= $param1 && mb_strlen($this->inputs[$key], 'utf-8') <= $param2) {
            return false;
        }
        return true;
    }

    /**
     * Validate exact length of string
     * @param string|int $key
     * @param $length
     * @return bool
     */
    protected function exact_length($key, $length) {
        return !(mb_strlen($this->inputs[$key], 'utf-8') == $length);
    }

    /**
     * Validate if field equals to given parameter
     * @param string|int $key
     * @param $param
     * @return bool
     */
    protected function equals($key, $param) {
        return !($this->inputs[$key] == $param);
    }

    /**
     * Validate minimum value of number
     * @param string|int $key
     * @param $param
     * @return bool
     */
    protected function min_number($key, $param)
    {
        if(is_numeric($this->inputs[$key]) && $this->inputs[$key] >= $param) {
            return false;
        }
        return true;
    }

    /**
     * Validate maximum value of number
     * @param string|int $key
     * @param $param
     * @return bool
     */
    protected function max_number($key, $param)
    {
        if(is_numeric($this->inputs[$key]) && $this->inputs[$key] <= $param) {
            return false;
        }
        return true;
    }

    /**
     * Validate value of number
     * @param string|int $key
     * @param $param1
     * @param $param2
     * @return bool
     */
    protected function between_numbers($key, $param1, $param2)
    {
        if(is_numeric($this->inputs[$key]) && $this->inputs[$key] >= $param1 && $this->inputs[$key] <= $param2) {
            return false;
        }
        return true;
    }

    /**
     * Check if value is number
     * @param string|int $key
     * @return bool
     */
    protected function numeric($key) {
        return !is_numeric($this->inputs[$key]);
    }

    /**
     * Check if value is email
     * @param string|int $key
     * @return bool
     */
    protected function email($key) {
        return !filter_var($this->inputs[$key], FILTER_VALIDATE_EMAIL);
    }

    /**
     * Check if value is integer
     * @param string|int $key
     * @return bool
     */
    protected function integer($key) {
        return !((is_int($this->inputs[$key]) || ($this->inputs[$key] == (string)(int)$this->inputs[$key])));
    }

    /**
     * Check if value is float
     * @param $key
     * @return bool
     */
    protected function float($key) {
        return !(is_float($this->inputs[$key]) || ($this->inputs[$key] == (string) (float) $this->inputs[$key]));
    }

    /**
     * Check if value has alpha characters
     * @param string|int $key
     * @return bool
     */
    protected function alpha($key) {
        return !(preg_match("#^[a-zA-ZÀ-ÿ]+$#", $this->inputs[$key]) == 1);
    }

    /**
     * Check if value has alphanumeric characters
     * @param string|int $key
     * @return bool
     */
    protected function alpha_numeric($key) {
        return !(preg_match("#^[a-zA-ZÀ-ÿ0-9]+$#", $this->inputs[$key]) == 1);
    }

    /**
     * Check if value is valid ip address
     * @param string|int $key
     * @return bool
     */
    protected function ip($key) {
        return !filter_var($this->inputs[$key], FILTER_VALIDATE_IP);
    }

    /**
     * Check if value is valid url
     * @param string|int $key
     * @return bool
     */
    protected function url($key) {
        return !filter_var($this->inputs[$key], FILTER_VALIDATE_URL);
    }

    /**
     * Check if value is boolean
     * @param string|int $key
     * @return bool
     */
    protected function boolean($key)
    {
        return !(($this->inputs[$key] == 0 || $this->inputs[$key] == 1) || ($this->inputs[$key] == 'no' || $this->inputs[$key] == 'yes'));
    }

    /**
     * Check if user email exists
     * @param string|int $key
     * @return bool
     */
    protected function email_exists($key) {
        if(email_exists($this->inputs[$key]) ===  false) {
            return false;
        }
        return true;
    }

    /**
     * Check if username exists
     * @param string|int $key
     * @return bool
     */
    protected function username_exists($key) {
        return !username_exists($this->inputs[$key]);
    }
}