<?php

declare(strict_types=1);

namespace PhelTest\Unit\Printer\TypePrinter;

use Override;
use Phel\Lang\Collections\Struct\AbstractPersistentStruct;

final class StubStruct extends AbstractPersistentStruct
{
    protected const array ALLOWED_KEYS = ['a', 'b'];

    protected $a;

    protected $b;

    public function __construct(
        private readonly array $allowedKeys,
    ) {
        parent::__construct();
    }

    #[Override]
    public function getAllowedKeys(): array
    {
        return $this->allowedKeys;
    }
}
