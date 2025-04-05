<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingLead extends Model
{
  protected $fillable = [
    'email',
    'is_active',
    'last_email_sent_at',
  ];

  protected $casts = [
    'is_active' => 'boolean',
    'last_email_sent_at' => 'datetime',
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

  public function scopeFilterListType($query, array $filters)
  {
    if (!isset($filters['listing_type'])) {
      return $query;
    }

    switch ($filters['listing_type']) {
      // Add as needed.

      default:
        // Handle unknown listing_type if necessary
        break;
    }

    return $query;
  }

  public function scopeSortBy($query, string $direction, array $sortBy)
  {
    foreach ($sortBy as $key) {
      if (in_array($key, array_merge($this->fillable, ['created_at', 'updated_at'])) && in_array(strtolower($direction), ['asc', 'desc'])) {
        $query->orderBy($key, $direction);
      }
    }
    return $query;
  }

  // Add any relationships or additional methods here
}