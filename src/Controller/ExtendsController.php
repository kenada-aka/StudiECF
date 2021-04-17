<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;                         // Permet d'utiliser les routes sans "config/routes.yaml"

use Doctrine\ORM\EntityManagerInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Repository\RealtyRepository;

use Symfony\Component\String\Slugger\SluggerInterface;

use App\Entity\Image;
use App\Form\ImageType;

use App\Entity\Document;
use App\Form\DocumentType;
use App\Repository\DocumentRepository;


class ExtendsController extends AbstractController
{

    private $realtyRepo;
    private $documentRepo;
    private $em;

    public function __construct(EntityManagerInterface $em, RealtyRepository $realtyRepository, DocumentRepository $documentRepository)
    {
        $this->realtyRepo = $realtyRepository;
        $this->documentRepo = $documentRepository;
        $this->em = $em;
    }

    /**
     * @Route("/rental/uploadImage/{idRent}", name="rental.upload.image")
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
        }

        return $this->redirectToRoute('member.home');
    }

    /**
     * @Route("/rental/uploadDocument/{idRent}", name="rental.upload.document")
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

            
       
        }

        return $this->redirectToRoute('member.home');
    }

    /**
     * @Route("/rental/askRemoveDocument/", name="rental.askRemove.document")
     * @IsGranted("ROLE_PROPRIETAIRE")
     */
    public function askRemoveDocument(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $idDocument = $request->request->get('id');

            $document = $this->documentRepo->find($idDocument);

            $document->setAskRemove(true);
            
            $this->em->persist($document);
            $this->em->flush();

            return new Response();
        }
    }

    /**
     * @Route("/admin/showRemoveDocument", name="admin.showRemoveDocument")
     * @IsGranted("ROLE_ADMIN")
     */
    public function showRemoveDocument(): Response
    {
        return $this->render('admin/remove.document.html.twig', [
            'title' => 'Gestion des documents',
            'subtitle' => 'Vous trouverez ici tous les documents en attente de supression.',
            'documents' => $this->documentRepo->find()
        ]);
    }

}