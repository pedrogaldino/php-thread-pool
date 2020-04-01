<?php

namespace Galdino\Threads\Pool;

use parallel\Channel;
use parallel\Runtime;

class Queue
{
    protected $name;

    protected $runtime;

    protected $channel;

    protected $bootstrap;

    protected $tasks = 0;

    public $id;

    public function __construct(string $name, Bootstrap $bootstrap = null)
    {
        $this->name = $name;
        $this->runtime = new Runtime();

        $this->id = uniqid();

        $this->channel = new Channel(Channel::Infinite);
        $this->bootstrap = $bootstrap;
    }

    public function getName()
    {
        return $this->name;
    }

    public function isAvailable()
    {
        return true;
    }

    public function setBootstrap(Bootstrap $bootstrap)
    {
        $this->bootstrap = $bootstrap;
        return $this;
    }

    public function getBootstrap() : Bootstrap
    {
        return $this->bootstrap;
    }

    public function hasBootstrap()
    {
        return $this->bootstrap instanceof Bootstrap;
    }

    public function getId()
    {
        return $this->id;
    }

    private function runtimeListener()
    {
        return function ($name, Channel $channel, array $bootstraps) {
            foreach ($bootstraps as $bootstrap) {
                try {
                    require_once $bootstrap;
                } catch (\Exception $exception) {
                    print "Error ocurred on autoload: " . $exception->getMessage() . PHP_EOL;
                    return;
                }
            }

            print "Queue: $name started " . PHP_EOL;

            while ($data = $channel->recv()) {
                print 'Task: ' . $data->getName() .' [PROCESSING] on queue: ' . $name . PHP_EOL;

                $data->setProcessing(true);
                $data->onExecute();
                $data->setProcessing(false);

                print 'Task: ' . $data->getName() .' [FINISHED] on queue: ' . $name . PHP_EOL;

                $channel->send(false);
            }

            print "Queue: $name finished" . PHP_EOL;
        };
    }

    public function tasks() : int
    {
        return $this->tasks;
    }

    protected function updateFutures()
    {
//        dump($this->futures);

//        $this->futures = array_filter($this->futures, function(Future $future) {
//            return ($future->done() || $future->cancelled());
//        });
    }

    public function addTask(Task $task)
    {
        $future = $this->runtime->run($this->runtimeListener(), [
            $this->getName(),
            $this->channel,
            $this->getBootstrap()->getFiles()
        ]);

        $this->channel->send($task);

        $this->tasks++;
    }

}
