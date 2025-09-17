<?php
use WHMCS\Module\Addon\AssetManager\Models\Asset;
use WHMCS\Module\Widget\AbstractWidget;

class AssetManagerWidget extends AbstractWidget
{
    protected $title = 'Asset Manager';
    protected $description = 'An overview of your assets.';
    protected $weight = 150;
    protected $columns = 1;
    protected $cache = false;
    protected $cacheExpiry = 120;
    protected $requiredPermission = '';

    public function getData()
    {
        $expiring_assets = Asset::whereNotNull('warranty_end_date')
            ->where('warranty_end_date', '>', date('Y-m-d'))
            ->where('warranty_end_date', '<=', date('Y-m-d', strtotime('+30 days')))
            ->count();

        return [
            'total_assets' => Asset::count(),
            'active_assets' => Asset::where('status', 'Active')->count(),
            'expiring_assets' => $expiring_assets,
        ];
    }

    public function generateOutput($data)
    {
        return <<<EOF
<div class="widget-content-padded">
    <div class="row">
        <div class="col-sm-4 text-center">
            <div class="stat-box">
                <div class="stat-label">Total Assets</div>
                <div class="stat-value">{$data['total_assets']}</div>
            </div>
        </div>
        <div class="col-sm-4 text-center">
            <div class="stat-box">
                <div class="stat-label">Active Assets</div>
                <div class="stat-value">{$data['active_assets']}</div>
            </div>
        </div>
        <div class="col-sm-4 text-center">
            <div class="stat-box">
                <div class="stat-label">Expiring Soon</div>
                <div class="stat-value">{$data['expiring_assets']}</div>
            </div>
        </div>
    </div>
</div>
EOF;
    }
}
