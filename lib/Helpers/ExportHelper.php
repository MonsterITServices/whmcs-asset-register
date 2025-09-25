<?php
namespace WHMCS\Module\Addon\AssetManager\Helpers;

use WHMCS\Module\Addon\AssetManager\Models\Asset;

class ExportHelper
{
    public static function exportToCsv()
    {
        $assets = Asset::with('client', 'type', 'customFields.customField')->get();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="assets.csv"');

        $output = fopen('php://output', 'w');

        // Get all possible custom field headers
        $customFieldHeaders = [];
        $assets->each(function($asset) use (&$customFieldHeaders) {
            $asset->customFields->each(function($field) use (&$customFieldHeaders) {
                $header = $field->customField->field_name;
                if (!in_array($header, $customFieldHeaders)) {
                    $customFieldHeaders[] = $header;
                }
            });
        });

        $headers = ['ID', 'Client Email', 'Asset Name', 'Asset Type', 'Serial Number', 'Purchase Date', 'Warranty End Date', 'Status'];
        fputcsv($output, array_merge($headers, $customFieldHeaders));

        foreach ($assets as $asset) {
            $row = [
                $asset->id,
                $asset->client->email,
                $asset->name,
                $asset->type->name,
                $asset->serial_number,
                $asset->purchase_date,
                $asset->warranty_end_date,
                $asset->status,
            ];

            $customFieldValues = [];
            foreach ($customFieldHeaders as $header) {
                $value = '';
                foreach ($asset->customFields as $customField) {
                    if ($customField->customField->field_name === $header) {
                        $value = $customField->field_value;
                        break;
                    }
                }
                $customFieldValues[] = $value;
            }

            fputcsv($output, array_merge($row, $customFieldValues));
        }

        fclose($output);
        exit;
    }
}
