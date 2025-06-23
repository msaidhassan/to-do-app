<?php

namespace App\Models;
use DateTimeInterface;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'status',
        'due_date',
    ];


    public static array $statuses = ['pending', 'in_progress', 'completed', 'overdue'];

    // protected function casts(): array
    // {
    //     return [
    //         'due_date' => 'date'
    //     ];
    // }

/**
 * Prepare a date for array / JSON serialization.
 */
protected function serializeDate(DateTimeInterface $date): string
{
    return $date->format('Y-m-d H:i:s');
}

    protected static function booted()
    {
        static::addGlobalScope('default_sort', function (Builder $builder) {
            $builder->orderByRaw("CASE
                WHEN status = 'pending' AND due_date IS NOT NULL AND due_date < CURRENT_DATE THEN 1
                WHEN status = 'pending' AND due_date IS NOT NULL THEN 2
                WHEN status = 'in_progress' THEN 3
                WHEN status = 'completed' THEN 4
                ELSE 5
            END")
            ->orderBy('due_date', 'asc')
            ->orderBy('created_at', 'desc');
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope for user's tasks
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for tasks by status
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for tasks by category
     */
    public function scopeByCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope for tasks due between two dates
     */
    public function scopeDueBetween(Builder $query, ?string $startDate, ?string $endDate): Builder
    {
        if ($startDate && $endDate) {
            return $query->whereBetween('due_date', [$startDate, $endDate]);
        }

        if ($startDate) {
            return $query->where('due_date', '>=', $startDate);
        }

        if ($endDate) {
            return $query->where('due_date', '<=', $endDate);
        }

        return $query;
    }

    /**
     * Scope for searching tasks
     */
    public function scopeSearch(Builder $query, string $searchTerm): Builder
    {
    // Clean and prepare search term
    $searchTerm = trim($searchTerm);

    // If empty search term, return original query
    if (empty($searchTerm)) {
        return $query;
    }

    // Create search pattern with exact spacing
    $exactPattern = '%' . $searchTerm . '%';

    return $query->where(function($q) use ($exactPattern) {
        // Search with original spacing only
        $q->where('title', 'LIKE', $exactPattern)
          ->orWhere('description', 'LIKE', $exactPattern);
    });
}

    /**
     * Scope for marking overdue tasks
     */
    public function scopeMarkOverdue($query)
    {
        return $query->where('due_date', '<', now())
                     ->whereIn('status', ['pending', 'in_progress']);
    }
}
