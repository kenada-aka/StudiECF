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

use App\Entity\Image;
use App\Form\ImageType;

use App\Entity\Document;
use App\Form\DocumentType;

use Symfony\Component\String\Slugger\SluggerInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class OwnerController extends AbstractController
{
    private $realtyRepo;
    private $messageRepo; 
    private $em;

    public function __construct(EntityManagerInterface $em, RealtyRepository $realtyRepository, MessageRepository $messageRepository, userRepository $userRepository)
    {
        $this->userRepo = $userRepository;
        $this->realtyRepo = $realtyRepository;
        $this->messageRepo = $messageRepository;
        $this->em = $em;
    }

    /**
     * @Route("/owners", name="show.owners")
     */
    public function owners()
    {
        return $this->render('owner/owner.public.html.twig', [
            'title' => 'Offres de location',
            'subtitle' => 'Sur cette page vous pouvez voir toutes les offres de location libre de nos biens, ainsi que les biens des propriétaires et des bailleurs tiers.',
            'realties' => $this->realtyRepo->findAllFreeRent()
        ]);
    }

    /**
     * Liste l'ensemble des articles triés par date de publication pour une page donnée.
     *
     * @Route("/owners/public/{page}", requirements={"page" = "\d+"}, name="front_articles_index")
     *
     * @param int $page Le numéro de la page
     *
     * @return array
     */
    public function pagination(int $page, Request $request)
    {
        $nbArticlesParPage = 1;

        $order = "ASC";

        if($request->isMethod('post'))
        {
            $order = $request->get('order'); // TODO : Filtrer les posibilités
        }

        $articles = $this->realtyRepo->findAllWithPagination($page, $nbArticlesParPage, $order);

        $pagination = array(
            'page' => $page,
            'nbPages' => ceil(count($articles) / $nbArticlesParPage),
            'nomRoute' => 'front_articles_index',
            'paramsRoute' => array()
        );

        return $this->render('owner/owner.public.html.twig', [
            'title' => 'Owner / Home',
            'realties' => $articles,
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/owner/", name="owner.home")
     * @IsGranted("ROLE_PROPRIETAIRE")
     */

    public function home(): Response
    {
        $user = $this->getUser();
        if($this->isGranted('ROLE_AGENCE')) $realties = $this->realtyRepo->findAllWhereAgencyId($user->getId());
        else $realties = $this->realtyRepo->findAllWhereOwnerId($user->getId());
        return $this->render('owner/owner.home.html.twig', [
            'title' => 'Visualiser vos annonces de location',
            'subtitle' => 'A partir de cette page vous allez pouvoir visualiser les annonces de vos locations.',
            'realties' => $realties
        ]);
    }

    /**
     * @Route("/owner/extends", name="owner.extends")
     * @IsGranted("ROLE_AGENCE")
     */

    public function homeExtends(): Response
    {
        $user = $this->getUser();
        return $this->render('owner/owner.home.extends.html.twig', [
            'title' => 'Visualiser les annonces des propriétaires',
            'subtitle' => 'A partir de cette page vous allez pouvoir visualiser les annonces des propriétaires.',
            'realties' => $this->realtyRepo->findAllWhereOwnerExtends()
        ]);
    }

    /**
     * @Route("/owner/add", name="owner.add")
     * @IsGranted("ROLE_PROPRIETAIRE")
     */

    public function add(Request $request): Response
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
            return $this->redirectToRoute('owner.home');
            
        }

        return $this->render('owner/owner.add.html.twig', [
            'title' => 'Ajouter une nouvelle location',
            'subtitle' => 'Pour ajouter une nouvelle location il suffit de compléter le formulaire ci-dessous.',
            'form' => $form->createView()
        ]);
    }

    

    /**
     * @Route("/owner/update/{idOwner}", name="owner.update")
     * @IsGranted("ROLE_PROPRIETAIRE")
     */
    public function updateOwner(int $idOwner, Request $request): Response
    {

        $realty = $this->realtyRepo->find($idOwner);
        $form = $this->createForm(RealtyType::class,  $realty);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($realty);
            $this->em->flush();
            return $this->redirectToRoute('owner.home');
        }

        return $this->render('owner/owner.edit.html.twig', [
            'title' => 'Modifier votre annonce',
            'subtitle' => 'A partir de cette page, vous pouvez modifier les informations de votre annonce.',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/owner/remove/{idOwner}", name="owner.delete", methods="DELETE")
     * @IsGranted("ROLE_PROPRIETAIRE")
     */

    public function remove(int $idOwner, Request $request)
    {
        $realty = $this->realtyRepo->find($idOwner);

        if($this->isCsrfTokenValid('delete'. $realty->getId(), $request->get('_token')))
        {
            $this->em->remove($realty);
            $this->em->flush();
        }
        
        return $this->redirectToRoute('owner.home');
    }

    /**
     * @Route("/owner/post/{idOwner}", name="owner.post")
     * @IsGranted("ROLE_PROPRIETAIRE")
     */

    public function post(int $idOwner)
    {
        $realty = $this->realtyRepo->find($idOwner);

        $realty->setStatut(3); // publier

        $this->em->persist($realty);
        $this->em->flush();
        
        return $this->redirectToRoute('owner.home');
    }

    /**
     * @Route("/owner/reserved/{idOwner}", name="owner.reserved")
     * @IsGranted("ROLE_LOCATAIRE")
     */

    public function reserved(int $idOwner)
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
     * @Route("/owner/canceled/{idRent}", name="owner.canceled")
     * @IsGranted("ROLE_PROPRIETAIRE")
     */

    public function canceled(int $idRent)
    {
        $realty = $this->realtyRepo->find($idRent);

        $realty->setStatut(3); // reserver
        $realty->setIdTenant(null);

        $this->em->persist($realty);
        $this->em->flush();
        
        return $this->redirectToRoute('owner.home');
    }

    /**
     * @Route("/owner/accepted/{idRent}", name="owner.accepted")
     * @IsGranted("ROLE_PROPRIETAIRE")
     */

    public function accepted(int $idRent)
    {
        $realty = $this->realtyRepo->find($idRent);

        $realty->setStatut(5); // louer

        $this->em->persist($realty);
        $this->em->flush();
        
        return $this->redirectToRoute('owner.home');
    }

    /**
     * @Route("/owner/agencyOk/{idRent}", name="owner.agency.ok")
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
        
        return $this->redirectToRoute('owner.home');
    }

    

    /**
     * @Route("/owner/uploadImage/{idRent}", name="owner.upload.image")
     * @IsGranted("ROLE_PROPRIETAIRE")
     */

    public function uploadImage(int $idRent, Request $request, SluggerInterface $slugger): Response
    {
        $image = new Image();

        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            
            $pngFile = $form->get('png')->getData();

            if($pngFile) 
            {
                $originalFilename = pathinfo($pngFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pngFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $pngFile->move(
                        $this->getParameter('png_directory'), // config/services.yaml
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $image->setUrl($newFilename);


                $reality = $this->realtyRepo->find($idRent);
                $image->setIdRealty($reality);


                $this->em->persist($image);
                $this->em->flush();
            }

            return $this->redirectToRoute('owner.home');
       
        }

        return $this->render('owner/owner.upload.image.html.twig', [
            'title' => 'Ajouter une photo',
            'subtitle' => 'A partir de cette page, vous pouvez ajouter une photo pour votre annonce, les formats acceptés sont JPG et PNG.',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/owner/uploadDocument/{idRent}", name="owner.upload.document")
     * @IsGranted("ROLE_PROPRIETAIRE")
     */

    public function uploadDocument(int $idRent, Request $request, SluggerInterface $slugger): Response
    {
        $document = new Document();

        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            
            $pdfFile = $form->get('pdf')->getData();

            if($pdfFile) 
            {
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pdfFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $pdfFile->move(
                        $this->getParameter('pdf_directory'), // config/services.yaml
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $document->setUrl($newFilename);


                $reality = $this->realtyRepo->find($idRent);
                $document->setIdRealty($reality);


                $this->em->persist($document);
                $this->em->flush();
            }

            return $this->redirectToRoute('owner.home');
       
        }

        return $this->render('owner/owner.upload.document.html.twig', [
            'title' => 'Ajouter un document',
            'subtitle' => 'A partir de cette page, vous pouvez ajouter un document pour votre annonce (exemple : bail, quittances, assurance, ...), le format accepté est PDF.',
            'form' => $form->createView(),
        ]);
    }
}
