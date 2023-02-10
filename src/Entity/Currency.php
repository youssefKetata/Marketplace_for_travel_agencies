<?php

namespace App\Entity;

use App\Repository\CurrencyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CurrencyRepository::class)]
class Currency
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 3)]
    private $code;

    #[ORM\Column(type: 'string', length: 128)]
    #[Assert\NotBlank(message: 'This value should not be blank')]
    private $name;

    #[ORM\Column(type: 'string', length: 16)]
    #[Assert\NotBlank(message: 'This value should not be blank')]
    private $symbol;

    #[ORM\OneToMany(mappedBy: 'currency', targetEntity: Country::class)]
    private $countries;







    public function __construct()
    {
        $this->countries = new ArrayCollection();

    }


    public function setCode($code): void
    {
        $this->code = $code;
    }

    public function getCode(): ?string
    {
        return $this->code;
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

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): self
    {
        $this->symbol = $symbol;

        return $this;
    }

    /**
     * @return Collection<int, Country>
     */
    public function getCountries(): Collection
    {
        return $this->countries;
    }

    public function addCountry(Country $country): self
    {
        if (!$this->countries->contains($country)) {
            $this->countries[] = $country;
            $country->setCurrency($this);
        }

        return $this;
    }

    public function removeCountry(Country $country): self
    {
        if ($this->countries->removeElement($country)) {
            // set the owning side to null (unless already changed)
            if ($country->getCurrency() === $this) {
                $country->setCurrency(null);
            }
        }

        return $this;
    }


    public function __toString(): string
    {
        return (string) $this->getName();
    }

}
