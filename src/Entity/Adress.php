<?php

namespace App\Entity;

use App\Repository\AdressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdressRepository::class)]
class Adress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $street = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column]
    private ?int $postal_code = null;
    /*
    /**
     * @var Collection<int, User>
     */
    /*
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'adress')]
    private Collection $user_id;
    */

    #[ORM\OneToOne(inversedBy: 'adress', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Store $store = null;

    /*
    public function __construct()
    {
        $this->user_id = new ArrayCollection();
    }
    */
    public function getId(): ?int
    {
        return $this->id;
    }
    

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): ?int
    {
        return $this->postal_code;
    }

    public function setPostalCode(int $postal_code): static
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    /*
    /**
     * @return Collection<int, User>
     */

    /*
    public function getUserId(): Collection
    {
        return $this->user_id;
    }

    public function addUserId(User $userId): static
    {
        if (!$this->user_id->contains($userId)) {
            $this->user_id->add($userId);
            $userId->setAdress($this);
        }

        return $this;
    }

    public function removeUserId(User $userId): static
    {
        if ($this->user_id->removeElement($userId)) {
            // set the owning side to null (unless already changed)
            if ($userId->getAdress() === $this) {
                $userId->setAdress(null);
            }
        }

        return $this;
    }
    */

    public function getStore(): Store
    {
        return $this->store;
    }

    public function setStore(Store $store): static
    {
        $this->store = $store;

        return $this;
    }
}
