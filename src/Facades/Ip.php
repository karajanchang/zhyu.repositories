<?php

namespace Zhyu\Facades;

use Illuminate\Support\Facades\Facade;

class Ip extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Ip';
    }
}