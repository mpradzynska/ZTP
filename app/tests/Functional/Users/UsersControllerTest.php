<?php

namespace App\Tests\Functional\Users;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Functional\FunctionalTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class UsersControllerTest extends FunctionalTestCase
{
    private KernelBrowser $client;
    private UserRepository $usersRepository;

    /**
     * @throws ServiceCircularReferenceException When a circular reference is detected
     * @throws ServiceNotFoundException          When the service is not defined
     */
    public function setUp(): void
    {
        parent::setUp();
        $container = static::getContainer();
        $this->usersRepository = $container->get(UserRepository::class);
    }

    public function testGalleriesCorrectlyListed(): void
    {
        $user = new User();
        $user->setEmail('email@gmail.com');
        $user->setPassword('passwd');

        $this->em->persist($user);
        $this->em->flush();

        $this->httpClient->loginUser($this->createUser([UserRole::ROLE_USER]));
        $this->httpClient->request('GET', '/users');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('li', 'email@gmail.com');
    }
}