@props([
    'name' => 'student_comment',
    'model' => 'studentComment',
    'value' => '',
    'label' => 'Student Comment (Optional)',
    'placeholder' => 'Provide optional comments or observations for the student',
    'rows' => 3,
    'formType' => null,
    'role' => null,
    'showSnippet' => false,
])

<div class="space-y-2 pt-2">
    <label class="block font-bold text-gray-900 dark:text-white text-sm">
        {{ $label }}
    </label>

    @if($showSnippet && $formType && $role)
        <div class="pt-0.5">
            <x-snippet-dropdown :target="$model" :form-type="$formType" :role="$role" />
        </div>
    @endif

    <textarea name="{{ $name }}" 
              rows="{{ $rows }}" 
              x-model="{{ $model }}" 
              placeholder="{{ $placeholder }}" 
              class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 whitespace-pre-wrap">{{ trim($value) }}</textarea>
</div>
