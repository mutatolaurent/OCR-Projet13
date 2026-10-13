<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ApiAuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{
    public function __construct(
        #[Autowire(service: 'monolog.logger.api')]
        private LoggerInterface $apiLogger
    ) {
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        // Détermination du code de statut et du message selon le type d'exception
        if ($exception instanceof CustomUserMessageAccountStatusException) {
            $statusCode = JsonResponse::HTTP_FORBIDDEN; // 403
            $message = $exception->getMessage();
            $source = 'API_AUTH_FORBIDDEN';
        } else {
            $statusCode = JsonResponse::HTTP_UNAUTHORIZED; // 401
            $message = 'Identifiants invalides.';
            $source = 'API_AUTH_UNAUTHORIZED';
        }

        // Traçage de l'erreur dans le fichier api_error.log (grâce au logger injecté)
        $this->apiLogger->error(sprintf('[%s] Erreur %d : %s', $source, $statusCode, $exception->getMessage()), [
            'source' => $source,
            'status_code' => $statusCode,
            'url' => $request->getUri(),
            'method' => $request->getMethod(),
            'exception_class' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);

        // Retour de la réponse JSON adaptée
        return new JsonResponse([
            'status' => $statusCode,
            'message' => $message
        ], $statusCode);
    }
}
