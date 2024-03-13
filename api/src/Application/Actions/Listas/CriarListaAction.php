<?php

declare(strict_types=1);

namespace App\Application\Actions\Listas;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

use \PDO;

class CriarListaAction extends Action
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

        $user_id = $this->request->getAttribute('uid');

        $sth = $this->link->prepare("INSERT INTO listas (nome, utilizador, fechada) VALUES (?, ?, 0) ");
        $sth->bindParam(1, $input["nome"]);
        $sth->bindParam(2, $user_id);
        $sth->execute();

        return $this->response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }
}
