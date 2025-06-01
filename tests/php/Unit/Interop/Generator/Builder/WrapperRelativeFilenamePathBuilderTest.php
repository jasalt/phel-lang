<?php

declare(strict_types=1);

namespace PhelTest\Unit\Interop\Generator\Builder;

use Generator;
use Phel\Interop\Domain\Generator\Builder\WrapperRelativeFilenamePathBuilder;
use PHPUnit\Framework\TestCase;

final class WrapperRelativeFilenamePathBuilderTest extends TestCase
{
    /**
     * @dataProvider providerBuild
     */
    public function test_build(string $phelNs, string $expected): void
    {
        $builder = new WrapperRelativeFilenamePathBuilder();

        self::assertSame($expected, $builder->build($phelNs));
    }

    public static function providerBuild(): Generator
    {
        yield 'simple name' => [
            'project\simple',
            'Project/Simple.php',
        ];

        yield 'filename with dash' => [
            'project\\the_file',
            'Project/TheFile.php',
        ];

        yield 'filename and phel namespace with dash' => [
            'the_project\\the_simple_file',
            'TheProject/TheSimpleFile.php',
        ];
    }
}
