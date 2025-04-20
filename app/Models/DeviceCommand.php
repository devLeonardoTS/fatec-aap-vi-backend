<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceCommand extends Model
{
  protected $fillable = [
    'device_id',
    'command',
    'executed_at',
    'execute_after',
  ];


  protected $casts = [
    'executed_at' => 'datetime',
    'execute_after' => 'datetime',
  ];

  public function scopeFilter($query, array $filters)
  {

    // Strict boolean and direct filters on `casts` or `fillable` fields
    foreach ($filters as $key => $value) {
      if (array_key_exists($key, $this->casts) && $this->casts[$key] === 'boolean') {
        // Boolean filters
        $query->where($key, filter_var($value, FILTER_VALIDATE_BOOLEAN));
      } elseif (in_array($key, $this->fillable)) {
        // Direct `LIKE` filters for fillable fields
        $query->where($key, 'like', '%' . $value . '%');
      }
    }


    // Filters for relationships or "non-fillable" fields

    // Filter by Mapping Type Name
    $query->when(isset($filters['type']), function ($query) use ($filters) {
      $query->whereHas('type', function ($query) use ($filters) {
        $query->where('name', 'like', '%' . $filters['type'] . '%');
      });
    });

    // Handle global search across multiple fields and relationships
    $query->when(isset($filters['global']), function ($query) use ($filters) {
      $global = $filters['global'];
      $query->where(function ($query) use ($global) {
        // Search in fillable fields
        foreach ($this->searchable as $field) {
          $query->orWhere($field, 'like', '%' . $global . '%');
        }

      });
    });

    return $query;
  }

  public function scopeFilterListType($query, array $filters)
  {
    if (!isset($filters['listing_type'])) {
      return $query;
    }

    switch ($filters['listing_type']) {
      // Add as needed.
      case "basic_unexecuted":
        return $query->whereNull('executed_at')->whereNull('execute_after');

      case "basic_executed":
        return $query->whereNotNull('executed_at')->whereNull('execute_after');

      case "scheduled_unexecuted":
        return $query->whereNull('executed_at')->whereNotNull('execute_after');

      case "scheduled_executed":
        return $query->whereNotNull('executed_at')->whereNotNull('execute_after');

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

  // Relationship with Device model
  public function device()
  {
    return $this->belongsTo(Device::class);
  }
}