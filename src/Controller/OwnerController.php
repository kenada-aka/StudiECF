<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;

use App\Repository\MessageRepository;
use App\Entity\Message;

use App\Repository\RealtyRepository;
use App\Entity\Realty;
use App\Form\RealtyType;

class OwnerController extends AbstractController
{
    private $realtyRepo;
    private $messageRepo; 
    private $em;

    public function __construct(EntityManagerInterface $em, RealtyRepository $realtyRepository, MessageRepository $messageRepository)
    {
        $this->realtyRepo = $realtyRepository;
        $this->messageRepo = $messageRepository;
        $this->em = $em;
    }

    /**
     * @Route("/owner/", name="owner.home")
     */

    public function home(): Response
    {
        return $this->render('owner/owner.home.html.twig', [
            'title' => 'Owner / Home',
            'realties' => $this->realtyRepo->findAll()
        ]);
    }

    /**
     * @Route("/owner/add", name="owner.add")
     */

    public function add(Request $request): Response
    {
        $realty = new Realty();

        $form = $this->createForm(RealtyType::class, $realty);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($realty);
            $this->em->flush();
            return $this->redirectToRoute('owner.home');
        }

        return $this->render('owner/owner.add.html.twig', [
            'title' => 'Owner / Add',
            'form' => $form->createView()
        ]);
    }
}
