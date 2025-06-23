<?php
namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreTaskRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        if ($this->due_date) {
            $this->merge([
                'due_date' => Carbon::parse($this->due_date)->format('Y-m-d H:i:s'),
                'status' => $this->status ?? 'pending'
            ]);
        } else {
            $this->merge([
                'status' => $this->status ?? 'pending'
            ]);
        }
    }
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,in_progress,completed',
            'due_date' => 'nullable|date|after:now',
            'category_id' => 'required|exists:categories,id',
        ];
    }
    public function messages()
    {
        return [
            'due_date.after' => 'The due date must be a date and time in the future.',
        ];
    }
}

