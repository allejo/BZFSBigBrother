<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Controller\ApiController;
use App\Entity\APIKey;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiKeySubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();

        // When a controller class defines multiple action methods, the controller
        // is returned as [$controllerInstance, 'methodName']
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($controller instanceof ApiController) {
            $apiKeyRaw = $event->getRequest()->headers->get('x-api-key');
            $keyRepository = $this->entityManager->getRepository(APIKey::class);
            $apiKey = $keyRepository->findOneBy([
                'active' => true,
                'key' => $apiKeyRaw,
            ]);

            if ($apiKey === null) {
                throw new AccessDeniedHttpException(sprintf('Invalid API key could not be found: %s', $apiKeyRaw));
            }

            $event->getRequest()->attributes->set('apikey', $apiKey);
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $event->getResponse()->headers->set('Content-Type', 'text/plain');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}
