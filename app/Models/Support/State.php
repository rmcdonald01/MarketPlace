<?php
namespace App\Models\Support;

use Illuminate\Database\Eloquent\Model;
use App\Models\Support\StateStatusNote;

class State extends Model{

  protected $table = 'states_and_abbreviation';

  public function status()
  {
    return $this->hasMany(StateStatusNote::class);
  }


}
