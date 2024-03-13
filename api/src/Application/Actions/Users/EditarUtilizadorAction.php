<?php

declare(strict_types=1);

namespace App\Application\Actions\Users;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

use \PDO;

class EditarUtilizadorAction extends Action
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

        $res = array();
        $status = 200;

        $user_id = $this->request->getAttribute('uid');


        if (isset($input["password"]) && $input["password"]) {
            $password = password_hash($input["password"], PASSWORD_DEFAULT);

            $sth = $this->link->prepare("UPDATE utilizadores SET nome = ?, username = ?, email = ?, password = ? WHERE id = ? ");
            $sth->bindParam(1, $input["nome"]);
            $sth->bindParam(2, $input["username"]);
            $sth->bindParam(3, $input["email"]);
            $sth->bindParam(4, $password);
            $sth->bindParam(5, $user_id);
            $sth->execute();
        } else {
            $sth = $this->link->prepare("UPDATE utilizadores SET nome = ?, username = ?, email = ? WHERE id = ? ");
            $sth->bindParam(1, $input["nome"]);
            $sth->bindParam(2, $input["username"]);
            $sth->bindParam(3, $input["email"]);
            $sth->bindParam(4, $user_id);
            $sth->execute();
        }



        $payload = json_encode($res);
        $this->response->getBody()->write($payload);
        return $this->response->withStatus($status)->withHeader('Content-Type', 'application/json');
    }
}
