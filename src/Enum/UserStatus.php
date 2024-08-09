<?php

declare(strict_types=1);

namespace Application\Enum;

enum UserStatus: string
{
    case New = 'new';
    case Active = 'active';
    case Archived = 'archived';
}
