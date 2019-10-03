<?php


namespace App\Manager;


use App\Entity\Game;
use App\Entity\GameBuffer;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Internal\Hydration\IterableResult;

class GameFinderManager
{
    const MIN_PASS_PERCENTAGE = 45;

    /** @var GameRepository */
    private $repository;
    /** @var EntityManager */
    private $entityManager;
    /** @var GameBuffer */
    private $gameBuffer;

    /**
     * GameFinderManager constructor.
     *
     * @param GameBuffer $gameBuffer
     *
     * algo:
     *
     * find all games by date range
     * compare founded games with this one
     */
    public function __construct(GameRepository $repository, EntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    public function findSimilarGame(GameBuffer $gameBuffer): ?Game
    {
        $this->gameBuffer = $gameBuffer;

        $startedAt = $gameBuffer->getStartedAt()->getTimestamp();
        $timeDelta = 26 * 3600;
        $dateFrom = date('Y-m-d H:i:s', $startedAt - $timeDelta);
        $dateTo = date('Y-m-d H:i:s', $startedAt + $timeDelta);

        $gamesIterator = $this->findGameBuffers(
            $dateFrom,
            $dateTo
        );

        while (($rows = $gamesIterator->next()) !== false) {
            /** @var Game $game */
            $game = reset($rows);

            if ($this->compareGames($game, $gameBuffer)) {
                return $game;
            }

            $this->entityManager->detach($game);
        }

        return null;
    }

    private function compareGames(Game $game, GameBuffer $compareGame)
    {
        $properties = [
            'type',
            'league',
            'team1_name',
            'team2_name',
        ];

        foreach ($properties as $property) {

            if (!$this->checkProperty($game, $compareGame, $property)) {
                return false;
            }
        }

        return true;
    }

    private function checkProperty(Game $game, GameBuffer $gameBuffer, string $property)
    {
        $method = $this->getMethodName($property);
        $value1 = $this->translit($game->$method());
        $value2 = $this->translit($gameBuffer->$method());

        similar_text($value1, $value2, $percents);

        return $percents >= static::MIN_PASS_PERCENTAGE;
    }

    private function getMethodName(string $property): string
    {
        $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $property)));
        return 'get' . $str;
    }

    /**
     * @param string $dateFrom
     * @param string $dateTo
     * @return IterableResult
     */
    private function findGameBuffers(string $dateFrom, string $dateTo): IterableResult
    {
        $query = $this->repository->createQueryBuilder('t')
            ->andWhere('t.started_at BETWEEN :startedFrom AND :startedTo')
            ->setParameter('startedFrom', $dateFrom)
            ->setParameter('startedTo', $dateTo)
            ->getQuery();

        return $query->iterate();
    }

    private function translit($str)
    {
        $rus = [
            'А',
            'Б',
            'В',
            'Г',
            'Д',
            'Е',
            'Ё',
            'Ж',
            'З',
            'И',
            'Й',
            'К',
            'Л',
            'М',
            'Н',
            'О',
            'П',
            'Р',
            'С',
            'Т',
            'У',
            'Ф',
            'Х',
            'Ц',
            'Ч',
            'Ш',
            'Щ',
            'Ъ',
            'Ы',
            'Ь',
            'Э',
            'Ю',
            'Я',
            'а',
            'б',
            'в',
            'г',
            'д',
            'е',
            'ё',
            'ж',
            'з',
            'и',
            'й',
            'к',
            'л',
            'м',
            'н',
            'о',
            'п',
            'р',
            'с',
            'т',
            'у',
            'ф',
            'х',
            'ц',
            'ч',
            'ш',
            'щ',
            'ъ',
            'ы',
            'ь',
            'э',
            'ю',
            'я'
        ];
        $lat = [
            'A',
            'B',
            'V',
            'G',
            'D',
            'E',
            'E',
            'Gh',
            'Z',
            'I',
            'Y',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'R',
            'S',
            'T',
            'U',
            'F',
            'H',
            'C',
            'Ch',
            'Sh',
            'Sch',
            'Y',
            'Y',
            'Y',
            'E',
            'Yu',
            'Ya',
            'a',
            'b',
            'v',
            'g',
            'd',
            'e',
            'e',
            'gh',
            'z',
            'i',
            'y',
            'k',
            'l',
            'm',
            'n',
            'o',
            'p',
            'r',
            's',
            't',
            'u',
            'f',
            'h',
            'c',
            'ch',
            'sh',
            'sch',
            'y',
            'y',
            'y',
            'e',
            'yu',
            'ya'
        ];
        return str_replace($rus, $lat, $str);
    }
}
