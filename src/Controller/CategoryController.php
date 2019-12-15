<?php

namespace App\Controller;

use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category")
     * @IsGranted("ROLE_ADMIN")
     */
    public function addCategory(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $category = $form ->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_index');
        }

        return $this->render('category/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
