<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

use Symfony\Component\Validator\Validation;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;



use App\Entity\User;

class AuthentificationTest extends KernelTestCase
{

    public function getEntity(): User
    {
        return (new User())
            ->setUsername("Test")
            ->setPlainPassword("132")
            ->setEmail("test@test.com");
    }

    public function assertHasErrors(User $user, int $number = 0)
    {
        self::bootKernel();

        $validator = Validation::createValidatorBuilder()
                        ->enableAnnotationMapping(true)
                        ->addDefaultDoctrineAnnotationReader()
                        ->getValidator();
        
        $errors = $validator->validate($user);

        $messages = [];
    
        foreach($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }

        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    public function testInvalidBlankEmailEntity()
    {
        $this->assertHasErrors($this->getEntity()->setEmail(''), 1);
    }

    public function testInvalidEmailEntity()
    {
        $this->assertHasErrors($this->getEntity()->setEmail('blablaa'), 1);
    }





/*
    public function testInjectionSQL()
    {
        $this->assertHasErrors($this->getEntity()->setUsername("Nickname' OR 'a' = 'a"), 1);
    }
*/
/*

    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testSearchByName()
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['username' => 'Admin'])
        ;

        $this->assertSame('Admin', $user->getUsername());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
*/

}
