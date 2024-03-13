<?php

declare(strict_types=1);

namespace App\Application\Actions\Listas;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

use \PDO;

class ObterListasAction extends Action
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

        $user_id = $this->request->getAttribute('uid');

        $sth = $this->link->prepare("SELECT * FROM listas WHERE utilizador = ? ");
        $sth->bindParam(1, $user_id);
        $sth->execute();

        $listas = $sth->fetchAll();

        $json = json_encode($listas);
        $this->response->getBody()->write($json);
        return $this->response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}
