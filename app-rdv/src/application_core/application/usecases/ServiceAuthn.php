<?php
namespace toubilib\core\application\usecases;

use toubilib\api\dtos\CredentialsDTO;
use toubilib\api\dtos\InputUserDTO;
use toubilib\core\application\usecases\interfaces\AuthnProviderInterface;
use toubilib\core\application\usecases\interfaces\ServiceAuthnInterface;
use toubilib\api\dtos\InputAuthnDTO;
use Firebase\JWT\JWT;
use toubilib\infra\repositories\interface\AuthnRepositoryInterface;

class ServiceAuthn implements ServiceAuthnInterface {

    private AuthnProviderInterface $userProvider;
    private AuthnRepositoryInterface $authnRepository;
    private string $secretKey;

    public function __construct(AuthnProviderInterface $provider, AuthnRepositoryInterface $authnRepository, $jwtSecret) {
        $this->userProvider = $provider;
        $this->authnRepository = $authnRepository;
        $this->secretKey = $jwtSecret;
    }

    public function login(InputAuthnDTO $user_dto, string $host) : string {

        // 1. On valide l'utilisateur
        $user = $this->userProvider->signin($user_dto);

        // 2. On construit le payload
        $payload = [
            "iss" => $host,
            "aud" => $host,
            "iat" => time(),
            "exp" => time() + 3600,
            "sub" => $user->id,
            "data" => [
                "email" => $user->email,
                "role" => $user->role,
            ]
        ];

        // 3. On encode et on return
        return JWT::encode($payload, $this->secretKey, 'HS512');
    }

    public function register(InputUserDTO $user_dto, ?int $role = 1): array {
        try {
            $passwordhash = password_hash($user_dto->password, PASSWORD_BCRYPT);
            $credential = new CredentialsDTO($user_dto->email, $passwordhash);

            $this->authnRepository->saveUser($credential, $role);
        } catch (\Exception $e) {
            return [
                'status' => $e->getCode(),
                'success' => false,
                "message" => $e->getMessage()
            ];
        }
        return [
            'status' => 201,
            'success' => true,
            "message" => "Utilisateur ajouté avec succès."
        ];
    }
}