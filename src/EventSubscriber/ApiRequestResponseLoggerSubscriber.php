<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RequestStack;

class ApiRequestResponseLoggerSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $apiLogger;
    private RequestStack $requestStack;

    public function __construct(
        #[Autowire(service: 'monolog.logger.api')] LoggerInterface $apiLogger,
        RequestStack $requestStack
    )
    {
        $this->apiLogger = $apiLogger;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 10],
            KernelEvents::TERMINATE => ['onKernelTerminate', -10],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (str_starts_with($request->getPathInfo(), '/api')) {
            $this->apiLogger->info('Requête API reçue', [
                'method' => $request->getMethod(),
                'uri' => $request->getUri(),
                'headers' => $request->headers->all(),
                'content' => $request->getContent(),
            ]);
        }
    }

    public function onKernelTerminate(TerminateEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();

        if (str_starts_with($request->getPathInfo(), '/api')) {
            $this->apiLogger->info('Réponse API envoyée', [
                'status_code' => $response->getStatusCode(),
                'headers' => $response->headers->all(),
                'content' => $response->getContent(),
            ]);
        }
    }
}
