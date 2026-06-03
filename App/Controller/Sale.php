<?php

declare(strict_types=1);

namespace App\Controller;


final class Home extends Base
{
    public function home($request, $response)
    {
        return $this->getTwig()
            ->render($response, $this->setView('home'), [
                'titulo' => 'Início',
            ])
            ->withHeader('Content-Type', 'text/html')
            ->withStatus(200);
    }
    public function getsalesdata($request, $response)
    {
        $qb = \App\Database\DB::select('*')->from('sale');
        $sales = $qb->fetchAllAssociative();
        return $this->json($response, ['status' => true, 'data' => $sales], 200);
    }
}
