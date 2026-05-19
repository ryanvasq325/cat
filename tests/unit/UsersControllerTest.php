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

    expect($result->getStatusCode())->toBe(201);

    expect($json['status'])->toBeTrue();

    expect($json['msg'])->toContain('Usuário salvo com sucesso!');


});
test('updateUsers com dados validos retorna 201 com status true', function () {
    $request = (new RequestFactory())
        ->createRequest('POST', '/usuario/insert')
        ->withParsedBody([
            'nome'      => 'calangudo',
            'sobrenome' => 'soberano',
            'cpf'       => '111.222.333-44',
            'rg'        => '1234',
            'ativo'     => 'true',
        ]);

    $response = (new ResponseFactory())->createResponse();
    $result   = (new app\controller\Users())->insert($request, $response);
    $result->getBody()->rewind();
    $json = json_decode($result->getBody()->getContents(), true);
    $id   = $json['id'];


    $request = (new RequestFactory())
        ->createRequest('POST', '/usuario/update')
        ->withParsedBody([
            'id'        => $id,
            'nome'      => 'drango',
            'sobrenome' => 'alama',
            'cpf'       => '111.222.333-44',
            'rg'        => '9999',
            'ativo'     => 'true',
        ]);

    $response = (new ResponseFactory())->createResponse();
    $result   = (new app\controller\Users())->update($request, $response);
    $result->getBody()->rewind();
    $json = json_decode($result->getBody()->getContents(), true);

    expect($result->getStatusCode())->toBe(201);
    expect($json['status'])->toBeTrue();
    expect($json['msg'])->toContain('Usuário alterado com sucesso!');
});

test('updateUsers sem ID retorna 403 com status false', function () {
    $request = (new RequestFactory())
        ->createRequest('POST', '/usuario/update')
        ->withParsedBody([
            'nome' => 'Sem ID',
        ]);

    $response = (new ResponseFactory())->createResponse();
    $result   = (new app\controller\Users())->update($request, $response);
    $result->getBody()->rewind();
    $json = json_decode($result->getBody()->getContents(), true);

    expect($result->getStatusCode())->toBe(403);
    expect($json['status'])->toBeFalse();
    expect($json['msg'])->toContain('Por favor informe o ID do registro');
});


test('deleteUsers com ID valido retorna 200 com status true', function () {
    // Insere um usuário para deletar
    $request = (new RequestFactory())
        ->createRequest('POST', '/usuario/insert')
        ->withParsedBody([
            'nome'      => 'Baianin',
            'sobrenome' => 'maua',
            'cpf'       => '999.888.777-66',
            'rg'        => '5555',
            'ativo'     => 'true',
        ]);

    $response = (new ResponseFactory())->createResponse();
    $result   = (new app\controller\Users())->insert($request, $response);
    $result->getBody()->rewind();
    $json = json_decode($result->getBody()->getContents(), true);
    $id   = $json['id'];


    $request = (new RequestFactory())
        ->createRequest('POST', '/usuario/delete')
        ->withParsedBody(['id' => $id]);

    $response = (new ResponseFactory())->createResponse();
    $result   = (new app\controller\Users())->delete($request, $response);
    $result->getBody()->rewind();
    $json = json_decode($result->getBody()->getContents(), true);

    expect($result->getStatusCode())->toBe(200);
    expect($json['status'])->toBeTrue();
    expect($json['msg'])->toContain('Usuário removido com sucesso!');
});

test('deleteUsers sem ID retorna 403 com status false', function () {
    $request = (new RequestFactory())
        ->createRequest('POST', '/usuario/delete')
        ->withParsedBody([]);

    $response = (new ResponseFactory())->createResponse();
    $result   = (new app\controller\Users())->delete($request, $response);
    $result->getBody()->rewind();
    $json = json_decode($result->getBody()->getContents(), true);

    expect($result->getStatusCode())->toBe(403);
    expect($json['status'])->toBeFalse();
    expect($json['msg'])->toContain('Informe o código do usuário');
});


test('listingdata retorna estrutura correta com status 200', function () {
    $request = (new RequestFactory())
        ->createRequest('POST', '/usuario/listingdata')
        ->withParsedBody([
            'start'  => 0,
            'length' => 10,
            'order'  => [['column' => '0', 'dir' => 'DESC']],
            'search' => ['value' => ''],
        ]);

    $response = (new ResponseFactory())->createResponse();
    $result   = (new app\controller\Users())->listingdata($request, $response);
    $result->getBody()->rewind();
    $json = json_decode($result->getBody()->getContents(), true);

    expect($result->getStatusCode())->toBe(200);
    expect($json)->toHaveKey('recordsTotal');
    expect($json)->toHaveKey('recordsFiltered');
    expect($json)->toHaveKey('data');
    expect($json['data'])->toBeArray();
});

test('listingdata com termo de busca retorna resultado filtrado', function () {
    $request = (new RequestFactory())
        ->createRequest('POST', '/usuario/listingdata')
        ->withParsedBody([
            'start'  => 0,
            'length' => 10,
            'order'  => [['column' => '1', 'dir' => 'ASC']],
            'search' => ['value' => 'Chuck'],
        ]);

    $response = (new ResponseFactory())->createResponse();
    $result   = (new app\controller\Users())->listingdata($request, $response);
    $result->getBody()->rewind();
    $json = json_decode($result->getBody()->getContents(), true);

    expect($result->getStatusCode())->toBe(200);
    expect($json['recordsFiltered'])->toBeInt();
    expect($json['data'])->toBeArray();
});
