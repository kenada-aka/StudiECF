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
     * @Route("/login", name="app_login", methods="GET|POST")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }
        /*
        $user = new User();
        $plainPassword = '123';
        $encoded = $encoder->encodePassword($user, $plainPassword);
        dump($encoded);
    */
/*
    {% if error %}
        <div class="alert alert-danger">{{ error.messageKey|trans(error.messageData, 'security') }}</div>
    {% endif %}
*/
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('home/home.html.twig', [
            'title' => 'CRUD TEST',
            'last_username' => $lastUsername, 'error' => $error
        ]);
    }



    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        //throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
        return $this->render('home/register.html.twig', [
            'title' => 'CRUD TEST'
        ]);
    }



    /**
     * @Route("/register", name="register", methods="GET|POST")
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



        return $this->render('home/register.html.twig', [
            'title' => 'Inscription',
            'subtitle' => 'Merci pour votre confiance, vous pouvez profiter de nos servies en vous inscrivant via le formulaire d\'inscription',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/subscribe", name="member.subscribe")
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
