<?php

namespace App\Entity;

use App\Repository\DataTableColumnRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DataTableColumnRepository::class)]
class DataTableColumn
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 64)]
    private $name;

    #[ORM\Column(type: 'string', length: 64)]
    private $datatableName;

    #[ORM\Column(type: 'string', length: 64)]
    private $selector;

    #[ORM\Column(type: 'boolean')]
    private $sortable;

    #[ORM\Column(type: 'boolean')]
    private $visible;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private $width;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $display_order;


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

    public function getDatatableName(): ?string
    {
        return $this->datatableName;
    }

    public function setDatatableName(string $datatableName): self
    {
        $this->datatableName = $datatableName;

        return $this;
    }

    public function getSelector(): ?string
    {
        return $this->selector;
    }

    public function setSelector(string $selector): self
    {
        $this->selector = $selector;

        return $this;
    }

    public function isSortable(): ?bool
    {
        return $this->sortable;
    }

    public function setSortable(bool $sortable): self
    {
        $this->sortable = $sortable;

        return $this;
    }

    public function isVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    public function getWidth(): ?string
    {
        return $this->width;
    }

    public function setWidth(?string $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getDisplayOrder(): ?int
    {
        return $this->display_order;
    }

    public function setDisplayOrder(?int $display_order): self
    {
        $this->display_order = $display_order;

        return $this;
    }
}
