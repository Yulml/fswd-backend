<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'string', length: 30, unique: true)]
    private $nickname;

    #[ORM\Column(type: 'date')]
    private $dob;

    #[ORM\Column(type: 'string', length: 100, options: ["default" => "blank_avatar.jpg"])]
    private $avatar;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Owned::class)]
    private $owneds;

    public function __construct()
    {
        $this->owneds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->nickname;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getDob(): ?\DateTimeInterface
    {
        return $this->dob;
    }

    public function setDob(\DateTimeInterface $dob): self
    {
        $this->dob = $dob;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection<int, Owned>
     */
    public function getOwneds(): Collection
    {
        return $this->owneds;
    }

    public function addOwned(Owned $owned): self
    {
        if (!$this->owneds->contains($owned)) {
            $this->owneds[] = $owned;
            $owned->setUser($this);
        }

        return $this;
    }

    public function removeOwned(Owned $owned): self
    {
        if ($this->owneds->removeElement($owned)) {
            // set the owning side to null (unless already changed)
            if ($owned->getUser() === $this) {
                $owned->setUser(null);
            }
        }

        return $this;
    }
    
    public function toArray(): array {
        return [
            'id' => $this->getId(),
            'email' => $this->getEmail(),
            'password' => $this->getPassword(),
            'roles' => $this->getRoles(),
            'nickname' => $this->getNickname(),
            'dateofbirth' => $this->getDob(),
            'avatar' => $this->getAvatar(),
        ];
    }
}
