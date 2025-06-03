<?php

namespace App\Http\Controllers\RolesAndPermissions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RolesAndPermissionController extends Controller
{
    public function rolesAndPermission()
    {
        return Inertia::render('MyMARZ/Admin/rolesAndPermission');
    }
}
