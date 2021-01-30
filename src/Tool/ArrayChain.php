<?php

namespace App\Tool;

use Exception;

class ArrayChain
{
    public static function value($variable, ...$keys)
    {
        if (empty($keys)) {
            return $variable;
        }

        $key = array_shift($keys);

        if (is_array($variable)) {
            if (array_key_exists($key, $variable)) {
                if (empty($keys)) {
                    return $variable[$key];
                }

                return self::value($variable[$key], ...$keys);
            }

            $errorMessage = 'Chain key "%s" is not set in array evaluated from method "%s". Available keys: ' .
                implode(', ', array_keys($variable));
        }
        else {

            $errorMessage = 'Chain key "%s" cannot be accessed in non-array variable evaluated from method "%s".';
        }

        list(, $method) = explode('::', __METHOD__);
        $callerMethod = '';
        foreach (debug_backtrace() as $item) {
            if ($item['function'] !== $method) {
                $callerMethod = (isset($item['class']) ? ($item['class'] . '::') : '') . $item['function'];
                break 1;
            }
        }
        throw new Exception(sprintf($errorMessage, $key, $callerMethod));
    }
}