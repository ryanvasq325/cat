<?php

declare(strict_types=1);

namespace app\middleware;

class Middleware
{
    public static function web()
    {
        $middleware = function ($request, $handler) {
            $response = $handler->handle($request);
            $method   = $request->getMethod();
            $pagina   = $request->getRequestTarget();

            if ($method === 'GET') {
                $usuarioLogado = empty($_SESSION['usuario']) || empty($_SESSION['usuario']['logado']);

                if ($usuarioLogado && $pagina !== '/login') {
                    if (session_status() === PHP_SESSION_ACTIVE) {
                        session_destroy();
                    }
                    return $response->withHeader('Location', '/login')->withStatus(302);
                }

                if ($pagina === '/login' && !$usuarioLogado) {
                    return $response->withHeader('Location', '/')->withStatus(302);
                }

                if (!$usuarioLogado && (empty($_SESSION['usuario']['ativo']) || !$_SESSION['usuario']['ativo'])) {
                    if (session_status() === PHP_SESSION_ACTIVE) {
                        session_destroy();
                    }
                    return $response->withHeader('Location', '/login')->withStatus(302);
                }
            }

            return $handler->handle($request);
        };

        return $middleware;
    }

    public static function api()
    {
        $middleware = function ($request, $handler) {
            $response = $handler->handle($request);

            if (empty($_SESSION['usuario']) || empty($_SESSION['usuario']['logado'])) {
                return $response->withHeader('Content-Type', 'application/json')
                    ->withStatus(401)
                    ->getBody()->write(json_encode([
                        'status' => false,
                        'msg'    => 'Não autorizado. Faça login para continuar.',
                    ]));
            }

            if (empty($_SESSION['usuario']['ativo']) || !$_SESSION['usuario']['ativo']) {
                return $response->withHeader('Content-Type', 'application/json')
                    ->withStatus(403)
                    ->getBody()->write(json_encode([
                        'status' => false,
                        'msg'    => 'Acesso negado. Usuário inativo.',
                    ]));
            }

            return $handler->handle($request);
        };

        return $middleware;
    }
}