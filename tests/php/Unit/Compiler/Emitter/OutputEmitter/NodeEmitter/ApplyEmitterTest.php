<?php

declare(strict_types=1);

namespace PhelTest\Unit\Compiler\Emitter\OutputEmitter\NodeEmitter;

use Phel\Compiler\CompilerFactory;
use Phel\Compiler\Domain\Analyzer\Ast\ApplyNode;
use Phel\Compiler\Domain\Analyzer\Ast\FnNode;
use Phel\Compiler\Domain\Analyzer\Ast\LiteralNode;
use Phel\Compiler\Domain\Analyzer\Ast\PhpVarNode;
use Phel\Compiler\Domain\Analyzer\Ast\VectorNode;
use Phel\Compiler\Domain\Analyzer\Environment\NodeEnvironment;
use Phel\Compiler\Domain\Emitter\OutputEmitter\NodeEmitter\ApplyEmitter;
use Phel\Lang\Symbol;
use PHPUnit\Framework\TestCase;

final class ApplyEmitterTest extends TestCase
{
    private ApplyEmitter $applyEmitter;

    protected function setUp(): void
    {
        $outputEmitter = (new CompilerFactory())
            ->createOutputEmitter();

        $this->applyEmitter = new ApplyEmitter($outputEmitter);
    }

    public function test_php_var_node_and_fn_node_is_infix(): void
    {
        $node = new PhpVarNode(NodeEnvironment::empty(), '+');
        $args = [
            new VectorNode(NodeEnvironment::empty()->withExpressionContext(), [
                new LiteralNode(NodeEnvironment::empty()->withExpressionContext(), 2),
                new LiteralNode(NodeEnvironment::empty()->withExpressionContext(), 3),
                new LiteralNode(NodeEnvironment::empty()->withExpressionContext(), 4),
            ]),
        ];

        $applyNode = new ApplyNode(NodeEnvironment::empty(), $node, $args);
        $this->applyEmitter->emit($applyNode);

        $this->expectOutputString(
            'array_reduce([...((\PhelType::persistentVectorFromArray([2, 3, 4])) ?? [])], function($a, $b) { return ($a + $b); });',
        );
    }

    public function test_php_var_node_but_no_infix(): void
    {
        $node = new PhpVarNode(NodeEnvironment::empty(), 'str');
        $args = [
            new LiteralNode(NodeEnvironment::empty()->withExpressionContext(), 'abc'),
            new VectorNode(NodeEnvironment::empty()->withExpressionContext(), [
                new LiteralNode(NodeEnvironment::empty()->withExpressionContext(), 'def'),
            ]),
        ];

        $applyNode = new ApplyNode(NodeEnvironment::empty(), $node, $args);
        $this->applyEmitter->emit($applyNode);

        $this->expectOutputString('str("abc", ...((\PhelType::persistentVectorFromArray(["def"])) ?? []));');
    }

    public function test_no_php_var_node(): void
    {
        $fnNode = new FnNode(
            NodeEnvironment::empty(),
            [Symbol::create('x')],
            new PhpVarNode(NodeEnvironment::empty()->withReturnContext(), 'x'),
            [],
            isVariadic: true,
            recurs: false,
        );

        $args = [
            new VectorNode(NodeEnvironment::empty()->withExpressionContext(), [
                new LiteralNode(NodeEnvironment::empty()->withExpressionContext(), 1),
            ]),
        ];

        $applyNode = new ApplyNode(NodeEnvironment::empty(), $fnNode, $args);
        $this->applyEmitter->emit($applyNode);

        $this->expectOutputString('(new class() extends \Phel\Lang\AbstractFn {
  public const BOUND_TO = "";

  public function __invoke(...$x) {
    $x = \PhelType::persistentVectorFromArray($x);
    return x;
  }
};)(...((\PhelType::persistentVectorFromArray([1])) ?? []));');
    }
}
