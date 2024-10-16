<?php

declare(strict_types=1);

namespace Application\Cases\PropertyMappingWithEnum\Model;

use Application\Enum\UserRole;
use Application\Enum\UserStatus;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    public ?int $id = null;

    #[ORM\Column(type: 'enum')]
    public UserRole $role = UserRole::Visitor;

    #[ORM\Column(type: 'enum')]
    public UserStatus $status = UserStatus::New;
}
