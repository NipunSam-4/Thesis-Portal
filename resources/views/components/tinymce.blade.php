@props([
    'name',
    'id' => null,
    'value' => '',
    'placeholder' => 'Type your comments here...',
    'height' => 350
])

@php
    $elementId = $id ?? 'tinymce_' . Str::random(8);
@endphp

<div x-data="{
        editor: null,
        initEditor() {
            if (typeof tinymce === 'undefined') {
                console.error('TinyMCE script is not loaded');
                return;
            }

            // Prevent duplicate initialization
            if (tinymce.get('{{ $elementId }}')) {
                tinymce.get('{{ $elementId }}').remove();
            }

            const isDarkMode = document.documentElement.classList.contains('dark');

            tinymce.init({
                selector: '#{{ $elementId }}',
                height: {{ $height }},
                menubar: true,
                plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons accordion',
                toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen preview print | image media link codesample table | code',
                quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
                toolbar_mode: 'sliding',
                skin: isDarkMode ? 'oxide-dark' : 'oxide',
                content_css: isDarkMode ? 'dark' : 'default',
                placeholder: '{{ $placeholder }}',
                branding: false,
                promotion: false,
                setup: (editor) => {
                    this.editor = editor;
                    editor.on('init', () => {
                        if (isDarkMode) {
                            editor.getContainer().style.backgroundColor = '#1f2937';
                        }
                    });
                    editor.on('change keyup NodeChange Undo Redo', () => {
                        editor.save();
                        const textarea = document.getElementById('{{ $elementId }}');
                        if (textarea) {
                            textarea.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    });
                }
            });
        }
     }"
     x-init="initEditor()"
     class="w-full">
    <textarea name="{{ $name }}"
              id="{{ $elementId }}"
              class="hidden w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500 p-3">{{ old($name, $value) }}</textarea>
</div>
