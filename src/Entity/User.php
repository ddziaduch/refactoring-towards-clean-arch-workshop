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
    #[ORM\Column()]
    private string $password = '';

    #[ORM\Column(length: 2000, nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(length: 2000, nullable: true)]
    private ?string $image = null;

    public function __construct(
        #[ORM\Column(length: 100, unique: true)]
        private string $email,

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
        return $this->email;
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

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}
