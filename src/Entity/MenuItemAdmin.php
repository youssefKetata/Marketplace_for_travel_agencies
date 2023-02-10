<?php

namespace App\Entity;

use App\Repository\MenuItemAdminRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuItemAdminRepository::class)]
class MenuItemAdmin
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 255)]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    private $route;

    #[ORM\Column(type: 'smallint')]
    private $displayOrder;





    //   #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'childs')]
    #[ORM\Column(type: 'string', length: 255)]
    private $parent;

//    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
//    private $childs;



    public function __construct()
    {
        // $this->childs = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function setRoute(string $route): self
    {
        $this->route = $route;

        return $this;
    }

    public function getDisplayOrder(): ?int
    {
        return $this->displayOrder;
    }

    public function setDisplayOrder(int $displayOrder): self
    {
        $this->displayOrder = $displayOrder;

        return $this;
    }


    public function getParent(): ?string
    {
        return $this->parent;
    }

    public function setParent(?string $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function __toString(): string
    {
        return $this->title;
    }

//    /**
//     * @return Collection<int, self>
//     */
    /*public function getChilds(): Collection
    {
        return $this->childs;
    }

    public function addChild(self $child): self
    {
        if (!$this->childs->contains($child)) {
            $this->childs[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->childs->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }*/
}
