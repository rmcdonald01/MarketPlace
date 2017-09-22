<?php

namespace App\Models\Support;

use Illuminate\Database\Eloquent\Model;
use App\Models\Support\State;


class StateStatusNote extends Model
{
    protected $table = 'state_status_notes';

    protected $guard = false;

    protected $fillable = [
      'state_id',
      'status',
      'note',
      'entered_by'
    ];

    public function state()
    {
      $this->belongsTo(State::class);
    }
}
