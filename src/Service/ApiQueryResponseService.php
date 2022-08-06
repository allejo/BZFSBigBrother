<?php declare(strict_types=1);

namespace App\Service;

use App\Repository\PlayerJoinRepository;
use Symfony\Component\HttpFoundation\Response;

class ApiQueryResponseService
{
    public function __construct(private readonly PlayerJoinRepository $joinRepository)
    {
    }

    public function renderResponseFromQuery(string $query): Response
    {
        $action = $this->queryType($query);

        return match ($action) {
            ApiQueryType::IP => $this->renderIpResponse($query),
            ApiQueryType::CALLSIGN => $this->renderCallsignResponse($query),
            default => $this->renderCommandResponse($query),
        };
    }

    private function queryType(string $query): ApiQueryType
    {
        if ($query[0] === '/') {
            return ApiQueryType::CMD;
        }
        if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $query)) {
            return ApiQueryType::IP;
        }

        return ApiQueryType::CALLSIGN;
    }

    private function renderIpResponse(string $ipAddress): Response
    {
        $data = $this->joinRepository->findUniqueJoinsByIP($ipAddress);
        $content = [sprintf('Results of IP address lookup for %s:', $ipAddress)];
        $rendered = false;

        foreach ($data as $item) {
            $content[] = sprintf('  %s (%d times)', $item['callsign'], $item['times']);
            $rendered = true;
        }

        if (!$rendered) {
            $content[] = '  No results found';
        }

        return new Response(implode("\n", $content));
    }

    private function renderCallsignResponse(string $callsign): Response
    {
        $ipAddresses = $this->joinRepository->findUniqueIPsByCallsign($callsign);
        $content = [sprintf('Results of callsign lookup for %s:', $callsign)];
        $rendered = false;

        foreach ($ipAddresses as $ipAddress) {
            $joins = $this->joinRepository->findUniqueJoinsByIP($ipAddress);

            if (count($joins) === 0) {
                continue;
            }

            $content[] = sprintf('  %s:', $ipAddress);

            foreach ($joins as $join) {
                $content[] = sprintf('    %s (%d times)', $join['callsign'], $join['times']);
            }

            $rendered = true;
        }

        if (!$rendered) {
            $content[] = '  No results found';
        }

        return new Response(implode("\n", $content));
    }

    private function renderCommandResponse(string $query): Response
    {
        return new Response('Commands are not supported yet.');
    }
}
