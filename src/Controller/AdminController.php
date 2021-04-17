<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;

use App\Repository\UserRepository;
use App\Entity\User;

use App\Repository\RealtyRepository;
use App\Entity\Realty;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AdminController extends AbstractController
{

    private $userRepo;
    private $realtyRepo;
    private $em;

    public function __construct(UserRepository $userRepository, RealtyRepository $realtyRepository, EntityManagerInterface $em)
    {
        $this->userRepo = $userRepository;
        $this->realtyRepo = $realtyRepository;
        $this->em = $em;
    }

    /**
     * @Route("/admin/user", name="admin.user.home")
     * @IsGranted("ROLE_ADMIN")
     */
    public function adminUserHome(): Response
    {
        $users = $this->userRepo->findAll();
        return $this->render('admin/user.html.twig', [
            'title' => 'Gestion des utilisateurs',
            'subtitle' => 'CRUD ADMIN : User',
            'users' => $users
        ]);
    }
    
    /**
     * @Route("/admin/user/remove/{idUser}", name="admin.user.remove", methods="DELETE")
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(int $idUser, Request $request)
    {
        $user = $this->userRepo->find($idUser);

        if($this->isCsrfTokenValid('delete'. $user->getId(), $request->get('_token')))
        {
            $this->em->remove($user);
            $this->em->flush();
        }
        
        return $this->redirectToRoute('admin.user.home');
    }

}
