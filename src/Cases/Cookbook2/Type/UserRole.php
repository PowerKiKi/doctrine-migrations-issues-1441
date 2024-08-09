<?php

declare(strict_types=1);

namespace Application\Cases\Cookbook2\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;

class UserRole extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return "ENUM('visitor', 'member', 'admin')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if (!\Application\Enum\UserRole::tryFrom($value)) {
            throw new InvalidArgumentException("Invalid status");
        }

        return $value;
    }

    public function getMappedDatabaseTypes(AbstractPlatform $platform): array
    {
        return ['enum'];
    }
}
