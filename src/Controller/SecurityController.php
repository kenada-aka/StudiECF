<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\User;
use App\Form\UserType;

class SecurityController extends AbstractController
{

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

        if($this->isGranted('ROLE_ADMIN')) $redirect = "admin.user.home";
        else if($this->isGranted('ROLE_AGENCE')) $redirect = "rental.owner";
        else if($this->isGranted('ROLE_BAILLEUR_TIERS')) $redirect = "rental.owner";
        else if($this->isGranted('ROLE_PROPRIETAIRE')) $redirect = "rental.owner";
        else if($this->isGranted('ROLE_LOCATAIRE')) $redirect = "rental.tenant";

        return $this->redirectToRoute($redirect);
        /*
        return $this->render('member/home.html.twig', [
            'title' => 'Bonjour',
            'subtitle' => 'Bienvenu dans votre espace sécurisé, vous pouvez consulter les annonces des biens disponibles et contacter les propriétaires pour conculure la location.'
        ]);
        */
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

    
}
