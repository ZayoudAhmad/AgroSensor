<?php

namespace App\Service;

use App\Entity\Sensor;

class SensorService
{
    public function mapToJson(Sensor $sensor): array
    {
        return [
            'id' => $sensor->getId(),
            'type' => $sensor->getType(),
            'latitude' => $sensor->getLatitude(),
            'longitude' => $sensor->getLongitude()
        ];
    }

    public function getSensorsByType(string $type): array
    {
        if (!in_array($type, [Sensor::TYPE_SOIL, Sensor::TYPE_PLANT])) {
            throw new \InvalidArgumentException('Invalid sensor type');
        }

        return $this->sensorRepository->findBy(['type' => $type]);
    }

}
