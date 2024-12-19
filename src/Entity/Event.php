<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "The name for the event cannot be empty.", allowNull: false)]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: "The name for the event cannot have less than {{ limit }} characters.",
        maxMessage: "The name for the event cannot have more than {{ limit }} characters."
    )]
    private ?string $name = null;

    #[Assert\NotBlank(message: "The provided date cannot be empty.", allowNull: false)]
    #[Assert\Type(\DateTimeInterface::class, message: "The provided date ({{value}}) is not a valid date.")]
	#[Assert\GreaterThanOrEqual("today", message: "The provided date ('{{ value }}') must not be before today.")]
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $date = null;

    /**
     * @var Collection<int, Participant>
     */
    #[ORM\OneToMany(targetEntity: Participant::class, mappedBy: 'event')]
    private Collection $participants;

    #[Assert\NotBlank(message: "The provided latitude cannot be empty.", allowNull: false)]
    #[Assert\Range(
        notInRangeMessage: "The provided latitude ({{ value }}) must be between {{ min }} and {{ max }}.",
		min: -90,
		max: 90
    )]
    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: 7)]
    private ?string $latitude = null;

    #[Assert\NotBlank(message: "The provided longitude cannot be empty.", allowNull: false)]
    #[Assert\Range(
		notInRangeMessage: "The provided longitude ({{ value }}) must be between {{ min }} and {{ max }}.",
		min: -180,
        max: 180,
    )]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7)]
    private ?string $longitude = null;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->setEvent($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): static
    {
        if ($this->participants->removeElement($participant)) {
            // set the owning side to null (unless already changed)
            if ($participant->getEvent() === $this) {
                $participant->setEvent(null);
            }
        }

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }
}
