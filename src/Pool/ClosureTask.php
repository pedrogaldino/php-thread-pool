<?php

namespace Galdino\Threads\Pool;

class ClosureTask extends Task
{
    protected $closure;

    public function __construct(\Closure $closure)
    {
        $this->closure = $closure;

        parent::__construct('Closure Task');
    }

    public function onExecute()
    {
        $closure = $this->closure;
        $closure($this);
    }

}