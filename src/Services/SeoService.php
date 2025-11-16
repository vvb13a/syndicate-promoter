<?php

namespace Syndicate\Promoter\Services;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use ReflectionMethod;

class SeoService implements Htmlable
{
    use Traits\HandlesClosureEvaluation;
    use Traits\ManagesCoreTags;
    use Traits\ManagesHreflang;
    use Traits\ManagesOpenGraph;
    use Traits\ManagesSchema;
    use Traits\ManagesTwitter;

    public function __construct(protected Model $record)
    {
    }

    public static function make(Model $record): self
    {
        return app(static::class, ['record' => $record]);
    }

    public function toHtml(): string
    {
        return $this->render()->render();
    }

    public function render(): View
    {
        return view(
            view: 'syndicate::promoter.head.seo',
            data: $this->extractPublicMethods()
        );
    }

    public function extractPublicMethods(): array
    {
        $reflection = new ReflectionClass($this);

        $methods = [];

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $methods[$method->getName()] = Closure::fromCallable([$this, $method->getName()]);
        }

        return $methods;
    }
}
