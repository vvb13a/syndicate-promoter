<?php

namespace Syndicate\Promoter\Services\Traits;

use Closure;
use Illuminate\Contracts\Container\BindingResolutionException;
use ReflectionFunction;
use ReflectionParameter;

trait HandlesClosureEvaluation
{
    protected function evaluate($value)
    {
        if (!$value instanceof Closure) {
            return $value;
        }

        $dependencies = [];
        foreach ((new ReflectionFunction($value))->getParameters() as $parameter) {
            $dependencies[] = $this->resolveClosureDependencyForEvaluation($parameter);
        }

        return $value(...$dependencies);
    }

    protected function resolveClosureDependencyForEvaluation(ReflectionParameter $parameter): mixed
    {
        $parameterName = $parameter->getName();

        $defaultWrappedDependencyByName = $this->resolveDefaultClosureDependencyForEvaluationByName($parameterName);

        if (count($defaultWrappedDependencyByName)) {
            return $defaultWrappedDependencyByName[0];
        }

        $staticClass = static::class;

        throw new BindingResolutionException("An attempt was made to evaluate a closure for [{$staticClass}], but [\${$parameterName}] was unresolvable.");
    }

    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'record' => [$this->record],
            'seo' => [$this],
        };
    }
}
