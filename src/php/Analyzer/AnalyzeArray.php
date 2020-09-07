<?php

declare(strict_types=1);

namespace Phel\Analyzer;

use Phel\Ast\ArrayNode;
use Phel\Lang\PhelArray;
use Phel\NodeEnvironment;

final class AnalyzeArray
{
    use WithAnalyzer;

    public function analyze(PhelArray $array, NodeEnvironment $env): ArrayNode
    {
        $values = [];
        $valueEnv = $env->withContext(NodeEnvironment::CONTEXT_EXPRESSION);

        foreach ($array as $value) {
            $values[] = $this->analyzer->analyze($value, $valueEnv);
        }

        return new ArrayNode($env, $values, $array->getStartLocation());
    }
}
