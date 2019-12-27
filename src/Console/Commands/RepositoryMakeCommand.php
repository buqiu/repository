<?php

namespace Buqiu\Repository\Console\Commands;

use Buqiu\Repository\Console\Commands\Creators\RepositoryCreator;

class RepositoryMakeCommand extends BaseCommand
{
    /**
     * 控制台命令的名称和签名
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:repository';

    /**
     * 控制台命令说明
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';

    /**
     * @param RepositoryCreator $creator
     */
    public function __construct(RepositoryCreator $creator)
    {
        parent::__construct($creator);
    }
}
