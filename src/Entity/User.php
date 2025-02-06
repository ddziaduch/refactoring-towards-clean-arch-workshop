<?php

namespace App\Entity;

use App\Domain\Entity\Article;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column()]
    public string $password = '';

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $bio = null;

    #[ORM\Column(length: 2000, nullable: true)]
    public ?string $image = null;

    /**
     * @var Collection<array-key, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'following', cascade: ['persist', 'remove'], orphanRemoval: true)]
    public Collection $followers;

    /**
     * @var Collection<array-key, Article>
     */
    #[ORM\OneToMany(targetEntity: Article::class, mappedBy: 'author', cascade: ['persist', 'remove'], orphanRemoval: true)]
    public Collection $articles;

    /**
     * @var Collection<array-key, self>
     */
    #[ORM\JoinTable(name: 'followers')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'follower_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'followers', cascade: ['persist', 'remove'], orphanRemoval: true)]
    public Collection $following;

    /**
     * @var Collection<array-key, Article>
     */
    #[ORM\JoinTable(name: 'favorites')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'article_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: Article::class, inversedBy: 'favoritedBy', cascade: ['persist', 'remove'], orphanRemoval: true)]
    public Collection $favorites;

    public function __construct(
        #[ORM\Column(length: 100, unique: true)]
        public string $email,

        #[ORM\Column(length: 100, unique: true)]
        public string $username,
    ) {
        $this->following = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->favorites = new ArrayCollection();
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
}
