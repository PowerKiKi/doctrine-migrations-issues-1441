<?php

declare(strict_types=1);

namespace Application\Cases\WithAutomatedEnum\Type;

class UserRole extends RealEnumType
{
    protected function getEnumType(): string
    {
        return \Application\Enum\UserRole::class;
    }
}
