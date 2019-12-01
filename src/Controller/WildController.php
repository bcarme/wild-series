<?php
// src/Controller/WildController.php
namespace App\Controller;
use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Actor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ProgramSearchType;
use Symfony\Component\HttpFoundation\Request;

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
            ->findAll();

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
            ->findBy(['program_id' => $program,
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



    /**
     * @Route("/wild/show/{slug}/saison{season}/",
     *      methods={"GET"},
     *      name="wild_season")
     * @return Response
     */
    public function showBySeason(string $slug, int $season):Response
    {
        if (!$season) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a season in program\'s table.');
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
            ->findBy(['program_id' => $program,
            ]);
        $episodes = $this->getDoctrine()
            ->getRepository(Episode::class)
            ->findBy(
                ['season_id'=>$season]
            );

        return $this->render('wild/season.html.twig', [
            'program' => $program,
            'slug' => $slug,
            'seasons' => $seasons,
            'episodes' => $episodes,
        ]);
    }

    /**
     * @Route("/wild/show/{slug}/saison{season}/episode{episode}",
     *      methods={"GET"},
     *      name="wild_episode")
     * @return Response
     */
    public function showEpisode(Episode $episode): Response
    {

        $season = $episode->getSeasonId();
        $program = $season->getProgramId();

        return $this->render('wild/episode.html.twig', [
                'program' => $program,
                'season' => $season,
                'episode' => $episode
            ]
        );

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

}

