<?php

declare(strict_types=1);

namespace Application\Cases\PropertyMapping\Model;

use Application\Enum\UserRole;
use Application\Enum\UserStatus;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    public ?int $id = null;

    #[ORM\Column]
    public UserRole $role = UserRole::Visitor;

    #[ORM\Column]
    public UserStatus $status = UserStatus::New;
}
