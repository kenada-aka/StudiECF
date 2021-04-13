<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;                         // Permet d'utiliser les routes sans "config/routes.yaml"

use Doctrine\ORM\EntityManagerInterface;

use App\Repository\UserRepository;
use App\Entity\User;
use App\Form\UserType;

use App\Repository\AdminRepository;
use App\Entity\Admin;
use App\Form\AdminType;

class DefaultController extends AbstractController
{

    private $repository; 
    private $repoTest;
    private $em;

    public function __construct(UserRepository $userRepository, AdminRepository $adminRepository, EntityManagerInterface $em) {
        $this->repository = $userRepository;
        $this->repoTest = $adminRepository;
        $this->em = $em;
    }

    /**
     * @Route("/", name="home")
     */

    public function home()
    {

        //dump($this->repoTest->findAll());

        return $this->render('home/home.html.twig', [
            'title' => 'CRUD TEST'
        ]);

    }

    /**
     * @Route("/register", name="register")
     */

    public function register()
    {
        return $this->render('home/register.html.twig', [
            'title' => 'CRUD TEST'
        ]);

    }

}