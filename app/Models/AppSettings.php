<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSettings extends Model
{
    use HasFactory;

    protected $table = 'app_settings';

    protected $fillable = [
        'app_name', 'app_url', 'app_logo', 'app_favicon', 'app_description', 'app_keywords', 'app_email', 'app_phone', 'app_address', 'google_map_link'
    ];
}
