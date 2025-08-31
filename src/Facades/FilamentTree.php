<?php

namespace StringKe\FilamentTree\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \StringKe\FilamentTree\FilamentTree
 */
class FilamentTree extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \StringKe\FilamentTree\FilamentTree::class;
    }
}
