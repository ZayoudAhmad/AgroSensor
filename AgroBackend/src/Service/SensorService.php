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
}
