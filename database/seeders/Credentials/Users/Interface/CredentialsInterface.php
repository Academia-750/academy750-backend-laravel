<?php

namespace Database\Seeders\Credentials\Users\Interface;

use App\Models\User;

interface CredentialsInterface
{
    public static function AdminCredentials (): \Illuminate\Database\Eloquent\Model|\LaravelIdea\Helper\App\Models\_IH_User_QB|\Illuminate\Database\Eloquent\Builder|User;
    public static function StudentCredentials (): \Illuminate\Database\Eloquent\Model|\LaravelIdea\Helper\App\Models\_IH_User_QB|\Illuminate\Database\Eloquent\Builder|User;
}
