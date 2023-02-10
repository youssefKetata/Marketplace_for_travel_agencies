<?php

namespace App\Entity;

use App\Repository\FileDataRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: FileDataRepository::class)]
class FileData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 128)]
    private $name;

    #[ORM\Column(type: 'string', length: 128)]
    private $directoryPath;

    #[ORM\Column(type: 'string', length: 32)]
    private $extension;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $alt;


    #[ORM\Column(type: 'smallint', nullable: true)]
    private $ordre;




    use TimestampableEntity;

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

    public function getDirectoryPath(): ?string
    {
        return $this->directoryPath;
    }

    public function setDirectoryPath(string $directoryPath): self
    {
        $this->directoryPath = $directoryPath;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(?string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }



    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(?int $ordre): self
    {
        $this->ordre = $ordre;

        return $this;
    }

    public function __toString()
    {
        return "";
    }
}
