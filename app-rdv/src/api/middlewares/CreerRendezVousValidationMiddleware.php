<?php

namespace toubilib\api\middlewares;

use DateTime;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;
use Slim\Exception\HttpBadRequestException;
use Slim\Routing\RouteContext;
use toubilib\api\dtos\InputRendezVousDTO;

class CreerRendezVousValidationMiddleware {
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $next) : ResponseInterface {
        $route_params = RouteContext::fromRequest($request)
            ->getRoute()
            ->getArguments() ?? null;

        $data = $request->getQueryParams();
        $data["id_prat"] = $route_params["id_prat"];

        $data["duree"] = intval($data["duree"]);
        try {
            v::key('duree', v::intType())
                ->key('motif', v::stringType()->notEmpty())
                ->key('date_heure_debut', v::stringType()->notEmpty())
                ->key('date_heure_fin', v::stringType()->notEmpty())
                ->key('id_pat', v::stringType()->notEmpty())
                ->key('id_prat', v::stringType()->notEmpty())
            ->assert($data);

        } catch (NestedValidationException $e) {
            throw new HttpBadRequestException($request, "Invalid data: " . $e->getFullMessage(), $e);
        }

        //vérification format des datetime
        foreach (['date_heure_debut', 'date_heure_fin'] as $datetime) {
            $data[$datetime] = urldecode($data[$datetime]);
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $data[$datetime]);
            if (!$date || $date->format('Y-m-d H:i:s') !== $data[$datetime]) {
                throw new HttpBadRequestException($request, "Le champ $datetime doit etre au format Y-m-d H:i:s");
            }
        }

        $rdvDTO = new InputRendezVousDTO($data);
        $request = $request->withAttribute('rdv_dto', $rdvDTO);

        return $next->handle($request);
    }
}