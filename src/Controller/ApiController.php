<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\RawLog;
use App\Repository\APIKeyRepository;
use App\Repository\PlayerJoinRepository;
use App\Repository\RawLogRepository;
use App\Service\RawLogService;
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
            $data = $this->queryCallsignHandler($query, $joinRepository);
        }

        return $this->renderQueryResponse($action, $data, $query);
    }

    #[Route(path: '/report-join', name: 'api_report_join')]
    public function reportJoin(Request $request, RawLogService $rawLogService, EntityManagerInterface $entityManager, APIKeyRepository $keyRepository, RawLogRepository $rawLogRepository): Response
    {
        $apiKey = $request->attributes->get('apikey');

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
        ]);
    }

    /**
     * @return self::QUERY_*
     */
    private function queryType(string $query): int
    {
        if ($query[0] === '/') {
            return self::QUERY_CMD;
        }

        if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $query)) {
            return self::QUERY_IP;
        }

        return self::QUERY_CALLSIGN;
    }

    /**
     * @phpstan-import-type JoinRecord from PlayerJoinRepository
     *
     * @return array<string, JoinRecord[]>
     */
    private function queryCallsignHandler(string $query, PlayerJoinRepository $joinRepository): array
    {
        $returnData = [];
        $ipAddresses = $joinRepository->findUniqueIPsByCallsign($query);

        foreach ($ipAddresses as $ipAddress) {
            $joins = $joinRepository->findUniqueJoinsByIP($ipAddress);

            if (count($joins) === 0) {
                continue;
            }

            if (!isset($returnData[$ipAddress])) {
                $returnData[$ipAddress] = [];
            }

            foreach ($joins as $join) {
                $returnData[$ipAddress][] = $join;
            }
        }

        return $returnData;
    }

    /**
     * @return array<number, string>
     */
    private function commandHandler(string $query): array
    {
        [$command, $body] = explode(' ', substr($query, 1));

        if ($command === 'host') {
            // @TODO: Implement host lookup
            return [];
        }

        return [];
    }

    private function renderQueryResponse(int $action, array $data, string $query): Response
    {
        /** @var string[] $content */
        $content = [];

        if ($action === self::QUERY_IP) {
            $content[] = sprintf('Results of IP address lookup for %s:', $query);
            $rendered = false;

            foreach ($data as $item) {
                $content[] = sprintf('  %s (%d times)', $item['callsign'], $item['times']);
                $rendered = true;
            }

            if (!$rendered) {
                $content[] = '  No results found';
            }
        } elseif ($action === self::QUERY_CALLSIGN) {
            $content[] = sprintf('Results of callsign lookup for %s:', $query);
            $rendered = false;

            foreach ($data as $ipAddress => $joins) {
                $content[] = sprintf('  %s:', $ipAddress);
                $rendered = true;

                foreach ($joins as $join) {
                    $content[] = sprintf('    %s (%d times)', $join['callsign'], $join['times']);
                }
            }

            if (!$rendered) {
                $content[] = '  No results found';
            }
        }

        return new Response(implode("\n", $content));
    }
}
