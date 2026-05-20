<?php


declare(strict_types=1);

use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

test('insertProduct com dados validos retorna 200 com status true', function () {
    $request = (new RequestFactory())
        ->createRequest('POST', '/produto/insert')
        ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
        ->withParsedBody([
            'nome' => 'Carne de calango',
            'codigo_barra' => '1234567890123',
            'unidade' => 'kg',
            'preco_compra' => '1500.00',
            'preco_venda' => '2000.00',
            'descricao' => 'fresca e saborosa',
            'ativo' => 'true'
        ]);

    $response = (new ResponseFactory())->createResponse();

    $result = (new app\controller\Product())->insert($request, $response);

    $result->getBody()->rewind();


    $json = json_decode($result->getBody()->getContents(), true);

    expect($result->getStatusCode())->toBe(201);

    expect($json['status'])->toBeTrue();

    expect($json['msg'])->toContain('Salvo com sucesso!');


});
