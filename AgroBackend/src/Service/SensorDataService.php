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
            'timestamp' => $sensorData->getTimestamp()->format('Y-m-d H:i:s'),
            'humidity' => $sensorData->getHumidity(),
            'rainfall' => $sensorData->getRainfall(),
        ];
    }

    public function getSensorDataForTimestamp(Sensor $sensor, \DateTime $timestamp): ?array
    {
        $sensorData = $this->sensorDataRepository->findOneBy(
            ['sensor' => $sensor, 'timestamp' => $timestamp]
        );

        if (!$sensorData) {
            return null;
        }

        return [
            'nitrogen' => $sensorData->getNitrogen(),
            'phosphorous' => $sensorData->getPhosphorous(),
            'potassium' => $sensorData->getPotassium(),
            'temperature' => $sensorData->getTemperature(),
            'humidity' => $sensorData->getHumidity(),
            'ph' => $sensorData->getPh(),
            'rainfall' => $sensorData->getRainfall(),
        ];
    }                                                                   

}
