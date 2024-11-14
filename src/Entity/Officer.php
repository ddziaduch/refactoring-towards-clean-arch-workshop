<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="officers")
 */
class Officer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $status;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\EmergencyCall")
     * @ORM\JoinColumn(name="current_assignment_id", referencedColumnName="id", nullable=true)
     */
    private $currentAssignment;

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
