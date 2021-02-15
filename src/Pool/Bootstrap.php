<?php

namespace Galdino\Threads\Pool;


class Bootstrap
{
    protected $files = [];

    public function __construct(array $files = [], $tryAutoloadVendorBootstrap = true)
    {
        if($tryAutoloadVendorBootstrap) {
            if ($autoload = $this->getVendorBootstrap()) {
                $this->addFile($autoload);
            }
        }

        foreach ($files as $file) {
            $this->addFile($file);
        }
    }

    protected function getVendorBootstrap()
    {
        return realpath(getcwd() . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');
    }

    public function addFile($filePath)
    {
        if(!file_exists($filePath)) {
            throw new \Exception('File ' . $filePath . ' not found for autoload');
        }

        $this->files[] = $filePath;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public static function addFiles(array $files, $tryAutoloadVendorBootstrap = true)
    {
        return new Bootstrap($files, $tryAutoloadVendorBootstrap);
    }

}
