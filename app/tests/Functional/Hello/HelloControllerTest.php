<?php

namespace App\Tests\Functional\Hello;

use App\Entity\Enum\UserRole;
use App\Tests\Functional\FunctionalTestCase;

class HelloControllerTest extends FunctionalTestCase
{
    public function testHello(): void
    {
        $this->httpClient->loginUser($this->createUser([UserRole::ROLE_USER]));
        $this->httpClient->request('GET', '/hello/myname');

        // Validate a successful response and some content
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('p', 'Hello myname asdasdasd!');
    }
}