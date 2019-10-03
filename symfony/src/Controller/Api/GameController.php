<?php

namespace App\Controller\Api;

use App\Entity\Game;
use App\Entity\GameBuffer;
use App\Form\GameBufferType;
use App\Manager\GameFinderManager;
use App\Repository\GameBufferRepository;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GameController
 *
 * @Route("/game")
 */
class GameController extends BaseController
{
    /** @var GameFinderManager */
    private $gameFinderManager;
    /** @var GameRepository */
    private $gameRepository;
    /** @var GameBufferRepository */
    private $gameBufferRepository;
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        GameFinderManager $gameFinderManager,
        GameRepository $gameRepository,
        GameBufferRepository $gameBufferRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->gameFinderManager = $gameFinderManager;
        $this->gameRepository = $gameRepository;
        $this->gameBufferRepository = $gameBufferRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route(name="add_game", methods={"POST"})
     */
    public function add(Request $request): Response
    {
        $gameBuffer = new GameBuffer();
        $form = $this->createForm(GameBufferType::class, $gameBuffer);
        $form->submit(json_decode($request->getContent(), true));

        $gameBuffer->initDataHash();

        if (false === $form->isValid()) {
            return $this->returnFormErrors($form);
        }

        if ($this->gameBufferRepository->findOneBy(['data_hash' => $gameBuffer->getDataHash()])) {
            return $this->returnCreatedItem();
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($gameBuffer);
        $entityManager->flush();

        $game = $this->gameFinderManager->findSimilarGame($gameBuffer);
        if (!$game) {
            $game = new Game();
        } else {
            $game->setMergeCount($game->getMergeCount() + 1);
        }

        $game->setLang($gameBuffer->getLang());
        $game->setLeague($gameBuffer->getLeague());
        $game->setSource($gameBuffer->getSource());
        $game->setStartedAt($gameBuffer->getStartedAt());
        $game->setTeam1Name($gameBuffer->getTeam1Name());
        $game->setTeam2Name($gameBuffer->getTeam2Name());
        $game->setType($gameBuffer->getType());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($game);
        $entityManager->flush();

        return $this->returnCreatedItem();
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/random", name="random_game", methods={"GET"})
     */
    public function random(Request $request)
    {
        $source = $request->get('source');
        $startedFrom = $request->get('startedFrom') ? date('Y-m-d H:i:s', strtotime($request->get('startedFrom'))) : null;
        $startedTo = $request->get('startedTo') ? date('Y-m-d H:i:s', strtotime($request->get('startedTo'))) : null;

        $queryBuilderMax = $this->entityManager->createQueryBuilder()->from(
            Game::class, 'g'
        );
        if ($source) {
            $queryBuilderMax->andWhere('g.source = :source')->setParameter('source', $source);
        }
        if ($startedFrom && $startedTo) {
            $queryBuilderMax->andWhere('g.started_at BETWEEN :startFrom TO :startTo')
                ->setParameter('startFrom', $startedFrom)
                ->setParameter('startTo', $startedFrom);
        } elseif ($startedFrom) {
            $queryBuilderMax->andWhere('g.started_at > :startFrom')
                ->setParameter('startFrom', $startedFrom);
        } elseif ($startedTo) {
            $queryBuilderMax->andWhere('g.started_at < :startTo')
                ->setParameter('startTo', $startedTo);
        }

        $queryBuilderMin = clone $queryBuilderMax;
        $min = $queryBuilderMin->select('MIN(g.id)')->getQuery()->getSingleScalarResult();
        if (!$min) {
            return $this->returnItem(null);
        }
        $max = $queryBuilderMax->select('MAX(g.id)')->getQuery()->getSingleScalarResult();

        if ($min && $max) {
            $randId = rand($min, $max);
        } elseif ($max) {
            $randId = $max;
        } else {
            $randId = $min;
        }

        if (!$randId) {
            return $this->returnItem(null);
        }

        $game = $this->gameRepository->find($randId);

        return $this->returnItem($game);
    }
}
