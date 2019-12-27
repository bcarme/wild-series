<?php
// src/Controller/WildController.php
namespace App\Controller;
use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Actor;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Service\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ProgramSearchType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class WildController extends AbstractController
{
    /**
     * @Route("/wild", name="wild_index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $form = $this->createForm(
            ProgramSearchType::class,
            null,
            ['method' => Request::METHOD_GET]
        );


        $programs=$this->getDoctrine()
            ->getRepository(Program::class)
            ->findAllWithCategoriesAndActors();

        if (!$programs){
            throw $this->createNotFoundException('No program found in the table');
        }
        return $this->render('wild/index.html.twig', [
            'website' => 'Wild Séries',
            'programs'=>$programs,
            'form' => $form->createView(),
        ]);


    }

    /**
     * @Route("/wild/show/{slug}",
     *      requirements={"slug"="[a-z0-9\-]+"},
     *      defaults={"slug"="Aucune série sélectionnée, veuillez choisir une série"},
     *      methods={"GET"},
     *      name="wild_show")
     * @return Response
     */
    public function show(string $slug):Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(['program' => $program,
            ]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with ' . $slug . ' title, found in program\'s table.'
            );

        }

        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug' => $slug,
            'seasons' => $seasons
        ]);
    }




    /**
     * @param int $id
     * @return Response
     * @Route("wild/season/{id<^[0-9-]+$>}", defaults={"id" = null}, name="show_season")
     */
    public function showBySeason(int $id) : Response
    {
        if (!$id) {
            throw $this
                ->createNotFoundException('No season has been find in season\'s table.');
        }
        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->find($id);
        $program = $season->getProgram();
        $episodes = $season->getEpisodes();
        if (!$season) {
            throw $this->createNotFoundException(
                'No season with '.$id.' season, found in Season\'s table.'
            );
        }
        return $this->render('wild/season.html.twig', [
            'season'   => $season,
            'program'  => $program,
            'episodes' => $episodes,
        ]);
    }


    /**
     * @Route("wild/episode/{id}",
     *      name="wild_episode")
     */
    public function showEpisode(Episode $episode, Request $request): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $comment->setAuthor($this->getUser());
            $comment->setEpisode($episode);
            $comment->setDate(new \DateTime());
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('episode_show', ['slug' => $episode->getSlug()]);
        }
        $season = $episode->getSeason();
        $program = $season->getProgram();
        $comments = $episode->getComments();

        return $this->render('episode/show.html.twig', [
            'episode' => $episode,
            'season' => $season,
            'program' => $program,
            'comment' => $comment,
            'comments' => $comments,
            'form' => $form->createView(),
        ]);
    }



    /**
     * @param $name
     * @Route("wild/actor/{name}", defaults={"name" = null}, name="show_actor")
     * @return Response
     */
    public function showByActor(Actor $actor): Response
    {
        $program = $actor->getPrograms()->toArray();
        return $this->render(("wild/actor.html.twig"), [
            "actor" => $actor,
            "programs" => $program,
        ]);
    }



    /**
     * @param string $categoryName
     * @Route("wild/category/{categoryName}", defaults={"categoryName" = null}, name="show_category")
     * @return Response
     */
    public function showByCategory(string $categoryName)
    {
        if (!$categoryName) {
            throw $this
                ->createNotFoundException('No category has been sent to find in the table.');
        }
        $categoryName = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($categoryName)), "-")
        );
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => $categoryName]);
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(
                ['category' => $category],
                ['id' => "ASC"],
                3
            );
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '.$categoryName.' category, found in program\'s table.'
            );
        }
        return $this->render('wild/category.html.twig', [
            'programs' => $program,
            'category'  => $categoryName,
        ]);
    }


}

