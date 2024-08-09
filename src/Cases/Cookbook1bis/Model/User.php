<?php

declare(strict_types=1);

namespace Application\Cases\Cookbook1bis\Model;

use Application\Enum\UserRole;
use Application\Enum\UserStatus;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    public ?int $id = null;

    #[ORM\Column(type: 'string', columnDefinition: "ENUM('visitor', 'member', 'admin')")]
    public UserRole $role = UserRole::Visitor;

    #[ORM\Column(type: 'string', columnDefinition: "ENUM('new', 'active', 'archived')")]
    public UserStatus $status = UserStatus::New;
}
