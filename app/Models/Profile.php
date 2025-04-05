<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
  protected $fillable = [
    'full_name',
    'user_id',
  ];

  public function setAttribute($key, $value)
  {
    // Check if the attribute is cast as a boolean
    if (array_key_exists($key, $this->casts) && $this->casts[$key] === 'boolean') {
      $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    // Call the parent method to set the value
    return parent::setAttribute($key, $value);
  }


  // Add any relationships or additional methods here
  public function user()
  {
    return $this->belongsTo(User::class);
  }

}