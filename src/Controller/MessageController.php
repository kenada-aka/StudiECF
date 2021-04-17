<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\MessageRepository;
use App\Entity\Message;
use App\Form\MessageType;

use App\Repository\RealtyRepository;
use App\Entity\Realty;


class MessageController extends AbstractController
{

    private $messageRepo;
    private $realtyRepo;
    private $em;

    public function __construct(EntityManagerInterface $em, MessageRepository $messageRepository, RealtyRepository $realtyRepository)
    {
        $this->messageRepo = $messageRepository;
        $this->realtyRepo = $realtyRepository;
        $this->em = $em;
    }

    /**
     * @Route("/message/tenant-owner/contact/{idOwner}", name="tenant.contact.owner")
     * @IsGranted("ROLE_LOCATAIRE")
     */
    public function tenantContactOwner(int $idOwner, Request $request)
    {
        return $this->tenantToOwner($idOwner, $request, 2, "A partir de cette page, vous allez pouvoir échanger entre locataire et propriétaire.");
    }

    /**
     * @Route("/message/tenant/problem/{idOwner}", name="tenant.problem.owner")
     * @IsGranted("ROLE_LOCATAIRE")
     */
    public function tenantProblemOwner(int $idOwner, Request $request)
    {
        return $this->tenantToOwner($idOwner, $request, 3, "A partir de cette page, vous allez pouvoir prévenir le propriétaire d'un problème concernant votre location.");
    }

    /**
     * @Route("/message/tenant/work/{idOwner}", name="tenant.work.owner")
     * @IsGranted("ROLE_LOCATAIRE")
     */
    public function tenantWorkOwner(int $idOwner, Request $request)
    {
        return $this->tenantToOwner($idOwner, $request, 4, "A partir de cette page, vous allez pouvoir demander à votre propriétaire de réaliser des travaux.");
    }

    private function tenantToOwner(int $idOwner, Request $request, int $type, string $subtitle)
    {
        // Tenant 
        $user = $this->getUser();
        
        // Message

        $message = new Message();

        // Sender

        $message->setIdSender($user);

        // Receiver

        $realty = $this->realtyRepo->findByTenant($user->getId());

        $receiver = $realty[0]->getIdOwner();
        $message->setIdReceiver($receiver);

        // Propriété

        $message->setIdOwner($realty[0]);

        $message->setType($type);
        $message->setDate(new \DateTime());

        $form = $this->createForm(MessageType::class,  $message);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($message);
            $this->em->flush();
            
            return $this->redirectToRoute("member.tenant");
        }

        return $this->render('member/message.html.twig', [
            'title' => 'Message',
            'subtitle' => $subtitle,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/message/owner-tenant/contact/{idOwner}", name="owner.contact.tenant")
     * @IsGranted("ROLE_PROPRIETAIRE")
     */
    public function ownerContactTenant(int $idOwner, Request $request)
    {
        return $this->ownerToTenant($idOwner, $request, 2, "A partir de cette page, vous allez pouvoir échanger entre propriétaire et locataire.");
    }

    private function ownerToTenant(int $idOwner, Request $request, int $type, string $subtitle)
    {
        // Owner 
        $user = $this->getUser();
        // Message

        $message = new Message();

        // Sender

        $message->setIdSender($user);

        // Receiver

        $realty = $this->realtyRepo->find($idOwner);

        $receiver = $realty->getIdTenant();
        $message->setIdReceiver($receiver);

        // Propriété

        $message->setIdOwner($realty);

        $message->setType($type);
        $message->setDate(new \DateTime());

        $form = $this->createForm(MessageType::class,  $message);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $this->em->persist($message);
            $this->em->flush();

            return $this->redirectToRoute("member.owner");
        }

        return $this->render('member/message.html.twig', [
            'title' => 'Message',
            'subtitle' => $subtitle,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/message/owner-agency/contact/{idOwner}", name="owner.contact.agency")
     * @IsGranted("ROLE_PROPRIETAIRE")
     */
    public function ownerContactAgency(int $idOwner, Request $request)
    {
        return $this->ownerToAgency($idOwner, $request, 5, "A partir de cette page, vous allez pouvoir échanger entre propriétaire et agence.");
    }

    public function ownerToAgency(int $idOwner, Request $request, int $type, string $subtitle)
    {
        // Propriétaire
        $user = $this->getUser();
        // Message

        $message = new Message();

        // Sender

        $message->setIdSender($user);

        // Receiver
        // Si receiver est null c'est forcement un message adressé à l'agence

        $realty = $this->realtyRepo->find($idOwner);

        

        $message->setIdOwner($realty);

        $message->setType($type);
        $message->setDate(new \DateTime());

        $form = $this->createForm(MessageType::class,  $message);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $this->em->persist($message);
            $this->em->flush();

            return $this->redirectToRoute("member.owner");
        }

        return $this->render('member/message.html.twig', [
            'title' => 'Message',
            'subtitle' => $subtitle,
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/message/agency-owner/contact/{idOwner}", name="agency.contact.owner")
     * @IsGranted("ROLE_AGENCE")
     */
    public function agencyContactOwner(int $idOwner, Request $request)
    {
        return $this->agencyToOwner($idOwner, $request, 5, "A partir de cette page, vous allez pouvoir échanger entre agence et propriétaire.");
    }

    private function agencyToOwner(int $idOwner, Request $request, int $type, string $subtitle)
    {
        // Agency 
        $user = $this->getUser();
        // Message

        $message = new Message();

        // Sender

        $message->setIdSender($user);

        // Receiver

        $realty = $this->realtyRepo->find($idOwner);

        $receiver = $realty->getIdOwner();
        $message->setIdReceiver($receiver);

        $message->setIdOwner($realty);

        $message->setType($type);
        $message->setDate(new \DateTime());

        $form = $this->createForm(MessageType::class,  $message);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $this->em->persist($message);
            $this->em->flush();

            return $this->redirectToRoute("member.owner");
        }

        return $this->render('member/message.html.twig', [
            'title' => 'Message',
            'subtitle' => $subtitle,
            'form' => $form->createView()
        ]);
    }
    
    





















    /**
     * @Route("/message/send/{idReceiver}", name="message.send", methods="GET|POST")
     */

    public function post(int $idReceiver, Request $request)
    {
        // Message

        $message = new Message();

        // Sender

        $sender = $this->userRepo->find(1);
        $message->setIdSender($sender);

        // Receiver

        $receiver = $this->userRepo->find($idReceiver);
        $message->setIdReceiver($receiver);


//date ???
// type
        $message->setType(2);
        $message->setDate(new \DateTime());

        $form = $this->createForm(MessageType::class,  $message);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $this->em->persist($message);
            $this->em->flush();

            return $this->redirectToRoute('message.home');
        }

        return $this->render('message/message.send.html.twig', [
            'title' => 'Message / send',
            'form' => $form->createView()
        ]);

    }
}
