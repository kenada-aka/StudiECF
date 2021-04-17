<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Doctrine\ORM\EntityManagerInterface;

use App\Repository\RealtyRepository;

use App\Repository\MessageRepository;

use App\Entity\User;
use App\Form\UserType;

use App\Entity\Image;
use App\Form\ImageType;

use App\Entity\Document;
use App\Form\DocumentType;

class SecurityController extends AbstractController
{

    private $realtyRepo;
    private $messageRepository;
    private $em;

    public function __construct(EntityManagerInterface $em, RealtyRepository $realtyRepository, MessageRepository $messageRepository)
    {
        $this->realtyRepo = $realtyRepository;
        $this->messageRepo = $messageRepository;
        $this->em = $em;
    }

    /**
     * @Route("/member/register", name="member.register", methods="GET|POST")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            // Encode the password

            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());

            $user->setPassword($password);
            $user->setRegister(new \DateTime());
            $user->setSubscribe(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('member/register.html.twig', [
            'title' => 'Inscription',
            'subtitle' => 'Merci pour votre confiance, vous pouvez profiter de nos servies en vous inscrivant via le formulaire d\'inscription',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="app_login", methods="GET|POST")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->redirectToRoute('member.home');
    }

    /**
     * @Route("/logout", name="app_logout")
     * @IsGranted("ROLE_LOCATAIRE")
     */
    public function logout()
    {
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/member/home", name="member.home")
     * @IsGranted("ROLE_LOCATAIRE")
     */
    public function home()
    {
        // A près authentification tous les utilisateurs arrivent ici !
        $user = $this->getUser();

        if($this->isGranted('ROLE_BAILLEUR_TIERS') && !$this->isGranted('ROLE_AGENCE'))
        {
            // Vérifier si l'abonnement est valide (1mois)
            $diff = date_diff(new \DateTime(), $user->getSubscribe());
            
            if($diff->m < 1)
            {
                return $this->render('member/subscribe.html.twig', [
                    'title' => 'Bonjour',
                    'subtitle' => 'Pour pouvoir utiliser nos services, veuillez régler votre abonnement, merci.',
                    'disablemenu' => 1
                ]);
            }
        }

        if($this->isGranted('ROLE_PROPRIETAIRE')) $redirect = "member.owner";
        else if($this->isGranted('ROLE_LOCATAIRE')) $redirect = "member.tenant";

        return $this->redirectToRoute($redirect);
        
        
        
        /*
        return $this->render('owner/owner.public.html.twig', [
            'title' => 'Bonjour',
            'subtitle' => 'Bienvenu dans votre espace sécurisé, vous pouvez consulter les annonces des biens disponibles et contacter les propriétaires pour conculure la location.',
            'realties' => $this->realtyRepo->findAllFreeRentWithPagination(1, 50, "ASC")
        ]);
        */
    }

    /**
     * @Route("/member/subscribe", name="member.subscribe")
     * @IsGranted("ROLE_LOCATAIRE")
     */
    public function subscribe(Request $request)
    {
        $user = $this->getUser();
        
        if($this->isGranted('ROLE_BAILLEUR_TIERS') && !$this->isGranted('ROLE_AGENCE') )
        {
            
            if($request->isMethod('post'))
            {
                $mode = $request->get('mode');
                if($mode == "CB")
                {
                    
                    $datetime = new \DateTime();
                    $datetime->add(new \DateInterval('P31D')); // + 31 Days : http://en.wikipedia.org/wiki/Iso8601#Durations
                    
                    $user->setSubscribe($datetime);
                    
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($user);
                    $entityManager->flush();
                    
                }
            }
        }

        return $this->redirectToRoute('member.home');
    }

    /**
     * @Route("/member/tenant", name="member.tenant")
     * @IsGranted("ROLE_LOCATAIRE")
     */
    public function rentalTenant()
    {
        $user = $this->getUser();

        // Si le locataire n'est pas entrain de louer un bien

        $realties = $this->realtyRepo->findByTenant($user->getId());

        if(!$realties)
        {
            return $this->redirectToRoute("rental.default");
        }

        // Récupérer les messages entre le locataire et le propriétaire ou l'agence

        $messages[$realties[0]->getId()] = $this->messageRepo->findAllByOwner($realties[0]->getId(), 2); // 2 = contact

        // Récupérer les messages concernant les problèmes de la location

        $problems[$realties[0]->getId()] = $this->messageRepo->findAllByOwner($realties[0]->getId(), 3); // 3 = problèmes

        // Récupérer les messages concernant les demandes de travaux

        $works[$realties[0]->getId()] = $this->messageRepo->findAllByOwner($realties[0]->getId(), 4); // 4 = travaux

        return $this->render('member/home.html.twig', [
            'title' => 'Ma location',
            'subtitle' => 'A partir de cette page vous allez pouvoir gérer votre location avec votre propriétaire ou votre agence.',
            'realties' => $realties,
            'messages' => $messages,
            'problems' => $problems,
            'works' => $works
        ]);
    }

    /**
     * @Route("/member/owner", name="member.owner")
     * @IsGranted("ROLE_PROPRIETAIRE")
     */
    public function securityOwner()
    {
        $user = $this->getUser();
        
        // Si pas d'annonce : redirection formulaire add

        if($this->isGranted('ROLE_ADMIN')) $realties = $this->realtyRepo->findAll();
        else if($this->isGranted('ROLE_AGENCE')) $realties = $this->realtyRepo->findAllWhereAgencyId($user->getId());
        else $realties = $this->realtyRepo->findAllWhereOwnerId($user->getId());

        if(!$realties)
        {
            return $this->redirectToRoute("rental.add");
        }


        // Formulaire Photo

        $image = new Image();

        $formImg = $this->createForm(ImageType::class, $image);

        // Formulaire Document

        $document = new Document();

        $formPdf = $this->createForm(DocumentType::class, $document);


        foreach($realties as $realty)
        {
            // Récupérer les messages entre le locataire et le propriétaire ou l'agence

            $messages[$realty->getId()] = $this->messageRepo->findAllByOwner($realty->getId(), 2); // 2 = contact

            // Récupérer les messages concernant les problèmes de la location

            $problems[$realty->getId()] = $this->messageRepo->findAllByOwner($realty->getId(), 3); // 3 = problèmes

            // Récupérer les messages concernant les demandes de travaux

            $works[$realty->getId()] = $this->messageRepo->findAllByOwner($realty->getId(), 4); // 4 = travaux

            // Récupérer les messages entre le propriétaire et l'agence

            $messagesAgence[$realty->getId()] = $this->messageRepo->findAllByOwner($realty->getId(), 5); // 5 = agence
        }

        return $this->render('member/home.html.twig', [
            'title' => 'Bonjour',
            'subtitle' => 'A partir de votre espace sécurisé vous allez pouvoir gérer votre ou vos biens.',
            'realties' => $realties,
            'formImg' => $formImg->createView(),
            'formPdf' => $formPdf->createView(),
            'messages' => $messages,
            'problems' => $problems,
            'works' => $works,
            'messagesAgence' => $messagesAgence
        ]);
    }

    
}
