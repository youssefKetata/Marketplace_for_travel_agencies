<?php

namespace App\Entity;

use App\Repository\MarketSubscriptionRequestRepository;
use App\Trait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: MarketSubscriptionRequestRepository::class)]
#[ORM\HasLifecycleCallbacks]
/**
 * @ORM\Entity
 * @UniqueEntity(fields={"email"}, message="This email address is already in use.")
 */

class MarketSubscriptionRequest
{
    use Trait\TimeStampTrait2;
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
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',)]
    #[Assert\Length(
        min: 5,
        minMessage: 'Not a valid Email')]
    private ?string $email = null;

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

    #[ORM\ManyToOne(inversedBy: 'marketSubscriptionRequests')]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\NotBlank(message: 'This value should not be blank')]

    private ?City $city = null;

    #[ORM\Column(length: 45, nullable: true)]
    private ?string $status = 'pending';

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }


}
