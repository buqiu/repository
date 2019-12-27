<?php

namespace Buqiu\Repository\Console\Commands;

use Buqiu\Repository\Console\Commands\Creators\BaseCreator;

class CriteriaMakeCommand extends BaseCommand
{
    /**
     * 控制台命令的名称和签名
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:criteria';

    /**
     * 控制台命令说明
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new criteria(标准) class';

    /**
     * CriteriaMakeCommand constructor.
     * @param BaseCreator $creator
     */
    public function __construct(BaseCreator $creator)
    {
        parent::__construct($creator);
    }
}
