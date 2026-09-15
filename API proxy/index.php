<?php
header("Content-Type: application/json");

// Zmienne
$url = "http://127.0.0.1:8081/carelink/nohistory";
$data = [];
$response = @file_get_contents($url);

// Lista kluczy, które chcesz zachowaæ wraz z podkluczami
$keysToKeep = [
	# Data aktualizacji serwera
	"lastMedicalDeviceDataUpdateServerTime",
	# Cukier
	"lastSG" => ["sg", "timestamp"],
    "timeInRange",
	"averageSG",
	# Pompa
    "medicalDeviceInformation" => ["manufacturer", "modelNumber"],
    "pumpCommunicationState",
    "pumpBatteryLevelPercent",
    "reservoirRemainingUnits",
    "reservoirAmount",
	# Sensor
	"gstCommunicationState",
	"gstBatteryLevel",
	"sensorDurationMinutes",
	"sensorDurationHours",
	"sensorState"
];

// Funkcja do filtrowania danych
function filterData($data, $keysToKeep) {
    $filteredData = [];
    foreach ($keysToKeep as $key => $subKeys) {
        if (is_int($key)) {
            $key = $subKeys;
            $subKeys = null;
        }
        if (isset($data[$key])) {
            if (is_array($subKeys)) {
                $filteredData[$key] = [];
                foreach ($subKeys as $subKey) {
                    if (isset($data[$key][$subKey])) {
                        $filteredData[$key][$subKey] = $data[$key][$subKey];
                    }
                }
            } else {
                $filteredData[$key] = $data[$key];
            }
        }
    }
    return $filteredData;
}
	
// Analiza odpowiedzi i zwrócenie statusu z danymi
if ($response === FALSE) {
    $data["status"] = "error";
    $data["values"]["problem"] = "Error fetching data from API";
} else {
    $json = json_decode($response, true);
    $data["status"] = "ok";
    
    // Filtrowanie danych
    $data["values"] = filterData($json, $keysToKeep);
}

echo json_encode($data);
