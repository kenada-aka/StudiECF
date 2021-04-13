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

        $user = $this->userRepo->find(1);
        //$user = new User();
        //$this->em->persist($user);

        $realty->setIdTenant($user);

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

    /**
     * @Route("/owner/uploadImage", name="owner.upload.image")
     */

    public function uploadImage(Request $request, SluggerInterface $slugger): Response
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


                $reality = $this->realtyRepo->find(1);
                $image->setIdRealty($reality);


                $this->em->persist($image);
                $this->em->flush();
            }

            return $this->redirectToRoute('owner.home');
       
        }

        return $this->render('owner/owner.upload.image.html.twig', [
            'title' => 'Owner / Upload Image',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/owner/uploadDocument", name="owner.upload.document")
     */

    public function uploadDocument(Request $request, SluggerInterface $slugger): Response
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


                $reality = $this->realtyRepo->find(1);
                $document->setIdRealty($reality);


                $this->em->persist($document);
                $this->em->flush();
            }

            return $this->redirectToRoute('owner.home');
       
        }

        return $this->render('owner/owner.upload.document.html.twig', [
            'title' => 'Owner / Upload Document',
            'form' => $form->createView(),
        ]);
    }
}
