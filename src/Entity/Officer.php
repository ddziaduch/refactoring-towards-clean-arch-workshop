<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\EmergencyCall;

#[ORM\Entity]
#[ORM\Table(name: 'officers')]
class Officer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $status = null;

    #[ORM\OneToOne(targetEntity: EmergencyCall::class)]
    #[ORM\JoinColumn(name: 'current_assignment_id', referencedColumnName: 'id', nullable: true)]
    private ?EmergencyCall $currentAssignment = null;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getCurrentAssignment(): ?EmergencyCall
    {
        return $this->currentAssignment;
    }

    public function setCurrentAssignment(?EmergencyCall $currentAssignment): self
    {
        $this->currentAssignment = $currentAssignment;
        return $this;
    }
}
