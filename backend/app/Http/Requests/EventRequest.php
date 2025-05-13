<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->isMethod('PUT')) {
            return $this->updateRules();
        }

        return $this->createRules();
    }

    protected function createRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
            'venue' => 'required|string|max:255',
            'price' => 'required|numeric|min:10|max:10000',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'sometimes|string',
            'featured' => 'sometimes|boolean',
        ];
    }

    protected function updateRules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string|max:255',
            'date' => 'sometimes|required|date:after:today',
            'venue' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric',
            'image' => 'sometimes|required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'sometimes|required|exists:categories,id',
            'tags' => 'sometimes|string',
            'featured' => 'sometimes|boolean',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if ($this->has('tags') && is_string(request()->tags)) {
            try {
                $decodedTags = json_decode(request()->tags, true);
                if (is_array($decodedTags)) {
                    $this->merge([
                        'tags_array' => $decodedTags,
                    ]);
                }
            } catch (\Exception $e) {
                // If JSON decoding fails, we'll handle it in the controller
            }
        }
    }
}
