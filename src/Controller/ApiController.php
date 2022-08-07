<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\RawLog;
use App\Repository\RawLogRepository;
use App\Service\ApiQueryResponseService;
use App\Service\LatencyRecordService;
use App\Service\RawLogService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api')]
class ApiController extends AbstractController
{
    #[Route(path: '/dronebl', name: 'api_drone_bl', methods: ['GET'])]
    public function dronebl(Request $request): Response
    {
        $ipAddress = $request->get('ip');
        $inverseIP = implode('.', array_reverse(explode('.', $ipAddress)));
        $dns = sprintf('%s.dnsbl.dronebl.org', $inverseIP);

        $result = dns_get_record($dns, DNS_A) ?: [];
        $block = count($result) > 0;

        return new JsonResponse([
            'ip' => $ipAddress,
            'countryCode' => '',
            'countryName' => '',
            'asn' => 0,
            'isp' => '',
            'block' => (int)$block,
        ]);
    }

    #[Route(path: '/query', name: 'api_query', methods: ['GET'])]
    public function query(Request $request, ApiQueryResponseService $responseService): Response
    {
        return $responseService->renderResponseFromQuery($request->get('query'));
    }

    #[Route(path: '/report-join', name: 'api_report_join', methods: ['POST'])]
    public function reportJoin(
        Request $request,
        RawLogService $rawLogService,
        EntityManagerInterface $entityManager,
        RawLogRepository $rawLogRepository
    ): Response {
        $apiKey = $request->attributes->get('apikey');

        $entry = new RawLog();
        $entry->setCallsign($request->get('callsign'));
        $entry->setBzid($request->get('bzid'));
        $entry->setIpAddress($request->get('ipaddress'));
        $entry->setHostname(gethostbyaddr($request->get('ipaddress')) ?: null);
        $entry->setApikey($apiKey);
        $entry->setBuild($request->get('build'));
        $entry->setEventTime(new DateTime());

        $rawLogRepository->add($entry);
        $rawLogService->updatePlayerData($entry);
        $entityManager->flush();

        $response = 'SUCCESS: Added join for "%s" (BZID: %s) from %s';

        return new Response(sprintf($response, $entry->getCallsign(), $entry->getBzid(), $entry->getIpAddress()));
    }

    #[Route(path: '/report-latency', name: 'api_report_latency', methods: ['POST'])]
    public function reportLatency(Request $request, LatencyRecordService $latencyRecordService): Response
    {
        $latencyRecordService->setHostname($request->get('hostname'));
        $latencyRecordService->setPort((int)$request->get('port'));
        $latencyRecordService->setApiKey($request->attributes->get('apikey'));
        $latencyRecordService->recordLatency($request->get('latency'));

        return new Response('SUCCESS: Latency reported successfully.');
    }
}
