<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private string $password;

    public function __construct(
        #[ORM\Column(length: 2000)]
        private string $bio,

        #[ORM\Column(length: 100, unique: true)]
        private string $email,

        #[ORM\Column(length: 2000)]
        private string $image,

        #[ORM\Column(length: 100, unique: true)]
        private string $username,
    ) {
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }


    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    /**
     * @return list<string>
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function toDto(string $token): array
    {
        return [
            'bio' => $this->bio,
            'email' => $this->email,
            'image' => $this->image,
            'token' => $token,
            'username' => $this->username,
        ];
    }
}
