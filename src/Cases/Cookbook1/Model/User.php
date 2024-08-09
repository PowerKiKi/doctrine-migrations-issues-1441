<?php

declare(strict_types=1);

namespace Application\Cases\Cookbook1\Model;

use Application\Enum\UserRole;
use Application\Enum\UserStatus;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    public ?int $id = null;

    #[ORM\Column(type: 'string')]
    public string $role = UserRole::Visitor->value;

    #[ORM\Column(type: 'string')]
    public string $status = UserStatus::New->value;

    public function setRole(UserRole $role): void
    {
        $this->role = $role->value;
    }

    public function setStatus(UserStatus $status): void
    {
        $this->status = $status->value;
    }
}
