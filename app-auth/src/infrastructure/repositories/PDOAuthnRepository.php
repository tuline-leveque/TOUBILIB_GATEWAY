<?php

namespace auth\infra\repositories;

use DI\NotFoundException;
use Exception;
use PDO;
use Ramsey\Uuid\Uuid;
use Slim\Exception\HttpInternalServerErrorException;
use auth\api\dtos\CredentialsDTO;
use auth\core\domain\entities\user\User;
use auth\infra\repositories\interface\AuthnRepositoryInterface;

class PDOAuthnRepository implements AuthnRepositoryInterface {


    private PDO $authn_pdo;

    public function __construct(PDO $authn_pdo) {
        $this->authn_pdo = $authn_pdo;
    }

    public function getUser(string $email): User
    {
        try {
            $query = $this->authn_pdo->query("SELECT id, email, password, role FROM users WHERE email = '$email'");
            $res = $query->fetch(PDO::FETCH_ASSOC);
        } catch (HttpInternalServerErrorException) {
            //500
            throw new HttpInternalServerErrorException("Erreur lors de l'execution de la requete SQL.");
        } catch(\Throwable) {
            throw new Exception("Erreur lors de la reception de l'utilisateur.");
        }
        if (!$res) {
            //404
            throw new NotFoundException("L'utilisateur ayant pour email ".$email." n'existe pas.");
        } else {
            return new User(
                id: $res['id'],
                email: $res['email'],
                password: $res['password'],
                role: $res['role']
            );
        }
    }

    public function saveUser(CredentialsDTO $cred, ?int $role = 1): void
    {
        try {
            $id = Uuid::uuid4()->toString();
            // Le mot de passe est hashé dans le DTO
            $stmt = $this->authn_pdo->prepare(
                "INSERT INTO users (id, email, password, role) VALUES (:id, :email, :password, :role)"
            );
            $stmt->execute([
                'id' => $id,
                'email' => $cred->email,
                'password' => $cred->password,
                'role' => $role
            ]);

        } catch(\PDOException $e) {
            throw new \Exception("Erreur lors de l'enregistrement de l'utilisateur : " . $e->getMessage());
        }
    }
}