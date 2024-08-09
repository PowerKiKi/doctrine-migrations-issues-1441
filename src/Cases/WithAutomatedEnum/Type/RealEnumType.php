<?php

declare(strict_types=1);

namespace Application\Cases\WithAutomatedEnum\Type;

use BackedEnum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;

/**
 * MariaDB enum based on native PHP backed enum.
 */
abstract class RealEnumType extends Type
{
    /**
     * Returns the FQCN of the native PHP enum.
     *
     * @return class-string<BackedEnum>
     */
    abstract protected function getEnumType(): string;

    final public function getQuotedPossibleValues(): string
    {
        return implode(', ', array_map(fn (string $str) => "'" . $str . "'", $this->getPossibleValues()));
    }

    public function getSqlDeclaration(array $column, AbstractPlatform $platform): string
    {
        $sql = 'ENUM(' . $this->getQuotedPossibleValues() . ')';

        return $sql;
    }

    private function getPossibleValues(): array
    {
        return array_map(fn (BackedEnum $str) => $str->value, $this->getEnumType()::cases());
    }

    /**
     * @param ?string $value
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?BackedEnum
    {
        if ($value === null || '' === $value) {
            return null;
        }

        if (!is_string($value)) {
            throw new InvalidArgumentException("Invalid '" . $value . "' value fetched from database for enum " . get_class($this));
        }

        return $this->getEnumType()::from($value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value) && $this->getEnumType()::tryFrom($value)) {
            return $value;
        }

        if (!is_object($value) || !is_a($value, $this->getEnumType())) {
            throw new InvalidArgumentException("Invalid '" . $value . "' value to be stored in database for enum " . get_class($this));
        }

        return $value->value;
    }

    public function getMappedDatabaseTypes(AbstractPlatform $platform): array
    {
        return ['enum'];
    }
}
