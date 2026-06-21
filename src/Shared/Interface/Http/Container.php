<?php

declare(strict_types=1);

namespace App\Shared\Interface\Http;

use App\Shared\Infrastructure\Persistence\PdoConnectionFactory;
use App\Shared\Infrastructure\Service\NativeUuidGenerator;
use App\Shared\Interface\Exception\ExceptionResponseFactory;
use App\User\Application\UseCase\CreateUserUseCase;
use App\User\Application\UseCase\DeleteUserUseCase;
use App\User\Application\UseCase\GetUserByIdUseCase;
use App\User\Application\UseCase\ListUsersUseCase;
use App\User\Application\UseCase\UpdateUserUseCase;
use App\User\Infrastructure\Repository\PostgresUserRepository;
use App\User\Infrastructure\Service\Argon2idPasswordHasher;
use App\User\Interface\Controller\CreateUserController;
use App\User\Interface\Controller\DeleteUserController;
use App\User\Interface\Controller\GetUserByIdController;
use App\User\Interface\Controller\ListUsersController;
use App\User\Interface\Controller\UpdateUserController;
use PDO;

final readonly class Container
{
    private PDO $pdo;

    private PostgresUserRepository $userRepository;

    private Argon2idPasswordHasher $passwordHasher;

    private NativeUuidGenerator $uuidGenerator;

    private ExceptionResponseFactory $exceptionResponseFactory;

    public function __construct()
    {
        $this->pdo = PdoConnectionFactory::create();
        $this->userRepository = new PostgresUserRepository($this->pdo);
        $this->passwordHasher = new Argon2idPasswordHasher();
        $this->uuidGenerator = new NativeUuidGenerator();
        $this->exceptionResponseFactory = new ExceptionResponseFactory();
    }

    public function createUserController(): CreateUserController
    {
        return new CreateUserController(
            new CreateUserUseCase(
                $this->userRepository,
                $this->passwordHasher,
                $this->uuidGenerator,
            ),
            $this->exceptionResponseFactory,
        );
    }

    public function getUserByIdController(): GetUserByIdController
    {
        return new GetUserByIdController(
            new GetUserByIdUseCase($this->userRepository),
            $this->exceptionResponseFactory,
        );
    }

    public function listUsersController(): ListUsersController
    {
        return new ListUsersController(
            new ListUsersUseCase($this->userRepository),
            $this->exceptionResponseFactory,
        );
    }

    public function updateUserController(): UpdateUserController
    {
        return new UpdateUserController(
            new UpdateUserUseCase(
                $this->userRepository,
                $this->passwordHasher,
            ),
            $this->exceptionResponseFactory,
        );
    }

    public function deleteUserController(): DeleteUserController
    {
        return new DeleteUserController(
            new DeleteUserUseCase($this->userRepository),
            $this->exceptionResponseFactory,
        );
    }
}
