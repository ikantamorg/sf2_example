<?php
/**
 * User: Dred
 * Date: 19.09.13
 * Time: 16:53
 */

namespace Domain\CoreBundle\Util;

use Domain\CoreBundle\Util\Interfaces\ErrorsContainerInterface;

class ErrorsContainer implements ErrorsContainerInterface
{

    protected $errors = [];

    public function __construct()
    {

    }

    public function count()
    {
        return count($this->errors);
    }

    public function exist()
    {
        return !!$this->count();
    }

    public function setError($type, $message)
    {
        $this->errors[$type] = $message;
        return $this;
    }
    public function addError($message)
    {
        $this->errors[] = $message;
        return $this;
    }

    public function clear()
    {
        $this->errors = [];
        return $this;
    }

    public function getErrorString($separator = "\n")
    {
        return implode($separator, $this->errors);
    }
}
