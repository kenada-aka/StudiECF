<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;

use App\Repository\AdminRepository;
use App\Entity\Admin;
use App\Form\AdminType;

class AdminController extends AbstractController
{
    private $repository; 
    private $repoTest;
    private $em;

    public function __construct(AdminRepository $adminRepository, EntityManagerInterface $em)
    {
        $this->repository = $adminRepository;
        $this->em = $em;
    }

    /**
     * @Route("/admin/", name="admin.property.home")
     */

    public function home(): Response
    {
        return $this->render('admin/admin.home.html.twig', [
            'title' => 'CRUD TEST',
            'test' => $this->repository->findAll()
        ]);
    }
    
    /**
     * @Route("/admin/addUser", name="admin.property.new")
     */

    public function addUser(Request $request)
    {

        $user = new Admin();

        $form = $this->createForm(AdminType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $this->em = $this->getDoctrine()->getManager();

            $this->em->persist($user);
            $this->em->flush();
            return $this->redirectToRoute('admin.property.home');
        }

        return $this->render('admin/admin.add.html.twig', [

            'title' => 'TEST CRUD (new)',
            'form' => $form->createView()

        ]);

    }

    /**
     * @Route("/admin/posts/{id}", name="admin.property.edit", methods="GET|POST")
     * @param Admin $user
     */

    public function post(Admin $user, int $id, Request $request)
    {

        $form = $this->createForm(AdminType::class,  $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // $this->em->persist($user);
            $this->em->flush();
        }

        return $this->render('admin/admin.edit.html.twig', [
            'title' => 'TEST CRUD (edit)',
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/admin/posts/{id}", name="admin.property.delete", methods="DELETE")
     * @param Admin $user
     */

    public function delete(Admin $user, Request $request)
    {

        if($this->isCsrfTokenValid('delete'. $user->getId(), $request->get('_token')))
        {
            $this->em->remove($user);
            $this->em->flush();
        }
        
        return $this->redirectToRoute('admin.property.home');

    }

}