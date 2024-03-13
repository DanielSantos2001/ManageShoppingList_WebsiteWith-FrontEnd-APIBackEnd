<?php

declare(strict_types=1);

use App\Application\Actions\Itens\CompradoItemAction;
use App\Application\Actions\Itens\CriarItemAction;
use App\Application\Actions\Itens\ObterItensAction;
use App\Application\Actions\Itens\RemoverItemAction;
use App\Application\Actions\Listas\CriarListaAction;
use App\Application\Actions\Listas\FecharListaAction;
use App\Application\Actions\Listas\ObterListasAction;
use App\Application\Actions\Users\EditarUtilizadorAction;
use App\Application\Actions\Users\LoginAction;
use App\Application\Actions\Users\LogoutAction;
use App\Application\Actions\Users\RegistoAction;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->post('/user/login', LoginAction::class);
    $app->post('/user/registo', RegistoAction::class);
    $app->post('/user/logout', LogoutAction::class);
    $app->put('/user', EditarUtilizadorAction::class);

    $app->get('/listas', ObterListasAction::class);
    $app->post('/listas', CriarListaAction::class);
    $app->post('/listas/{id}/fechar', FecharListaAction::class);
    $app->get('/listas/{id}/itens', ObterItensAction::class);


    $app->post('/itens', CriarItemAction::class);
    $app->post('/itens/{id}/comprado', CompradoItemAction::class);
    $app->delete('/itens/{id}', RemoverItemAction::class);
};
