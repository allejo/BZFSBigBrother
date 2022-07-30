<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\RawLog;
use App\Repository\APIKeyRepository;
use App\Repository\PlayerJoinRepository;
use App\Repository\RawLogRepository;
use App\Service\RawLogService;
use App\Utilities\PlainTextResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api')]
class ApiController extends AbstractController
{
    final public const QUERY_IP = 1;

    final public const QUERY_CMD = 2;

    final public const QUERY_CALLSIGN = 3;

    #[Route(path: '/query', name: 'api_query')]
    public function query(Request $request, PlayerJoinRepository $joinRepository): Response
    {
        $query = $request->get('query');
        $action = $this->queryType($query);

        if ($action === self::QUERY_IP) {
            $data = $joinRepository->findUniqueJoinsByIP($query);
        } elseif ($action === self::QUERY_CMD) {
            $data = $this->commandHandler($query);
        } else {
            $data = $joinRepository->findUniqueIPsByCallsign($query);
        }

        return $this->render('api/query.txt.twig', [
            'type' => $action,
            'data' => $data,
        ]);
    }

    #[Route(path: '/report-join', name: 'api_report_join')]
    public function reportJoin(Request $request, RawLogService $rawLogService, EntityManagerInterface $entityManager, APIKeyRepository $keyRepository, RawLogRepository $rawLogRepository): Response
    {
        $apiKeyRaw = $request->get('apikey');
        $apiKey = $keyRepository->findOneBy([
            'active' => true,
            'key' => $apiKeyRaw,
        ]);

        if ($apiKey === null) {
            throw $this->createAccessDeniedException(sprintf('Invalid API key could not be found: %s', $apiKeyRaw));
        }

        $entry = new RawLog();
        $entry->setCallsign($request->get('callsign'));
        $entry->setBzid($request->get('bzid'));
        $entry->setIpAddress($request->get('ipaddress'));
        $entry->setHostname($request->get('hostname'));
        $entry->setApikey($apiKey);
        $entry->setBuild($request->get('build'));

        $rawLogRepository->add($entry);
        $rawLogService->updatePlayerData($entry);
        $entityManager->flush();

        return $this->render('api/report-join.txt.twig', [
            'log' => $entry,
        ], new PlainTextResponse());
    }

    public function queryType(string $query): int
    {
        if ($query[0] === '/') {
            return self::QUERY_CMD;
        }

        if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $query)) {
            return self::QUERY_IP;
        }

        return self::QUERY_CALLSIGN;
    }

    public function commandHandler(string $query): array
    {
        [$command, $body] = explode(' ', substr($query, 1));

        if ($command === 'host') {
            // @TODO: Implement host lookup
            return [];
        }

        return [];
    }
}
