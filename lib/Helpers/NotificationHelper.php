<?php
namespace WHMCS\Module\Addon\AssetManager\Helpers;

class NotificationHelper
{
    public static function success($message)
    {
        return '<div class="alert alert-success">' . $message . '</div>';
    }

    public static function error($message)
    {
        return '<div class="alert alert-danger">' . $message . '</div>';
    }

    public static function info($message)
    {
        return '<div class="alert alert-info">' . $message . '</div>';
    }

    public static function warning($message)
    {
        return '<div class="alert alert-warning">' . $message . '</div>';
    }
}
