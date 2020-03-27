<?php


namespace Galdino\Pool;


class Pool
{
    protected $bootstrap;

    protected $queues = [];

    public function __construct(Bootstrap $bootstrap = null, int $autoCreateQueues = 0)
    {
        if(empty($bootstrap)) {
            $this->bootstrap = new Bootstrap();
        } else {
            $this->bootstrap = $bootstrap;
        }

        if($autoCreateQueues) {
            for ($q = 0; $q < $autoCreateQueues; $q++) {
                $this->addQueue($q + 1);
            }
        }
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

    public function hasQueue($queue)
    {
        return isset($this->queues[$queue]);
    }

    public function addQueue($queue = null)
    {
        $acceptable = ['integer', 'string', 'double'];

        if($queue instanceof Queue) {
            if(!$queue->hasBootstrap() && $this->hasBootstrap()) {
                $queue->setBootstrap($this->getBootstrap());
            }

            $this->queues[$queue->getName()] = $queue;
        } else if(empty($queue)) {
            $queue = count($this->queues) + 1;

            $this->queues[$queue] = new Queue((string) $queue, $this->getBootstrap());
        } else if(in_array(gettype($queue), $acceptable)) {
            if(isset($this->queues[$queue])) {
                throw new \Exception("Queue $queue already exists");
            }

            $this->queues[(string) $queue] = new Queue((string) $queue, $this->getBootstrap());
        } else {
            throw new \Exception('Invalid queue type. Only ' . implode($acceptable, ', ') . ' is acceptable.');
        }

        return $this;
    }

    public function getBetterQueue() : Queue
    {
        $ordered = array_sort($this->queues, function(Queue $queue) {
            return $queue->tasks();
        });

        return $this->queues[
            array_key_first($ordered)
        ];
    }

    public function addTask(Task $task, $forceQueueCreation = false)
    {
        if(empty($this->queues)) {
            $queueName = count($this->queues) + 1;

            $this->addQueue($queueName);
        } elseif ($forceQueueCreation) {
            $queueName = count($this->queues) + 1;

            $this->addQueue($queueName);
        } else {
            $this
                ->getBetterQueue()
                ->addTask($task);
        }

        return $this;
    }

    public function addTaskTo($queueName, Task $task)
    {
        if(!$this->hasQueue($queueName)) {
            throw new \Exception('Queue ' . $queueName . ' not found');
        }

        $this->queues[$queueName]->addTask($task);

        return $this;
    }
}
