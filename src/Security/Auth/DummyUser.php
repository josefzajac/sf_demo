<?php declare(strict_types=1);

namespace Document\Security\Auth;

use Symfony\Component\Security\Core\User\UserInterface;

class DummyUser implements UserInterface
{
    public function getRoles(): array
    {
        return [];
    }

    public function eraseCredentials()
    {
    }

    public function getUserIdentifier(): string
    {
        return '1-dummy';
    }
}
