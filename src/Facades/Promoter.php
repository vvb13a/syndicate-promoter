<?php

namespace Syndicate\Promoter\Facades;

use Illuminate\Support\Facades\Facade;
use Syndicate\Promoter\Services\PromoterService;

/**
 * @mixin PromoterService
 */
class Promoter extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return PromoterService::class;
    }
}
