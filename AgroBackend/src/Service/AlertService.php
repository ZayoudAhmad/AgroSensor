<?php

namespace App\Service;

use App\Entity\Alert;

class AlertService
{
    public function mapToJson(Alert $alert): array
    {
        return [
            'id' => $alert->getId(),
            'message' => $alert->getMessage(),
            'severity' => $alert->getSeverity(),
            'timestamp' => $alert->getTimestamp()->format('Y-m-d H:i:s')
        ];
    }
}
