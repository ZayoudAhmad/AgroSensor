<?php

namespace App\Service;

use App\Entity\SensorData;

class SensorDataService
{
    public function mapToJson(SensorData $sensorData): array
    {
        return [
            'id' => $sensorData->getId(),
            'sensor_id' => $sensorData->getSensor()->getId(),
            'nitrogen' => $sensorData->getNitrogen(),
            'phosphorus' => $sensorData->getPhosphorus(),
            'potassium' => $sensorData->getPotassium(),
            'temperature' => $sensorData->getTemperature(),
            'ph' => $sensorData->getPh(),
            'timestamp' => $sensorData->getTimestamp()->format('Y-m-d H:i:s')
        ];
    }
}
