<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Category extends Model
{
    protected $fillable = [
        'name', 'slug', 'image','status'
    ]; 
    use HasFactory;

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(CategoryTranslation::class);
    }
    
    // Helper method to get translated name
    public function getTranslatedName($locale)
    {
        return $this->translations
            ->where('locale', $locale)
            ->first()?->name ?? $this->name;
    }
}
