<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\MessageRepository;
use App\Entity\Message;
use App\Form\MessageType;

use App\Repository\UserRepository;
use App\Entity\User;

class MessageController extends AbstractController
{

    private $messageRepo;
    private $userRepo;
    private $em;

    public function __construct(EntityManagerInterface $em, MessageRepository $messageRepository, UserRepository $userRepository)
    {
        $this->messageRepo = $messageRepository;
        $this->userRepo = $userRepository;
        $this->em = $em;
    }

    /**
     * @Route("/message/", name="message.home")
     */

    public function home(): Response
    {
        return $this->render('message/message.home.html.twig', [
            'title' => 'Message / Home',
            'messages' => $this->messageRepo->findAll()
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
