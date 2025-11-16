<?php

namespace Syndicate\Promoter\Services\Traits;

use Closure;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Graph;

trait ManagesSchema
{
    protected Graph|BaseType|Closure|null $schema = null;

    public function schema(Graph|BaseType|Closure $schema): self
    {
        $this->schema = $schema;

        return $this;
    }

    public function getSchema(): Graph|BaseType|null
    {
        return $this->evaluate($this->schema);
    }
}
