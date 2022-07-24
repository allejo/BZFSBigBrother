<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\RawLog;
use App\Repository\APIKeyRepository;
use App\Repository\RawLogRepository;
use App\Service\RawLogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/report-join", name="api_report_join")
     */
    public function reportJoin(
        Request $request,
        RawLogService $rawLogService,
        EntityManagerInterface $entityManager,
        APIKeyRepository $keyRepository,
        RawLogRepository $rawLogRepository
    ): Response {
        $apiKeyRaw = $request->get('apikey');
        $apiKey = $keyRepository->findOneBy([
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

        return $this->json([
            'success' => true,
        ]);
    }
}
