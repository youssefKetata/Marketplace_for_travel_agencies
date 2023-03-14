<?php

namespace App\Entity;

use App\Repository\ContinentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Translatable\Translatable ;

#[ORM\Entity(repositoryClass: ContinentRepository::class)]
#[UniqueEntity('name')]
class Continent //implements Translatable
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 2)]
    private $code;

    //#[Gedmo\Translatable]
    #[ORM\Column(name: 'name', type: 'string', length: 50)]
    #[Assert\NotBlank(message: 'This value should not be blank')]
    #[Assert\Unique]
    private $name;

    #[ORM\OneToMany(mappedBy: 'continent', targetEntity: Country::class)]
    private $countries;

    #[ORM\Column(type: 'boolean')]
    private $active;


    /**
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    #[Gedmo\Locale]
    private $locale;


    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }
    public function __construct()
    {
        $this->countries = new ArrayCollection();
    }


    public function setCode($code): void
    {
        $this->code = $code;
    }

    public function getCode(): string
    {
        return $this->code;
    }


    public function getName(): string
    {
        return $this->name;
    }


    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }


    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

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
            $country->setContinent($this);
        }

        return $this;
    }

    public function removeCountry(Country $country): self
    {
        if ($this->countries->removeElement($country)) {
            // set the owning side to null (unless already changed)
            if ($country->getContinent() === $this) {
                $country->setContinent(null);
            }
        }

        return $this;
    }


    public function __toString(): string
    {
        return $this->name;
    }
}
