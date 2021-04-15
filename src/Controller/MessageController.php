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
     * @Route("/message/", name="message.home")
     * @IsGranted("ROLE_ADMIN")
     */

    public function home(): Response
    {
        return $this->render('message/message.home.html.twig', [
            'title' => 'Message / Home',
            'messages' => $this->messageRepo->findAll()
        ]);
    }

    /**
     * @Route("/message/tenant/{idOwner}", name="tenant.contact")
     * @IsGranted("ROLE_LOCATAIRE")
     */
    public function tenantContact(int $idOwner, Request $request)
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

        $message->setType(2); // 2 = message entre propriétaire et locataire
        $message->setDate(new \DateTime());

        $form = $this->createForm(MessageType::class,  $message);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $this->em->persist($message);
            $this->em->flush();

            
        }

        // Historique des messages



        return $this->render('message/message.home.html.twig', [
            'title' => 'Message',
            'subtitle' => 'A partir de cette page, vous allez pouvoir échanger entre locataire et propriétaire.',
            'form' => $form->createView(),
            'messages' => $this->messageRepo->findAllByOwner($idOwner)
        ]);

    }

    /**
     * @Route("/message/owner/{idOwner}", name="owner.contact")
     * @IsGranted("ROLE_PROPRIETAIRE")
     */
    public function ownerContact(int $idOwner, Request $request)
    {
        // Tenant 
        $user = $this->getUser();
        // Message

        $message = new Message();

        // Sender

        $message->setIdSender($user);

        // Receiver

        $realty = $this->realtyRepo->find($idOwner);

        $receiver = $realty->getIdTenant();
        $message->setIdReceiver($receiver);

        $message->setType(2); // 2 = message entre propriétaire et locataire
        $message->setDate(new \DateTime());

        $form = $this->createForm(MessageType::class,  $message);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $this->em->persist($message);
            $this->em->flush();

            
        }

        // Historique des messages



        return $this->render('message/message.home.html.twig', [
            'title' => 'Message',
            'subtitle' => 'A partir de cette page, vous allez pouvoir échanger entre locataire et propriétaire.',
            'form' => $form->createView(),
            'messages' => $this->messageRepo->findAllByOwner($idOwner)
        ]);

    }

    /**
     * @Route("/message/agencyTOowner/{idOwner}", name="agency.contact.owner")
     * @IsGranted("ROLE_AGENCE")
     */
    public function agencyContact(int $idOwner, Request $request)
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

        $message->setType(2); // 2 = message entre propriétaire et locataire
        $message->setDate(new \DateTime());

        $form = $this->createForm(MessageType::class,  $message);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $this->em->persist($message);
            $this->em->flush();

            
        }

        // Historique des messages



        return $this->render('message/message.home.html.twig', [
            'title' => 'Message',
            'subtitle' => 'A partir de cette page, vous allez pouvoir échanger entre locataire et propriétaire.',
            'form' => $form->createView(),
            'messages' => $this->messageRepo->findAllByOwner($idOwner)
        ]);

    }

    /**
     * @Route("/message/ownerTOagency/{idOwner}", name="owner.contact.agency")
     * @IsGranted("ROLE_PROPRIETAIRE")
     */
    public function ownerTOagency(int $idOwner, Request $request)
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

        $message->setType(2); // 2 = message entre propriétaire et locataire
        $message->setDate(new \DateTime());

        $form = $this->createForm(MessageType::class,  $message);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $this->em->persist($message);
            $this->em->flush();

            
        }

        // Historique des messages



        return $this->render('message/message.home.html.twig', [
            'title' => 'Message',
            'subtitle' => 'A partir de cette page, vous allez pouvoir échanger entre locataire et propriétaire.',
            'form' => $form->createView(),
            'messages' => $this->messageRepo->findAllByOwner($idOwner)
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
