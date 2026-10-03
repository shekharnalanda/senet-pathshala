<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MciFeeInvoice extends Model
{
    protected $guarded = ['id'];
    protected function casts(): array { return ['amount_paise' => 'integer']; }
}
