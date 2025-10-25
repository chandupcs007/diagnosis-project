<?php

namespace App\Services;

use App\Models\MenuItem;

class MenuService
{
    /**
     * Get all menu items with hierarchy
     */
    public function getMenuItems()
    {
        return MenuItem::with(['children' => function($query) {
            $query->active()->ordered();
        }])
        ->topLevel()
        ->active()
        ->ordered()
        ->get();
    }

    /**
     * Check if menu item is active
     */
    public function isActive($menuItem, $currentRoute)
    {
        if ($menuItem->route && $menuItem->route == $currentRoute) {
            return true;
        }

        if ($menuItem->children) {
            foreach ($menuItem->children as $child) {
                if ($child->route && $child->route == $currentRoute) {
                    return true;
                }
            }
        }

        return false;
    }
}