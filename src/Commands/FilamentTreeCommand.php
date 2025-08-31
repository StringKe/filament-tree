<?php

namespace StringKe\FilamentTree\Commands;

use Illuminate\Console\Command;

class FilamentTreeCommand extends Command
{
    public $signature = 'filament-tree';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
