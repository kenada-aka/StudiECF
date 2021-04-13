<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;

use App\Repository\MessageRepository;
use App\Entity\Message;

class MessageController extends AbstractController
{

    private $messageRepo; 
    private $em;

    public function __construct(EntityManagerInterface $em, MessageRepository $messageRepository)
    {
        $this->messageRepo = $messageRepository;
        $this->em = $em;
    }

    /**
     * @Route("/message/", name="message.home")
     */

    public function home(): Response
    {
        return $this->render('message/message.home.html.twig', [
            'title' => 'Message / Home',
            'realties' => $this->messageRepo->findAll()
        ]);
    }
}
