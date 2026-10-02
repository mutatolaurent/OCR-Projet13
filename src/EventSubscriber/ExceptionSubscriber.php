<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    // Injection des deux loggers dédiés
    public function __construct(
        #[Autowire(service: 'monolog.logger.web')]
        private LoggerInterface $webLogger,
        #[Autowire(service: 'monolog.logger.api')]
        private LoggerInterface $apiLogger
    ) {
    }

    public function onExceptionEvent(ExceptionEvent $event): void
    {
        $request = $event->getRequest();
        $exception = $event->getThrowable();

        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;

        // Détermination de l'origine
        $isApi = str_starts_with($request->getPathInfo(), '/api');
        $source = $isApi ? 'API' : 'WEB';

        // Choix du logger selon la source
        $logger = $isApi ? $this->apiLogger : $this->webLogger;

        // Écriture du log dans le fichier correspondant (web_error.log ou api_error.log)
        $logger->error(sprintf('[%s] Erreur %d : %s', $source, $statusCode, $exception->getMessage()), [
            'source' => $source,
            'status_code' => $statusCode,
            'url' => $request->getUri(),
            'method' => $request->getMethod(),
            'exception_class' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);

        // Gestion spécifique pour l'API (Retour JSON)
        if ($isApi) {
            $data = [
                'error' => true,
                'status' => $statusCode,
                'message' => $exception->getMessage() ?: 'Une erreur est survenue sur l\'API.',
            ];

            $event->setResponse(new JsonResponse($data, $statusCode));
            return;
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onExceptionEvent',
        ];
    }
}
