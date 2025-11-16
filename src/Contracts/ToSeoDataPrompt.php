<?php

namespace Syndicate\Promoter\Contracts;

use Syndicate\Promoter\Prompts\SeoDataPrompt;

interface ToSeoDataPrompt
{
    public function toSeoDataPrompt(): SeoDataPrompt;
}
