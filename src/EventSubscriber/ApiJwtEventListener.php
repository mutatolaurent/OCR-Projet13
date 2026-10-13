<?php

namespace App\EventSubscriber;

use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Event subscriber pour gérer les événements liés à l'authentification JWT.
 *
 * Ce subscriber écoute les événements de JWT manquant, invalide ou expiré,
 * et renvoie une réponse JSON appropriée avec un code d'état HTTP correspondant.
 * Il log également les erreurs dans le fichier api_error.log.
 */
class ApiJwtEventListener implements EventSubscriberInterface
{
    public function __construct(
        #[Autowire(service: 'monolog.logger.api')]
        private LoggerInterface $apiLogger
    ) {
    }

    /**
     * Déclaration des événements auxquels ce subscriber s'abonne.
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::JWT_NOT_FOUND => 'onJwtNotFound',
            Events::JWT_INVALID   => 'onJwtInvalid',
            Events::JWT_EXPIRED   => 'onJwtExpired',
        ];
    }

    public function onJwtNotFound(JWTNotFoundEvent $event): void
    {
        $this->handleJwtError($event, 'API_JWT_NOT_FOUND', 'Jeton d\'authentification manquant.');
    }

    public function onJwtInvalid(JWTInvalidEvent $event): void
    {
        $this->handleJwtError($event, 'API_JWT_INVALID', 'Jeton d\'authentification invalide.');
    }

    public function onJwtExpired(JWTExpiredEvent $event): void
    {
        $this->handleJwtError($event, 'API_JWT_EXPIRED', 'Jeton d\'authentification expiré.');
    }

    /**
     * Gère les erreurs JWT en loggant l'erreur et en définissant une réponse JSON.
     *
     * @param JWTNotFoundEvent|JWTInvalidEvent|JWTExpiredEvent $event L'événement JWT.
     * @param string $source La source de l'erreur (pour le log).
     * @param string $message Le message d'erreur à afficher.
     */
    private function handleJwtError(object $event, string $source, string $message): void
    {
        $request = $event->getRequest();
        $statusCode = JsonResponse::HTTP_UNAUTHORIZED; // 401

        $this->apiLogger->error(sprintf('[%s] Erreur %d : %s', $source, $statusCode, $message), [
            'source' => $source,
            'status_code' => $statusCode,
            'url' => $request->getUri(),
            'method' => $request->getMethod(),
        ]);

        $event->setResponse(new JsonResponse([
            'status' => $statusCode,
            'message' => $message
        ], $statusCode));
    }
}
