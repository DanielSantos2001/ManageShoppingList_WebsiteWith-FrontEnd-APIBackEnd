<?php

declare(strict_types=1);

namespace App\Application\Actions\Users;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

use \PDO;

class RegistoAction extends Action
{
    private PDO $link;

    public function __construct(LoggerInterface $logger, PDO $link)
    {
        parent::__construct($logger);
        $this->link = $link;
    }

    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $json = $this->request->getBody()->getContents();
        $input = json_decode($json, true); // assume-se que dados são recebidos via JSON

        $sth = $this->link->prepare("SELECT * FROM utilizadores WHERE username = ?");
        $sth->bindParam(1, $input['username']);
        $sth->execute();

        $usr = $sth->fetchAll();

        $res = array();
        $status = 200;


        if (count($usr) > 0) {
            $res["err"] = "Username em uso";
            $status = 401;
        } else {
            $password = password_hash($input["password"], PASSWORD_DEFAULT);

            $sth = $this->link->prepare("INSERT INTO utilizadores (nome, username, email, password) VALUES (?, ?, ?, ?) ");
            $sth->bindParam(1, $input["nome"]);
            $sth->bindParam(2, $input["username"]);
            $sth->bindParam(3, $input["email"]);
            $sth->bindParam(4, $password);
            $sth->execute();
            $status = 201;
        }

        $payload = json_encode($res);
        $this->response->getBody()->write($payload);
        return $this->response->withStatus($status)->withHeader('Content-Type', 'application/json');
    }
}
