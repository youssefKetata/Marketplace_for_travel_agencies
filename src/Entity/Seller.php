<?php

namespace App\Entity;

use App\Repository\SellerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SellerRepository::class)]
class Seller
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 45)]
    #[Assert\NotBlank(message: 'This value should not be blank')]

    #[Assert\Type(
        type: 'string',
        message: 'Not a valid name.',
    )]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: 'Your name must be at least {{ limit }} characters long',
        maxMessage: 'Your name cannot be longer than {{ limit }} characters',
    )]
    private ?string $name = null;

    #[ORM\Column(length: 45)]
    #[Assert\NotBlank(message: 'This value should not be blank')]
    #[Assert\Type(
        type: 'string',
        message: 'Not a valid website.',
    )]
    #[Assert\Length(
        min: 5,
        minMessage: 'Your first name must be at least {{ limit }} characters long')]
    #[Assert\Url]
    private ?string $website = null;


    #[ORM\Column(length: 45)]
    #[Assert\NotBlank(message: 'This value should not be blank')]
    private ?string $address = null;

    #[ORM\ManyToOne(inversedBy: 'sellers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?City $city = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToOne(inversedBy: 'seller', cascade: ['persist', 'remove'])]
    private ?Api $api = null;

    #[ORM\OneToMany(mappedBy: 'seller', targetEntity: SellerOffer::class, cascade: ['persist'],fetch: 'EAGER')]
    private Collection $sellerOffers;

    #[ORM\Column(type: 'string')]
    #[ORM\JoinColumn(nullable: true)]
    private ?string $brochureFilename;

    public function __construct()
    {
        $this->sellerOffers = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }


    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getApi(): ?Api
    {
        return $this->api;
    }

    public function setApi(?Api $api): self
    {
        $this->api = $api;
        return $this;
    }

//    /**
//     * @return Collection<int, SellerOffer>
//     */
    public function getSellerOffers(): Collection
    {
            return $this->sellerOffers;
    }

    public function getBrochureFilename(): ?string
    {
        return $this->brochureFilename;
    }

    public function setBrochureFilename(?string $brochureFilename): self
    {
        $this->brochureFilename = $brochureFilename;

        return $this;
    }

    public function addSellerOffer(SellerOffer $sellerOffer): self
    {
        if (!$this->sellerOffers->contains($sellerOffer)) {
            $this->sellerOffers->add($sellerOffer);
            $sellerOffer->setSeller($this);
        }

        return $this;
    }

    public function removeSellerOffer(SellerOffer $sellerOffer): self
    {
        if ($this->sellerOffers->removeElement($sellerOffer)) {
            // set the owning side to null (unless already changed)
            if ($sellerOffer->getSeller() === $this) {
                $sellerOffer->setSeller(null);
            }
        }

        return $this;
    }

    public function getValidSellerOffers(): Collection
    {
        //get all active offers
        $validOffers = new ArrayCollection();
        if (count($this->sellerOffers) > 0) {
            foreach ($this->sellerOffers as $sellerOffer) {
                if ($sellerOffer->getStatus() === 'active' &&
                    $sellerOffer->getOffer()->getNbProductTypes() != 0) {
                    $validOffers->add($sellerOffer);
                }
            }
        }
        return $validOffers;
    }

    public function __toString(): string
    {
        // TODO: Implement __toString() method.
         return $this->name;
    }
}
