<?php

namespace App\Entity;

use App\Repository\ParameterRepository;
use Doctrine\ORM\Mapping as ORM;
use Sherlockode\ConfigurationBundle\Model\Parameter as BaseParameter;

#[ORM\Entity()]
class Parameter extends BaseParameter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected $id;

    #[ORM\Column(type: 'string', length: 255)]
    protected $path;

    #[ORM\Column(type: 'text', nullable: true)]
    protected $value;

}
