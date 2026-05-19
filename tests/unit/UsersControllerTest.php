<?php


declare(strict_types=1);

use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

test('insertUsers com dados validos retorna 200 com status true', function () {
    $request = (new RequestFactory())
        ->createRequest('POST', '/usuario/insert')
        ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
        ->withParsedBody([
            'nome' => 'Chuck',
            'sobrenome' => 'Norris',
            'cpf' => '098.654.096-69',
            'rg' => '5675',
            'ativo' => 'true'
        ]);

    $response = (new ResponseFactory())->createResponse();

    $result = (new app\controller\Users())->insert($request, $response);

    $result->getBody()->rewind();


    $json = json_decode($result->getBody()->getContents(), true);
    #Capturamos o codigo de resposta e o status do json
    #Foi criado.
    expect($result->getStatusCode())->toBe(201);

    expect($json['status'])->toBeTrue();

    expect($json['msg'])->toContain('Usuário salvo com sucesso!');


});
