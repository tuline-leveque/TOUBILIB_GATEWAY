<?php

namespace toubilib\core\application\usecases;
use toubilib\api\dtos\AuthnDTO;
use toubilib\api\dtos\InputAuthnDTO;
use toubilib\core\application\usecases\interfaces\AuthnProviderInterface;
use toubilib\core\exceptions\ConnexionException;
use toubilib\infra\repositories\interface\AuthnRepositoryInterface;

class AuthnProvider implements AuthnProviderInterface {

    private AuthnRepositoryInterface $authRepository;

    public function __construct(AuthnRepositoryInterface $authRepository) {
        $this->authRepository = $authRepository;
    }

    /**
     * @throws ConnexionException
     */
    public function signin(InputAuthnDTO $user_dto): AuthnDTO {

        $user = null;

        try {
            // On essaie de récupérer l'utilisateur
            $user = $this->authRepository->getUser($user_dto->email);
        } catch (\Exception $e) {
            // Si le repo lève une exception (ex: NotFound), on l'ignore ici.
            // $user restera 'null', et c'est le 'if' suivant qui gérera l'erreur.
        }

        if (!$user || !password_verify($user_dto->password, $user->password)) {
            // On lève la MÊME erreur dans tous les cas d'échec
            throw new ConnexionException("Identifiants incorrects.");
        }

        return new AuthnDTO(
            id: $user->id,
            email: $user->email,
            role: $user->role
        );
    }
}