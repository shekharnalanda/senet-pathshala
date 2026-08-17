<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SchoolClass extends Model
{
    protected $table='school_classes';
    protected $fillable=['name','sections','sort_order','is_active'];
    protected $casts=['is_active'=>'boolean'];
    public function getSectionListAttribute(): array
    {
        return collect(explode(',',(string)$this->sections))->map(fn($v)=>trim($v))->filter()->values()->all();
    }
}
