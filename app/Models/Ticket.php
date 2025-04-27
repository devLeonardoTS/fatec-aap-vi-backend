<?php

namespace App\Models;

use App\Constants\UserRoles;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{

  protected $fillable = [
    'title',
    'status',
    'description',
    'user_id',
  ];

  protected $touches = ['comments'];

  protected $searchable = ['title', 'description', 'status'];

  public function scopeFilter($query, array $filters)
  {

    // Role based filtering
    if (auth()?->user()?->role === UserRoles::ADMIN) {
      // Show all devices for admin users
    } else {
      // Show only devices that belong to the user
      $query->where('user_id', auth()?->id());
    }

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
    // $query->when(isset($filters['type']), function ($query) use ($filters) {
    //   $query->whereHas('type', function ($query) use ($filters) {
    //     $query->where('name', 'like', '%' . $filters['type'] . '%');
    //   });
    // });

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

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Get all of the ticket's messages.
   */
  public function comments()
  {
    return $this->morphMany(Comment::class, 'commentable');
  }
}