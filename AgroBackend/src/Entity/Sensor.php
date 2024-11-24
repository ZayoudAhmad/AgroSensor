<?php

namespace App\Entity;

use App\Repository\SensorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SensorRepository::class)]
class Sensor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    // Constants for sensor types
    public const TYPE_SOIL = 'SOIL_SENSOR';
    public const TYPE_PLANT = 'PLANT_SENSOR';

    #[ORM\Column]
    private ?float $latitude = null;

    #[ORM\Column]
    private ?float $longitude = null;

    /**
     * @var Collection<int, SensorData>
     */
    #[ORM\OneToMany(targetEntity: SensorData::class, mappedBy: 'sensor')]
    private Collection $sensorData;

    /**
     * @var Collection<int, Alert>
     */
    #[ORM\OneToMany(targetEntity: Alert::class, mappedBy: 'sensor')]
    private Collection $alerts;

    public function __construct()
    {
        $this->sensorData = new ArrayCollection();
        $this->alerts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return Collection<int, SensorData>
     */
    public function getSensorData(): Collection
    {
        return $this->sensorData;
    }

    public function addSensorData(SensorData $sensorData): static
    {
        if (!$this->sensorData->contains($sensorData)) {
            $this->sensorData->add($sensorData);
            $sensorData->setSensor($this);
        }

        return $this;
    }

    public function removeSensorData(SensorData $sensorData): static
    {
        if ($this->sensorData->removeElement($sensorData)) {
            // set the owning side to null (unless already changed)
            if ($sensorData->getSensor() === $this) {
                $sensorData->setSensor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Alert>
     */
    public function getAlerts(): Collection
    {
        return $this->alerts;
    }

    public function addAlert(Alert $alert): static
    {
        if (!$this->alerts->contains($alert)) {
            $this->alerts->add($alert);
            $alert->setSensor($this);
        }

        return $this;
    }

    public function removeAlert(Alert $alert): static
    {
        if ($this->alerts->removeElement($alert)) {
            // set the owning side to null (unless already changed)
            if ($alert->getSensor() === $this) {
                $alert->setSensor(null);
            }
        }

        return $this;
    }
}
