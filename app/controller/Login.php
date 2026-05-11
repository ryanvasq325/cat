<?php

declare(strict_types=1);

namespace app\controller;

use Firebase\JWT\JWT;
use app\database\DB;

final class Login extends Base
{
    public function login($request, $response)
    {
        try {
            return $this->getTwig()
                ->render($response, $this->setView('login'), [
                    'titulo' => 'Autenticação',
                ])
                ->withHeader('Content-Type', 'text/html')
                ->withStatus(200);
        } catch (\Exception $e) {
            return $this->getTwig()
                ->render($response, $this->setView('login'), [
                    'titulo' => 'Autenticação',
                    'error'  => $e->getMessage(),
                ])
                ->withHeader('Content-Type', 'text/html')
                ->withStatus(500);
        }
    }

    public function auth($request, $response)
    {
        try {
            $form = $request->getParsedBody();

            if (!isset($form['login']) || empty($form['login'])) {
                return $this->json($response, ['status' => false, 'msg' => 'Por favor informe o login.', 'id' => 0], 403);
            }

            if (!isset($form['senha']) || empty($form['senha'])) {
                return $this->json($response, ['status' => false, 'msg' => 'Por favor informe a senha.', 'id' => 0], 403);
            }

            $user = DB::select()
                ->from('users')
                ->where('cpf = :login')
                ->andWhere('excluido = false') // ← direto no SQL, sem parâmetro
                ->setParameter('login', $form['login'])
                ->fetchAssociative();
            if (!isset($user) || empty($user)) {
                return $this->json($response, ['status' => false, 'msg' => 'Usuário ou senha inválidos!', 'id' => 0], 403);
            }

            if (!$user['ativo']) {
                return $this->json($response, ['status' => false, 'msg' => 'Por enquanto você ainda não tem permissão de acessar o sistema!', 'id' => 0], 403);
            }

            if (!password_verify($form['senha'], $user['senha'])) {
                return $this->json($response, ['status' => false, 'msg' => 'Usuário ou senha inválidos!', 'id' => 0], 403);
            }

            if (password_needs_rehash($user['senha'], PASSWORD_DEFAULT)) {
                DB::connection()->update(
                    'users',
                    ['senha' => password_hash($form['senha'], PASSWORD_DEFAULT)],
                    ['id'    => $user['id']]
                );
            }

            $now     = time();
            $payload = [
                'iat' => $now,
                'exp' => $now + (60 * 60 * 8),
                'sub' => $user['id'],
            ];

            $token = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

            setcookie('auth_token', $token, [
                'expires'  => $now + (60 * 60 * 8),
                'path'     => '/',
                'httponly' => true,
                'secure'   => ($_ENV['APP_ENV'] ?? 'production') !== 'local',
                'samesite' => 'Strict',
            ]);

            $_SESSION['user'] = [
                'id'            => $user['id'],
                'nome'          => $user['nome'],
                'sobrenome'     => $user['sobrenome'],
                'cpf'           => $user['cpf'],
                'rg'            => $user['rg'],
                'ativo'         => $user['ativo'],
                'logado'        => true,
                'administrador' => $user['administrador'],
                'criado_em'     => $user['criado_em'],
                'atualizado_em' => $user['atualizado_em'],
            ];

            return $this->json($response, ['status' => true, 'msg' => 'Seja bem-vindo de volta!', 'id' => $user['id']], 200);
        } catch (\Exception $e) {
            return $this->json($response, ['status' => false, 'msg' => 'Restrição: ' . $e->getMessage(), 'id' => 0], 500);
        }
    }

    public function precadastro($request, $response)
    {
        $conn = DB::connection(); # ← fora do try

        try {
            $form = $request->getParsedBody();

            if (empty($form['nome'])) {
                return $this->json($response, ['status' => false, 'msg' => 'Por favor informe o nome.', 'id' => 0], 403);
            }
            if (empty($form['cpf'])) {
                return $this->json($response, ['status' => false, 'msg' => 'Por favor informe o CPF.', 'id' => 0], 403);
            }
            if (empty($form['senhaCadastro'])) {
                return $this->json($response, ['status' => false, 'msg' => 'Por favor informe a senha.', 'id' => 0], 403);
            }
            if (empty($form['email'])) {
                return $this->json($response, ['status' => false, 'msg' => 'Por favor informe o e-mail.', 'id' => 0], 403);
            }

            $cpfExiste = DB::select('id')
                ->from('users')
                ->where('cpf = :cpf')
                ->andWhere('excluido = false')
                ->setParameter('cpf', $form['cpf'])
                ->fetchAssociative();

            if ($cpfExiste) {
                return $this->json($response, ['status' => false, 'msg' => 'CPF já cadastrado!', 'id' => 0], 403);
            }

            $conn->beginTransaction();

            $conn->insert('users', [
                'nome'          => $form['nome'],
                'sobrenome'     => $form['sobrenome'] ?? null,
                'cpf'           => $form['cpf'],
                'rg'            => $form['rg']        ?? null,
                'senha'         => password_hash($form['senhaCadastro'], PASSWORD_DEFAULT),
                'ativo'         => (int) false, // 0 — aguarda liberação do administrador
                'administrador' => (int) false, // 0
                'criado_em'     => (new \DateTime())->format('Y-m-d H:i:s'),
                'atualizado_em' => (new \DateTime())->format('Y-m-d H:i:s'),
            ]);

            $id_usuario = $conn->lastInsertId();

            if (!empty($form['email'])) {
                $conn->insert('contact', [
                    'id_usuario' => $id_usuario,
                    'tipo'       => 'EMAIL',
                    'contato'    => $form['email'],
                ]);
            }

            if (!empty($form['celular'])) {
                $conn->insert('contact', [
                    'id_usuario' => $id_usuario,
                    'tipo'       => 'CELULAR',
                    'contato'    => $form['celular'],
                ]);
            }

            if (!empty($form['whatsapp'])) {
                $conn->insert('contact', [
                    'id_usuario' => $id_usuario,
                    'tipo'       => 'WHATSAPP',
                    'contato'    => $form['whatsapp'],
                ]);
            }

            $conn->commit();

            return $this->json($response, ['status' => true, 'msg' => 'Cadastro realizado com sucesso! Aguarde a liberação do administrador.', 'id' => $id_usuario], 201);
        } catch (\Exception $e) {
            if ($conn->isTransactionActive()) {
                $conn->rollBack();
            }
            return $this->json($response, ['status' => false, 'msg' => 'Restrição: ' . $e->getMessage(), 'id' => 0], 500);
        }
    }
}
