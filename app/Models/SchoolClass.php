<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class SchoolClass extends Model
{
    protected $table='school_classes';
    protected $fillable=['branch_id','name','sections','sort_order','is_active'];
    protected $casts=['is_active'=>'boolean'];
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
    public function getSectionListAttribute(): array
    {
        return collect(explode(',',(string)$this->sections))->map(fn($v)=>trim($v))->filter()->values()->all();
    }
}
