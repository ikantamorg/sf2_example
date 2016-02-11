<?php

namespace Domain\CoreBundle\Util\Interfaces;

interface ErrorsContainerInterface
{
    public function count();
    public function exist();
    public function addError($message);
    public function setError($type, $message);
    public function clear();
    public function getErrorString($separator = "\n");
}
