<?php
/**
 * Input Validation Middleware
 * Sanitize and validate user inputs
 */

class ValidationMiddleware
{
    /**
     * Sanitize all inputs
     */
    public static function sanitizeInputs()
    {
        $_GET = self::sanitizeArray($_GET);
        $_POST = self::sanitizeArray($_POST);
        $_REQUEST = self::sanitizeArray($_REQUEST);
    }

    /**
     * Sanitize array
     */
    private static function sanitizeArray($array)
    {
        $sanitized = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = self::sanitizeArray($value);
            } else {
                $sanitized[$key] = self::sanitizeString($value);
            }
        }
        return $sanitized;
    }

    /**
     * Sanitize string
     */
    private static function sanitizeString($string)
    {
        return trim(htmlspecialchars(stripslashes($string), ENT_QUOTES, 'UTF-8'));
    }

    /**
     * Validate email
     */
    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate phone
     */
    public static function validatePhone($phone)
    {
        return preg_match('/^[0-9\+\-\s\(\)]+$/', $phone) && strlen($phone) >= 10;
    }

    /**
     * Validate URL
     */
    public static function validateURL($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validate required fields
     */
    public static function validateRequired($data, $fields)
    {
        $errors = [];
        foreach ($fields as $field) {
            if (empty($data[$field])) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }
        return $errors;
    }

    /**
     * Validate minimum length
     */
    public static function validateMinLength($data, $field, $minLength)
    {
        if (isset($data[$field]) && strlen($data[$field]) < $minLength) {
            return ucfirst(str_replace('_', ' ', $field)) . ' must be at least ' . $minLength . ' characters';
        }
        return null;
    }

    /**
     * Validate maximum length
     */
    public static function validateMaxLength($data, $field, $maxLength)
    {
        if (isset($data[$field]) && strlen($data[$field]) > $maxLength) {
            return ucfirst(str_replace('_', ' ', $field)) . ' must not exceed ' . $maxLength . ' characters';
        }
        return null;
    }

    /**
     * Validate numeric
     */
    public static function validateNumeric($data, $field)
    {
        if (isset($data[$field]) && !is_numeric($data[$field])) {
            return ucfirst(str_replace('_', ' ', $field)) . ' must be numeric';
        }
        return null;
    }

    /**
     * Validate integer
     */
    public static function validateInteger($data, $field)
    {
        if (isset($data[$field]) && !is_int((int)$data[$field])) {
            return ucfirst(str_replace('_', ' ', $field)) . ' must be an integer';
        }
        return null;
    }
}
