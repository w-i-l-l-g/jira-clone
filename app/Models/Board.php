<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Board extends Model
{
    use HasFactory;
    
    protected $fillable = ['project_id','name'];

    public function project(){ return $this->belongsTo(Project::class); }
    public function columns(){ return $this->hasMany(Column::class)->orderBy('position'); }
}