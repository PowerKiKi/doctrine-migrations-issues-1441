<?php

declare(strict_types=1);

namespace Application\Enum;

enum UserRole: string
{
    case Visitor = 'visitor';
    case Member = 'member';
    case Admin = 'admin';
}


