<?php
// src/Controller/DefaultController.php
namespace App\Controller;
use App\Entity\Program;
use App\Form\SearchProgramType;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     * @param Request $request
     * @return Response
     */
    public function index(ProgramRepository $programRepository, Request $request): Response
    {

        $programs = $programRepository->findBy([], ['id' => 'DESC'], 3);

        $form = $this->createForm(SearchProgramType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $search = $data['search'];
           $programs =  $programRepository->searchByName($search);
        }

        return $this->render('home.html.twig',[
            'programs' => $programs,
            'form' => $form->createView(),
        ]);
    }

}
