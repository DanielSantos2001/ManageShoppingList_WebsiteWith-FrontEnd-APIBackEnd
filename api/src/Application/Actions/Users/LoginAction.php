<?php

declare(strict_types=1);

namespace App\Application\Actions\Users;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

use \PDO;

class LoginAction extends Action
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

            if (password_verify($input["password"], $usr[0]["password"])) {
                $res["token"] = md5((string) time());
                $res["username"] = $usr[0]["username"];
                $res["email"] = $usr[0]["email"];
                $res["nome"] = $usr[0]["nome"];

                $res["id"] = $usr[0]["id"];
            } else {
                $status = 401;
                $res["err"] = "Password errada";
            }


            //update do token e da validade to token
            $sth = $this->link->prepare("UPDATE utilizadores SET token = ? WHERE id = ?");
            $sth->bindParam(1, $res["token"]);
            $sth->bindParam(2, $res["id"]);
            $sth->execute();
        } else {
            $status = 401;
            $res["err"] = "Utilizador nao encontrado";
        }

        $payload = json_encode($res);
        $this->response->getBody()->write($payload);
        return $this->response->withStatus($status)->withHeader('Content-Type', 'application/json');
    }
}
