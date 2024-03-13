<?php

declare(strict_types=1);

namespace App\Application\Actions\Users;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

use \PDO;

class LogoutAction extends Action
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

        $sth = $this->link->prepare("UPDATE utilizadores SET token = null WHERE id = ?");
        $sth->bindParam(1, $user_id);
        $sth->execute();

        return $this->response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}
