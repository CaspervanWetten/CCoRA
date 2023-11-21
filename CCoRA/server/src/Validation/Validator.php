<?php

namespace Cora\Validation;


class Validator
{

    /**
     * Variable which holds the error message the Validator finds for the given
     * input.
     * @var string
     */
    protected $errorMessage;

    /**
     * Associative array which holds the configuration for the Validator.
     * Three types of checks are implemented:
     * min_length
     * max_length
     * custom
     * @var array
     */
    protected $config;

    /**
     * Constructs the validator with the given settings
     * A typical settings array would look something like this:
     * array (
     *  'min_length' => array (
     *                      'argument' => 3
     *                      'message' => "This field does not meet the minimum length requirement"
     *                  ),
     * 'custom' => array(
     *                  1 => array (
     *                          'regex' => /[0-9]+/
     *                          'message' => "This went wrong"
     *                          )
     *              )
     * )
     * @param [type] $settings [description]
     */
    public function __construct($settings)
    {
        $this->config = $settings;
        $this->errorMessage = FALSE;
    }

    /**
     * Get the first set error. If the input has not been validated yet, this
     * will return false.
     * @return string The error message
     */
    public function getError()
    {
        return $this->errorMessage;
    }

    /**
     * Add a custom rule to the configuration
     * @param string $regex   The regex to be checked
     * @param string $message The error when the check fails
     */
    public function addRule($regex, $message)
    {
        if (!isset($this->config['custom']))
            $this->config['custom'] = array();

        array_push(
            $this->config['custom'],
            [
                "regex" => $regex,
                "message" => $message
            ]
        );
    }
    /**
     * Validate the given input string according to the configuration.
     * @param  string $string The string to be validated
     * @return bool           True when no errors where found, false otherwise.
     */
    public function validate($input)
    {
        // check minimum length
        if (isset($this->config['min_length']) && !$this->checkMinLength($input))
        {
            $this->errorMessage = $this->config['min_length']['message'];
            return FALSE;
        }

        // check maximum length
        if (isset($this->config['max_length']) && !$this->checkMaxLength($input))
        {
            $this->errorMessage = $this->config['max_length']['message'];
            return FALSE;
        }

        // check all custom rules
        if (isset($this->config['custom']))
        {
            foreach ($this->config['custom'] as $index => $rule) {
                // var_dump((bool)preg_match($rule['regex'], $string));
                if (!(bool)preg_match($rule['regex'], $input))
                {
                    $this->errorMessage = $rule['message'];
                    return FALSE;
                }
            }
        }
        return TRUE;
    }

    protected function checkMinLength($input)
    {
        return strlen($input) >= $this->config['min_length']['argument'];
    }

    protected function checkMaxLength($input)
    {
        return strlen($input) <= $this->config['max_length']['argument'];
    }
}

 ?>
