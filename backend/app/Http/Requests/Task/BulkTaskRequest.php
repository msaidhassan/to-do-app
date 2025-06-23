<?php
namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class BulkTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'task_ids' => 'required|array|min:1',
            'task_ids.*' => 'required|integer|exists:tasks,id'
        ];
    }
}
