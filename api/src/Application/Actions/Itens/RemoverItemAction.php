<?php

declare(strict_types=1);

namespace App\Application\Actions\Itens;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

use \PDO;

class RemoverItemAction extends Action
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
        $item_id = $this->args["id"];

        $sth = $this->link->prepare("DELETE FROM itens WHERE id = ?");
        $sth->bindParam(1, $item_id);
        $sth->execute();

        return $this->response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}
