<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;                         // Permet d'utiliser les routes sans "config/routes.yaml"

use Doctrine\ORM\EntityManagerInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Repository\RealtyRepository;


class ExtendsController extends AbstractController
{

    private $realtyRepo;
    private $em;

    public function __construct(EntityManagerInterface $em, RealtyRepository $realtyRepository)
    {
        $this->realtyRepo = $realtyRepository;
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

            return $this->redirectToRoute('member.home');
       
        }

        return $this->render('extends/upload.image.html.twig', [
            'title' => 'Ajouter une photo',
            'subtitle' => 'A partir de cette page, vous pouvez ajouter une photo pour votre annonce, les formats acceptés sont JPG et PNG.',
            'form' => $form->createView(),
        ]);
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

            return $this->redirectToRoute('member.home');
       
        }

        return $this->render('extends/upload.document.html.twig', [
            'title' => 'Ajouter un document',
            'subtitle' => 'A partir de cette page, vous pouvez ajouter un document pour votre annonce (exemple : bail, quittances, assurance, ...), le format accepté est PDF.',
            'form' => $form->createView(),
        ]);
    }

    

}