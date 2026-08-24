<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'superadmin';
    case AdminManager = 'admin_manager';
    case Member = 'member';
}
