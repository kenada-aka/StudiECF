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

use App\Repository\UserRepository;
use App\Entity\User;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class RentalController extends AbstractController
{
    private $realtyRepo;
    private $messageRepo; 
    private $em;

    private $nbArticlesParPage = 5;

    public function __construct(EntityManagerInterface $em, RealtyRepository $realtyRepository, MessageRepository $messageRepository, userRepository $userRepository)
    {
        $this->userRepo = $userRepository;
        $this->realtyRepo = $realtyRepository;
        $this->messageRepo = $messageRepository;
        $this->em = $em;
    }

    /**
     * @Route("/rental/", name="rental.default", methods="GET|POST")
     */
    public function rentalDefault(Request $request)
    {
        $user = $this->getUser();

        if($this->isGranted('ROLE_LOCATAIRE'))
        {
            if($this->realtyRepo->findByTenant($user->getId()))
            {
                return $this->redirectToRoute("member.tenant");
            }
        }

        $order = "ASC";

        if($request->getMethod("post"))
        {
            if($request->get("order") == "ASC" || $request->get("order") == "DESC")
            {
                $order = $request->get("order");
            }
        }

        $page = 1;

        $articles = $this->realtyRepo->findAllFreeRentWithPagination($page, $this->nbArticlesParPage, $order);

        $pagination = array(
            'page' => $page,
            'nbPages' => ceil(count($articles) / $this->nbArticlesParPage),
            'nomRoute' => 'front_articles_index',
            'paramsRoute' => array()
        );

        return $this->render('rental/list.default.html.twig', [
            'title' => 'Offres de location',
            'subtitle' => 'Sur cette page vous pouvez voir toutes les offres de location libre de nos biens, ainsi que les biens des propriétaires et des bailleurs tiers.',
            'realties' => $articles,
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/rental/default/{page}", requirements={"page" = "\d+"}, name="front_articles_index")
     */
    public function paginationDefault(int $page, Request $request)
    {
        $order = "ASC";

        if($request->getMethod("post"))
        {
            if($request->get("order") == "ASC" || $request->get("order") == "DESC")
            {
                $order = $request->get("order");
            }
        }
        
        $articles = $this->realtyRepo->findAllFreeRentWithPagination($page, $this->nbArticlesParPage, $order);

        $pagination = array(
            'page' => $page,
            'nbPages' => ceil(count($articles) / $this->nbArticlesParPage),
            'nomRoute' => 'front_articles_index',
            'paramsRoute' => array()
        );

        return $this->render('owner/owner.public.html.twig', [
            'title' => 'Offres de location',
            'subtitle' => 'Sur cette page vous pouvez voir toutes les offres de location libre de nos biens, ainsi que les biens des propriétaires et des bailleurs tiers.',
            'realties' => $articles,
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/rental/extends", name="rental.extends")
     * @IsGranted("ROLE_AGENCE")
     */
    public function homeExtends(): Response
    {
        $user = $this->getUser();
        return $this->render('rental/owner.extends.html.twig', [
            'title' => 'Visualiser les annonces des propriétaires',
            'subtitle' => 'A partir de cette page vous allez pouvoir visualiser les annonces des propriétaires.',
            'realties' => $this->realtyRepo->findAllWhereOwnerExtends()
        ]);
    }

    /**
     * @Route("/rental/add", name="rental.add")
     * @IsGranted("ROLE_PROPRIETAIRE")
     */
    public function rentalAdd(Request $request): Response
    {
        $user = $this->getUser();

        $realty = new Realty();

        $realty->setIdOwner($user); // propriétaire

        if($this->isGranted('ROLE_AGENCE')) $realty->setIdAgency($user);

        if($this->isGranted('ROLE_BAILLEUR_TIERS')) $statut = 2;
        else  $statut = 1;

        $realty->setStatut($statut); // private

        $form = $this->createForm(RealtyType::class, $realty);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($realty);
            $this->em->flush();
            return $this->redirectToRoute('member.owner');
            
        }

        return $this->render('rental/add.html.twig', [
            'title' => 'Ajouter une nouvelle location',
            'subtitle' => 'Pour ajouter une nouvelle location il suffit de compléter le formulaire ci-dessous.',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/rental/update/{idOwner}", name="rental.update")
     * @IsGranted("ROLE_PROPRIETAIRE")
     */
    public function rentalUpdate(int $idOwner, Request $request): Response
    {
        $realty = $this->realtyRepo->find($idOwner);
        $form = $this->createForm(RealtyType::class,  $realty);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($realty);
            $this->em->flush();
            return $this->redirectToRoute('member.owner');
        }

        return $this->render('rental/update.html.twig', [
            'title' => 'Modifier votre annonce',
            'subtitle' => 'A partir de cette page, vous pouvez modifier les informations de votre annonce.',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/rental/remove/{idOwner}", name="rental.remove", methods="DELETE")
     * @IsGranted("ROLE_PROPRIETAIRE")
     */
    public function rentalRemove(int $idOwner, Request $request)
    {
        $realty = $this->realtyRepo->find($idOwner);

        if($this->isCsrfTokenValid('delete'. $realty->getId(), $request->get('_token')))
        {
            // Si locataire pas possible de supprimer
            if(!$realty->getIdTenant())
            {
                $realty->removeAllImages($this->getParameter('png_directory'));
                $realty->removeAllDocuments($this->getParameter('pdf_directory'));
                $this->em->remove($realty);
                $this->em->flush();
            }
        }
        
        return $this->redirectToRoute('member.home');
    }

    /**
     * @Route("/rental/post/{idOwner}", name="rental.post")
     * @IsGranted("ROLE_PROPRIETAIRE")
     */
    public function rentalPost(int $idOwner)
    {
        $realty = $this->realtyRepo->find($idOwner);

        $realty->setStatut(3); // publier

        $this->em->persist($realty);
        $this->em->flush();
        
        return $this->redirectToRoute('member.home');
    }

    /**
     * @Route("/rental/reserved/{idOwner}", name="rental.reserved")
     * @IsGranted("ROLE_LOCATAIRE")
     */
    public function rentalReserved(int $idOwner)
    {
        $user = $this->getUser();

        $realty = $this->realtyRepo->find($idOwner);

        $realty->setStatut(4); // reserver
        $realty->setIdTenant($user);

        $this->em->persist($realty);
        $this->em->flush();
        
        return $this->redirectToRoute('member.home');
    }

    /**
     * @Route("/rental/canceled/{idRent}", name="rental.canceled")
     * @IsGranted("ROLE_PROPRIETAIRE")
     */
    public function rentalCanceled(int $idRent)
    {
        $realty = $this->realtyRepo->find($idRent);

        $realty->setStatut(3); // reserver
        $realty->setIdTenant(null);

        $this->em->persist($realty);
        $this->em->flush();
        
        return $this->redirectToRoute('member.home');
    }

    /**
     * @Route("/rental/accepted/{idRent}", name="rental.accepted")
     * @IsGranted("ROLE_PROPRIETAIRE")
     */
    public function rentalAccepted(int $idRent)
    {
        $realty = $this->realtyRepo->find($idRent);

        $realty->setStatut(5); // louer

        $this->em->persist($realty);
        $this->em->flush();
        
        return $this->redirectToRoute('member.home');
    }

    /**
     * @Route("/rental/agencyOk/{idRent}", name="rental.agency.ok")
     * @IsGranted("ROLE_AGENCE")
     */

    public function agencyOk(int $idRent)
    {
        // L'agence accepte la gestion du bien du propriétaire
        $user = $this->getUser();

        $realty = $this->realtyRepo->find($idRent);

        $realty->setStatut(3); // libre à louer
        $realty->setIdAgency($user);

        $this->em->persist($realty);
        $this->em->flush();
        
        return $this->redirectToRoute('member.home');
    }

}
