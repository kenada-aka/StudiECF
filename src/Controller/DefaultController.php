<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;                         // Permet d'utiliser les routes sans "config/routes.yaml"


class DefaultController extends AbstractController

{

    private $repository; 
    private $em;
/*
    public function __construct(UserRepository $userRepository, EntityManagerInterface $em) {
        $this->repository = $userRepository;
        $this->em = $em;
    }
*/
    /**

     * @Route("/", name="home")

     */
    public function home()

    {

        //UserRepository $userRepository
        

        return $this->render('index.html.twig', [
                        'title' => 'Ma page de contact ',
                        //'test' => $userRepository->findAll()
                        
                    ]);

    }

    

}