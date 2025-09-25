<?php
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;

class AssetManagerActivation
{
    public static function activate()
    {
        try {
            if (!Capsule::schema()->hasTable('mod_asset_manager_types')) {
                Capsule::schema()->create('mod_asset_manager_types', function ($table) {
                    $table->increments('id');
                    $table->string('name');
                });
                // Insert default data
                Capsule::table('mod_asset_manager_types')->insert([['name' => 'Server'], ['name' => 'Desktop'], ['name' => 'Laptop'], ['name' => 'Phone']]);
            }

            if (!Capsule::schema()->hasTable('mod_asset_manager_assets')) {
                Capsule::schema()->create('mod_asset_manager_assets', function ($table) {
                    $table->increments('id');
                    $table->integer('userid');
                    $table->integer('asset_type_id');
                    $table->string('name');
                    $table->text('description')->nullable();
                    $table->string('serial_number')->nullable();
                    $table->string('product_number')->nullable();
                    $table->string('mac_address')->nullable();
                    $table->string('ip_address_type')->nullable();
                    $table->string('ip_address')->nullable();
                    $table->string('subnet_mask')->nullable();
                    $table->string('gateway')->nullable();
                    $table->string('dns1')->nullable();
                    $table->string('dns2')->nullable();
                    $table->text('admin_notes')->nullable();
                    $table->date('purchase_date')->nullable();
                    $table->date('warranty_end_date')->nullable();
                    $table->string('status');
                    $table->timestamps();
                    $table->index('userid');
                    $table->index('asset_type_id');
                });
            }

            if (!Capsule::schema()->hasTable('mod_asset_manager_custom_fields')) {
                Capsule::schema()->create('mod_asset_manager_custom_fields', function ($table) {
                    $table->increments('id');
                    $table->integer('asset_type_id');
                    $table->string('field_name');
                    $table->string('field_type');
                    $table->text('field_options')->nullable();
                    $table->index('asset_type_id');
                });
            }

            if (!Capsule::schema()->hasTable('mod_asset_manager_custom_field_values')) {
                Capsule::schema()->create('mod_asset_manager_custom_field_values', function ($table) {
                    $table->increments('id');
                    $table->integer('asset_id');
                    $table->integer('custom_field_id');
                    $table->text('field_value')->nullable();
                    $table->index('asset_id');
                    $table->index('custom_field_id');
                });
            }

            if (!Capsule::schema()->hasTable('mod_asset_manager_ticket_links')) {
                Capsule::schema()->create('mod_asset_manager_ticket_links', function ($table) {
                    $table->increments('id');
                    $table->integer('ticket_id');
                    $table->integer('asset_id');
                    $table->index('ticket_id');
                    $table->index('asset_id');
                });
            }

            if (!Capsule::schema()->hasColumn('mod_asset_manager_assets', 'contact_id')) {
                Capsule::schema()->table('mod_asset_manager_assets', function ($table) {
                    $table->integer('contact_id')->nullable()->after('userid');
                });
            }

            return ['status' => 'success', 'description' => 'Asset Manager module activated successfully.'];
        } catch (Exception $e) {
            return ['status' => 'error', 'description' => 'Error activating Asset Manager module: ' . $e->getMessage()];
        }
    }

    public static function deactivate()
    {
        // Check for the setting to determine if we should delete data.
        $deleteOnDeactivate = Capsule::table('tbladdonmodules')
            ->where('module', 'asset_manager')
            ->where('setting', 'delete_on_deactivate')
            ->value('value');

        // Only drop tables if the setting is explicitly 'on'.
        if ($deleteOnDeactivate === 'on') {
            try {
                Capsule::schema()->dropIfExists('mod_asset_manager_assets');
                Capsule::schema()->dropIfExists('mod_asset_manager_types');
                Capsule::schema()->dropIfExists('mod_asset_manager_custom_fields');
                Capsule::schema()->dropIfExists('mod_asset_manager_custom_field_values');
                Capsule::schema()->dropIfExists('mod_asset_manager_ticket_links');

                // Also remove the setting itself after cleanup
                Capsule::table('tbladdonmodules')
                    ->where('module', 'asset_manager')
                    ->where('setting', 'delete_on_deactivate')
                    ->delete();

                return ['status' => 'success', 'description' => 'Asset Manager module deactivated and all data has been removed.'];
            } catch (Exception $e) {
                return ['status' => 'error', 'description' => 'Error during deactivation cleanup: ' . $e->getMessage()];
            }
        }

        // If the setting is not 'on', just deactivate without deleting data.
        return ['status' => 'success', 'description' => 'Asset Manager module deactivated successfully. Data has been preserved.'];
    }
}