<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $guarded = [];
    public function reporter() { return $this->belongsTo(User::class, 'reporter_id'); }
    public function handledBy() { return $this->belongsTo(User::class, 'handled_by'); }
    public function reportable() { return $this->morphTo(); }
}
