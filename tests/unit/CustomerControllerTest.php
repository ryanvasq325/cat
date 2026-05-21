<?php


declare(strict_types=1);

use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

test('insertCustomer com dados validos retorna 200 com status true', function () {
    $request = (new RequestFactory())
        ->createRequest('POST', '/customer/insert')
        ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
        ->withParsedBody([
            'nomeExibicao' => 'Lemans',
            'nomeLegal' => 'Hamiltons',
            'numeroDocumento' => '432.109.876-00',
            'registroSecundario' => '12365432',
            'dataRegistro' => '05/03/1999',
            'ativo' => 'true'
        ]);

    $response = (new ResponseFactory())->createResponse();

    $result = (new app\controller\Customer())->insert($request, $response);

    $result->getBody()->rewind();


    $json = json_decode($result->getBody()->getContents(), true);
    
    expect($result->getStatusCode())->toBe(201);

    expect($json['status'])->toBeTrue();

    expect($json['msg'])->toContain('Salvo com sucesso!');


});
