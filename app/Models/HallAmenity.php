<?php

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HallAmenity extends Model
{
    use HasFactory;

    protected $guarded = [];

    // هر امکانات متعلق به یک سالن است
    public function hall()
    {
        return $this->belongsTo(Hall::class, 'halls_id');
    }
}