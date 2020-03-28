<?php

namespace Galdino\Threads\Pool;

abstract class Task
{
    protected $name;

    protected $processing = false;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public final function setProcessing(bool $processing)
    {
        $this->processing = $processing;

        return $this;
    }

    public final function isProcessing()
    {
        return $this->processing;
    }

    abstract function onExecute();
}
