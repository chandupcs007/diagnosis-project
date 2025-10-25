<?php

if (!function_exists('system_name')) {
    function system_name() {
        return config('diagnosis.name', 'Diagnosis Pro');
    }
}

if (!function_exists('system_version')) {
    function system_version() {
        return config('diagnosis.version', '1.0.0');
    }
}

if (!function_exists('system_description')) {
    function system_description() {
        return config('diagnosis.description', 'Medical Diagnosis Platform');
    }
}
