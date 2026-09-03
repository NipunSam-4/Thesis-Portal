@props([
    'name' => 'recommendation',
    'model' => 'recommendation',
    'label' => 'Recommendation Status',
    'required' => true,
    'positiveValue' => '1',
    'positiveLabel' => '(a) RECOMMENDED',
    'positiveDesc' => 'Recommend the submission for forwarding to the next stage in the academic pipeline.',
    'negativeValue' => '0',
    'negativeLabel' => '(b) NOT RECOMMENDED',
    'negativeDesc' => 'Do not recommend the submission in its present form without further improvements.',
    'remarkName' => 'confidential_remark',
    'remarkModel' => 'confidentialRemark',
    'remarkValue' => '',
    'remarkLabelPositive' => 'Recommendation Remark (Optional)',
    'remarkLabelNegative' => 'Non-Recommendation Remark',
    'remarkPlaceholderPositive' => 'Optional recommendation remarks',
    'remarkPlaceholderNegative' => 'Provide mandatory non-recommendation remarks',
    'remarkRows' => 3,
    'formType' => null,
    'role' => null,
    'showSnippet' => true,
])

<div class="space-y-4">
    <!-- Item 1: Recommendation Status Radio Cards -->
    <div class="space-y-4">
        <label class="block font-bold text-gray-900 dark:text-white text-sm">
            {{ $label }}: @if($required)<span class="text-red-500">*</span>@endif
        </label>

        <div class="grid grid-cols-1 gap-4">
            <!-- (a) Recommended / Approved -->
            <label class="p-4 rounded-xl border-2 transition cursor-pointer flex items-start space-x-3" 
                   :class="{{ $model }} === '{{ $positiveValue }}' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'">
                <input type="radio" 
                       name="{{ $name }}" 
                       value="{{ $positiveValue }}" 
                       @if($required) required @endif
                       x-model="{{ $model }}" 
                       class="mt-1 text-emerald-600 focus:ring-emerald-500">
                <div>
                    <span class="block font-bold text-sm text-emerald-900 dark:text-emerald-300 uppercase tracking-wide">
                        {{ $positiveLabel }}
                    </span>
                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">
                        {{ $positiveDesc }}
                    </p>
                </div>
            </label>

            <!-- (b) Not Recommended / Rejected -->
            <label class="p-4 rounded-xl border-2 transition cursor-pointer flex items-start space-x-3" 
                   :class="{{ $model }} === '{{ $negativeValue }}' ? 'border-red-500 bg-red-50/50 dark:bg-red-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'">
                <input type="radio" 
                       name="{{ $name }}" 
                       value="{{ $negativeValue }}" 
                       @if($required) required @endif
                       x-model="{{ $model }}" 
                       class="mt-1 text-red-600 focus:ring-red-500">
                <div>
                    <span class="block font-bold text-sm text-red-900 dark:text-red-300 uppercase tracking-wide">
                        {{ $negativeLabel }}
                    </span>
                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">
                        {{ $negativeDesc }}
                    </p>
                </div>
            </label>
        </div>
    </div>

    <!-- Item 2: Dynamic Recommendation / Non-Recommendation Remark -->
    <div class="space-y-2 pt-2">
        <label class="block font-bold text-gray-900 dark:text-white text-sm">
            <span x-show="{{ $model }} === '{{ $positiveValue }}'">{{ $remarkLabelPositive }}</span>
            <span x-show="{{ $model }} === '{{ $negativeValue }}'">{{ $remarkLabelNegative }} <span class="text-red-500">*</span></span>
        </label>

        @if($showSnippet && $formType && $role)
            <div class="pt-0.5">
                <x-snippet-dropdown :target="$remarkModel" :form-type="$formType" :role="$role" />
            </div>
        @endif

        <textarea name="{{ $remarkName }}" 
                  rows="{{ $remarkRows }}" 
                  :required="{{ $model }} === '{{ $negativeValue }}'" 
                  x-model="{{ $remarkModel }}"
                  :placeholder="{{ $model }} === '{{ $positiveValue }}' ? '{{ addslashes($remarkPlaceholderPositive) }}' : '{{ addslashes($remarkPlaceholderNegative) }}'" 
                  class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 whitespace-pre-wrap">{{ trim($remarkValue) }}</textarea>
    </div>
</div>
