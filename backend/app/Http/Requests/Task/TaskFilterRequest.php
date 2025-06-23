<?php
namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class TaskFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'nullable|in:pending,overdue,in_progress,completed',
            'category_id' => 'nullable|exists:categories,id',
            'search' => 'nullable|string|min:1', // Add search parameter
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'sort_by' => 'nullable|in:title,status,due_date,created_at',
            'sort_order' => 'nullable|in:asc,desc',

        ];
    }
}
