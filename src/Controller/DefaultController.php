<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;                         // Permet d'utiliser les routes sans "config/routes.yaml"


class DefaultController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */

    public function home()
    {

        $user = $this->getUser();

        if($user)
        {
            //dump($user);
        }

        return $this->render('home/home.html.twig', [
            'title' => 'CRUD TEST'
        ]);

    }

}