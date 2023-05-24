<?php

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OfferRepository::class)]
class Offer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 45)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $nbDays = null;

    #[ORM\OneToMany(mappedBy: 'offer', targetEntity: OfferProductType::class, cascade: ['persist','remove'], fetch: 'EAGER')]
    private Collection $offerProductTypes;

    #[ORM\OneToMany( mappedBy: 'offer', targetEntity: SellerOffer::class, cascade: ['persist','remove'])]
    private Collection $sellerOffers;

    public function __construct()
    {
        $this->offerProductTypes = new ArrayCollection();
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

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNbDays(): ?int
    {
        return $this->nbDays;
    }

    public function setNbDays(int $nbDays): self
    {
        $this->nbDays = $nbDays;

        return $this;
    }

    /**
     * @return Collection<int, OfferProductType>
     */
    public function getOfferProductTypes(): Collection
    {
        return $this->offerProductTypes;
    }


    public function getNbProductTypes():int {
        return $this->offerProductTypes->count();
    }

    public function addOfferProductType(OfferProductType $offerProductType): self
    {
        if (!$this->offerProductTypes->contains($offerProductType)) {
            /* $offerProductType->setOffer($this);
             $this->offerProductTypes[] = $offerProductType;*/
           $this->offerProductTypes->add($offerProductType);
             $offerProductType->setOffer($this);
        }

        return $this;
    }

    public function removeOfferProductType(OfferProductType $offerProductType): void
    {
        $this->offerProductTypes->removeElement($offerProductType);
//        if ($this->offerProductTypes->removeElement($offerProductType)) {
//            if ($offerProductType->getOffer() === $this) {
//                $offerProductType->setOffer(null);
//            }
//        }
//
//        return $this;
    }

    /**
     * @return Collection<int, SellerOffer>
     */
    public function getSellerOffers(): Collection
    {
        return $this->sellerOffers;
    }

    public function addSellerOffer(SellerOffer $sellerOffer): self
    {
        if (!$this->sellerOffers->contains($sellerOffer)) {
            $this->sellerOffers->add($sellerOffer);
            $sellerOffer->setOffer($this);
        }

        return $this;
    }


    public function removeSellerOffer(SellerOffer $sellerOffer): self
    {
        if ($this->sellerOffers->removeElement($sellerOffer)) {
            // set the owning side to null (unless already changed)
            if ($sellerOffer->getOffer() === $this) {
                $sellerOffer->setOffer(null);
            }
        }

        return $this;
    }

    public function getPrice():float{
        $price = 0;
        foreach($this->offerProductTypes as $offerProductType)
            $price += $offerProductType->getPrice();
        return $price;
    }

    public function getOfferProductType(ProductType $productType): ?OfferProductType{
        foreach($this->offerProductTypes as $offerProductType)
            if($offerProductType->getProductType() === $productType)
                return $offerProductType;
        return null;
    }


    public function __toString(): string
    {
        // TODO: Implement __toString() method.
        return $this->name;
    }
}
