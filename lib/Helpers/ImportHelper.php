<?php
namespace WHMCS\Module\Addon\AssetManager\Helpers;

use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\AssetManager\Models\Asset;
use WHMCS\Module\Addon\AssetManager\Models\AssetType;
use WHMCS\User\Client;

class ImportHelper
{
    public static function importFromCsv($filepath)
    {
        $successCount = 0;
        $handle = fopen($filepath, 'r');

        if ($handle === false) {
            throw new \Exception("Failed to open uploaded file.");
        }

        // Get header row
        $headers = fgetcsv($handle);
        if ($headers === false) {
            throw new \Exception("Cannot read from CSV file.");
        }

        $requiredHeaders = ['Client Email', 'Asset Name', 'Asset Type', 'Serial Number', 'Status'];
        foreach ($requiredHeaders as $required) {
            if (!in_array($required, $headers)) {
                throw new \Exception("CSV file is missing required header: {$required}");
            }
        }

        while (($data = fgetcsv($handle)) !== false) {
            $rowData = array_combine($headers, $data);

            $client = Client::where('email', $rowData['Client Email'])->first();
            if (!$client) {
                continue; // Or log an error
            }

            $assetType = AssetType::firstOrCreate(['name' => $rowData['Asset Type']]);

            Asset::create([
                'userid' => $client->id,
                'asset_type_id' => $assetType->id,
                'name' => $rowData['Asset Name'],
                'serial_number' => $rowData['Serial Number'],
                'status' => $rowData['Status'],
                'purchase_date' => !empty($rowData['Purchase Date']) ? date('Y-m-d', strtotime($rowData['Purchase Date'])) : null,
                'warranty_end_date' => !empty($rowData['Warranty End Date']) ? date('Y-m-d', strtotime($rowData['Warranty End Date'])) : null,
                'description' => $rowData['Description'] ?? null,
            ]);

            $successCount++;
        }

        fclose($handle);
        return $successCount;
    }
}
