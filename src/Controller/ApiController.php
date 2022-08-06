<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\RawLog;
use App\Repository\RawLogRepository;
use App\Service\ApiQueryResponseService;
use App\Service\RawLogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api')]
class ApiController extends AbstractController
{
    #[Route(path: '/query', name: 'api_query')]
    public function query(Request $request, ApiQueryResponseService $responseService): Response
    {
        return $responseService->renderResponseFromQuery($request->get('query'));
    }

    #[Route(path: '/report-join', name: 'api_report_join')]
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
}
