<?php

declare(strict_types=1);

namespace app\middleware;

class Middleware
{
    #metodos de autenticação via token de rota POST.
    public static function api()
    {
        $middleware = function ($request, $handler) {};
        return $middleware;
    }
    #Metodo de autenticação das rotas GET.
    public static function web()
    {
        $middleware = function ($request, $handler) {};
        return $middleware;
    }
}
