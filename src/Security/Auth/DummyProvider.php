<?php declare(strict_types=1);

namespace Document\Security\Auth;

class DummyProvider
{
    public function returnDummy(): DummyUser
    {
        return new DummyUser();
    }
}
