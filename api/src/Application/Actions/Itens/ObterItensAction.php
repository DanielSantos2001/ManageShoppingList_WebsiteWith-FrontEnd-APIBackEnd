<?php

declare(strict_types=1);

namespace App\Application\Actions\Itens;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

use \PDO;

class ObterItensAction extends Action
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
        $lista_id = $this->args["id"];

        $sth = $this->link->prepare("SELECT * FROM itens WHERE lista = ?");
        $sth->bindParam(1, $lista_id);
        $sth->execute();

        $itens = $sth->fetchAll();

        $json = json_encode($itens);
        $this->response->getBody()->write($json);
        return $this->response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}
