<?php


declare(strict_types=1);

use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

test('insertSupplier com dados validos retorna 200 com status true', function () {
    $request = (new RequestFactory())
        ->createRequest('POST', '/supplier/insert')
        ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
        ->withParsedBody([
            'nomeExibicao' => 'Caçador de Calangos',
            'nomeLegal' => 'Caçador de Calangos LTDA',
            'numeroDocumento' => '12345678900',
            'registroSecundario' => '12345678901234',
            'dataRegistro' => '2024-01-01',
            'ativo' => 'true'
        ]);

    $response = (new ResponseFactory())->createResponse();

    $result = (new app\controller\Supplier())->insert($request, $response);

    $result->getBody()->rewind();


    $json = json_decode($result->getBody()->getContents(), true);

    expect($result->getStatusCode())->toBe(201);

    expect($json['status'])->toBeTrue();

    expect($json['msg'])->toContain('Fornecedor salvo com sucesso!');


});
