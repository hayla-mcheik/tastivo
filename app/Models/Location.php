<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
protected $table='restaurant_locations';
    protected $fillable = [
        'name',
        'image',
        'address',
        'opening_hours',
        'phone',
        'email',
        'delivery_available',
        'delivery_areas',
        'is_open'
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'delivery_available' => 'boolean',
        'is_open' => 'boolean'
    ];

    // Helper method to check if location is currently open
    public function getIsOpenAttribute()
    {
        // You can implement more complex logic here based on opening hours
        return $this->attributes['is_open'];
    }
}