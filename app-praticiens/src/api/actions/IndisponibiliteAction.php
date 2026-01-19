<?php

namespace praticiens\api\actions;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use praticiens\core\application\usecases\interfaces\ServicePraticienInterface;
use praticiens\core\exceptions\BadRequestException;

class IndisponibiliteAction {
        public function __construct(
            private ServicePraticienInterface $servicePraticien,
        ){}

    /**
     * @throws Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args) {
            $id_prat = $args['id_prat'] ?? null;
            if(empty($id_prat)) {
                throw new BadRequestException("ID_PRAT MISSING");
            }
            $response->getBody()->write(json_encode($this->servicePraticien->getIndisponibilite($id_prat)));
            return $response->withHeader("Content-Type", "application/json");
        }
}