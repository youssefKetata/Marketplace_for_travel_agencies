<?php

namespace App\Entity;

use App\Repository\SellerOfferRepository;
use DateInterval;
use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity(repositoryClass: SellerOfferRepository::class)]
class SellerOffer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ["persist"], inversedBy: 'sellerOffers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Offer $offer = null;

    #[ORM\ManyToOne(inversedBy: 'sellerOffers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Seller $seller = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOffer(): ?Offer
    {
        return $this->offer;
    }

    public function setOffer(?Offer $offer): self
    {
        $this->offer = $offer;

        return $this;
    }

    public function getSeller(): ?Seller
    {
        return $this->seller;
    }

    public function setSeller(?Seller $seller): self
    {
        $this->seller = $seller;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }


    /**
     * @throws Exception
     */
    public function getRemainingDays(): int|bool
    {
        $nbDays = $this->getOffer()->getNbDays();
        $endDate = clone $this->startDate;
        $endDate->add(new DateInterval('P' . $nbDays . 'D'));
        $currentDate = new DateTime();
        if ($currentDate>$endDate) {
            return 0;
        } elseif($currentDate < $this->startDate){
            return $nbDays;
        } else {
            $remainingDays = $endDate->diff($currentDate)->days;
            return ($remainingDays >= 0) ? $remainingDays : 0;
        }
    }


    /**
     * @throws Exception
     */
    public function getStatus(): string
    {
        $currentDate = new DateTime();
        $status = '';
        if($this->startDate > $currentDate)
            $status = 'pending';
        else{
            if($this->getRemainingDays() <= 0)
                $status = 'expired';
            else
                $status = 'active';
        }
        return $status;
    }


    public function __toString(): string
    {
        return $this->offer->getName();
        // TODO: Implement __toString() method.
    }
}
