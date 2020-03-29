
# PHP Thread Pool

This package is a simple PHP Thread Pool manager to create concurrent tasks easily using a great and simple interface.

It's require PHP 7.4 Thread Safe and PHP Parallel extension.

## Instalation

You can install the required extension on your PHP using PECL:

    pecl install paralell

Package Installation:

    composer require pedrogaldino/php-thread-pool


## Quick Start

### Tasks
Task is the place to put the code that will be executed inside a queue. Basically the code that your need to run in a concurrent way without block any parent code execution.

Let's create our task:
  
    <?php
    
    namespace App\Tasks;
    
    use Galdino\Threads\Pool\Task;
    
    class MyTask extends Task {
    
        public function onExecute()
        {
            print "Task is started" . PHP_EOL;  
            sleep(10);  
            print "Task is finished" . PHP_EOL;  
        }
        
    }

Now, let's create our Task's Pool and dispatch the task to be processed:

    <?php
      
    use Galdino\Threads\Pool\Bootstrap;  
    use Galdino\Threads\Pool\Pool;
    use App\Tasks\MyTask;
    
    $bootstrap = new Bootstrap();  
    $pool = new Pool($bootstrap);
    
    $myTask = new MyTask('Task name');
    
    $pool->addTask($myTask);

That's it. The pool will auto create a Queue to process this task. 

### Bootstrap

The bootstrap contains files that your application needs to run when your task execute. You can pass to the constructor, all the files needed.

    $bootstrap = new Bootstrap([
	    'boostrap/my_application_bootstrap.php'
    ]);

The bootstrap class will try to require the composer autoload file automatically if exists. If you want to disable this functionality set `false` to the second parameter:

    $bootstrap = new Bootstrap([
	    'boostrap/my_application_bootstrap.php'
    ], false);

Another way to init your bootstrap:

    $bootstrap = Bootstrap::addFiles([  
	    'bootstrap_file.php'  
    ]);

### Pool
The pool will auto manager the queues and tasks execution.

You can tell to the pool for auto create how many queues you want.

    // auto create 5 queues according to the tasks demand
    $pool = new Pool($bootstrap, 5);  


### Queues
Every queue process 1 task at time.

You can manually add new queues to the pool:

    $pool->addQueue('My queue name');

If you don't need the pool, you can create a queue separately too
  
    use Galdino\Threads\Pool\Bootstrap;
    use Galdino\Threads\Pool\Queue;
    
    $bootstrap = new Bootstrap();
    $queue = new Queue('My queue name', $bootstrap);
    
    $queue->addTask($myTask);

Remember, Tasks don't share memory between the parent Thread. So it's important to always load any bootstrap that your application needs.
