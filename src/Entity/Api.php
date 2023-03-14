<?php

namespace App\Entity;

use App\Repository\ApiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ApiRepository::class)]
class Api
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotNull(message: 'This value should not be blank.')]
    #[Assert\Url(message: '{{value}} is not a valid Url')]
    private ?string $baseUrl = null;

    #[ORM\Column(length: 45)]
    #[Assert\NotNull(message: 'This value should not be blank.')]
    private ?string $apiKeyValue = null;

    #[ORM\Column(length: 45)]
    #[Assert\NotNull(message: 'This value should not be blank.')]
    #[Assert\Length(
        min: 2,
        minMessage: 'login must be at least {{ limit }} characters long',
    )]
    private ?string $login = null;

    #[ORM\Column(length: 45)]
    #[Assert\NotBlank(message: 'This value should not be blank.')]
    #[Assert\Length(
        min: 5,
        max: 50,
        minMessage: 'Password must be at least {{ limit }} characters long',
        maxMessage: 'Password cannot be longer than {{ limit }} characters',
    )]
    private ?string $password = null;

    #[ORM\OneToOne(mappedBy: 'api', cascade: ['persist', 'remove'])]
    private ?Seller $seller = null;

    #[ORM\OneToMany(mappedBy: 'api_idApi', targetEntity: ApiProduct::class)]
    private Collection $apiProducts;

    public function __construct()
    {
        $this->apiProducts = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBaseUrl(): ?string
    {
        return $this->baseUrl;
    }

    public function setBaseUrl(?string $baseUrl): self
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    public function getApiKeyValue(): ?string
    {
        return $this->apiKeyValue;
    }

    public function setApiKeyValue(?string $apiKeyValue): self
    {
        $this->apiKeyValue = $apiKeyValue;

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(?string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSeller(): ?Seller
    {
        return $this->seller;
    }

    public function setSeller(?Seller $seller): self
    {
        // unset the owning side of the relation if necessary
        if ($seller === null && $this->seller !== null) {
            $this->seller->setApi(null);
        }

        // set the owning side of the relation if necessary
        if ($seller !== null && $seller->getApi() !== $this) {
            $seller->setApi($this);
        }

        $this->seller = $seller;

        return $this;
    }

    /**
     * @return Collection<int, ApiProduct>
     */
    public function getApiProducts(): Collection
    {
        return $this->apiProducts;
    }

    public function addApiProduct(ApiProduct $apiProduct): self
    {
        if (!$this->apiProducts->contains($apiProduct)) {
            $this->apiProducts->add($apiProduct);
            $apiProduct->setApi($this);
        }

        return $this;
    }

    public function removeApiProduct(ApiProduct $apiProduct): self
    {
        if ($this->apiProducts->removeElement($apiProduct)) {
            // set the owning side to null (unless already changed)
            if ($apiProduct->getApi() === $this) {
                $apiProduct->setApi(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        // TODO: Implement __toString() method.
        return $this->getLogin();
    }
}
