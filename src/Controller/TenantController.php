<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;                         // Permet d'utiliser les routes sans "config/routes.yaml"

use Doctrine\ORM\EntityManagerInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Repository\RealtyRepository;


class TenantController extends AbstractController
{

    private $realtyRepo;
    private $em;

    public function __construct(EntityManagerInterface $em, RealtyRepository $realtyRepository)
    {
        $this->realtyRepo = $realtyRepository;
        $this->em = $em;
    }

    /**
     * @Route("/member/home", name="member.home")
     * @IsGranted("ROLE_LOCATAIRE")
     */
    public function home()
    {
        // A près authentification tous les utilisateurs arrivent ici !
        $user = $this->getUser();

        if($user)
        {
            //dump($user);
        }

        return $this->render('owner/owner.public.html.twig', [
            'title' => 'Bonjour',
            'subtitle' => 'Bienvenu dans votre espace sécurisé, vous pouvez consulter les annonces des biens disponibles et contacter les propriétaires pour conculure la location.',
            'realties' => $this->realtyRepo->findAllFreeRent()
        ]);

    }

    /**
     * @Route("/member/rent", name="member.rent")
     * @IsGranted("ROLE_LOCATAIRE")
     */
    public function rent()
    {

        $user = $this->getUser();

        return $this->render('admin/tenant.rent.html.twig', [
            'title' => 'Ma location',
            'subtitle' => 'A partir de cette page vous allez pouvoir gérer votre location avec votre propriétaire.',
            'realties' => $this->realtyRepo->findByTenant($user->getId())
        ]);

    }

}