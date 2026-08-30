@props([
    'name',
    'id' => null,
    'value' => '',
    'placeholder' => 'Type your comments here...',
    'height' => 350,
    'uploadUrl' => null,
])

@php
    $elementId = $id ?? 'tinymce_' . Str::random(8);
@endphp

<div class="w-full">
    <textarea 
        id="{{ $elementId }}" 
        name="{{ $name }}" 
        class="hidden w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
        placeholder="{{ $placeholder }}">{{ old($name, $value) }}</textarea>
</div>

<script>
(function() {
    function initEditor() {
        if (typeof tinymce === 'undefined') {
            setTimeout(initEditor, 100);
            return;
        }

        if (tinymce.get('{{ $elementId }}')) {
            tinymce.get('{{ $elementId }}').remove();
        }

        const isDarkMode = document.documentElement.classList.contains('dark') || window.matchMedia('(prefers-color-scheme: dark)').matches;

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
            placeholder: @json($placeholder),
            branding: false,
            promotion: false,
            relative_urls: false,
            remove_script_host: true,
            convert_urls: false,
            @if($uploadUrl)
            file_picker_types: 'image',
            file_picker_callback: function (callback, value, meta) {
                if (meta.filetype === 'image') {
                    const input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/jpeg,image/png,image/jpg,image/gif,image/webp');
                    input.setAttribute('multiple', 'multiple');

                    input.onchange = async function () {
                        const files = Array.from(this.files);
                        if (!files.length) return;

                        const maxBytes = 10 * 1024 * 1024;
                        let totalBytes = files.reduce(function(acc, f) { return acc + f.size; }, 0);

                        if (totalBytes > maxBytes) {
                            alert('Total size of selected images (' + (totalBytes / (1024 * 1024)).toFixed(1) + ' MB) exceeds 10 MB limit.');
                            return;
                        }

                        const uploadFile = function(file) {
                            return new Promise(function(resolve, reject) {
                                const xhr = new XMLHttpRequest();
                                xhr.open('POST', '{{ $uploadUrl }}');
                                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                                xhr.onload = function() {
                                    if (xhr.status >= 200 && xhr.status < 300) {
                                        const json = JSON.parse(xhr.responseText);
                                        if (json && json.location) resolve(json.location);
                                        else reject('Invalid server response');
                                    } else {
                                        reject('Upload failed with status ' + xhr.status);
                                    }
                                };
                                xhr.onerror = function() { reject('Network error'); };
                                const fd = new FormData();
                                fd.append('file', file, file.name);
                                xhr.send(fd);
                            });
                        };

                        try {
                            const urls = [];
                            for (const file of files) {
                                const loc = await uploadFile(file);
                                urls.push({ url: loc, name: file.name });
                            }

                            if (urls.length > 0) {
                                callback(urls[0].url, { alt: urls[0].name });

                                if (urls.length > 1 && window.tinymce && window.tinymce.activeEditor) {
                                    let extraHtml = ' ';
                                    for (let i = 1; i < urls.length; i++) {
                                        extraHtml += '<img src="' + urls[i].url + '" alt="' + urls[i].name + '" /> ';
                                    }
                                    window.tinymce.activeEditor.insertContent(extraHtml);
                                }
                            }
                        } catch (err) {
                            alert('Error uploading images: ' + err);
                        }
                    };
                    input.click();
                }
            },
            images_upload_handler: function (blobInfo, progress) {
                return new Promise(function(resolve, reject) {
                    const maxTotalBytes = 10 * 1024 * 1024;
                    const currentFileSize = blobInfo.blob().size;

                    if (currentFileSize > maxTotalBytes) {
                        reject({ message: 'Image size (' + (currentFileSize / (1024 * 1024)).toFixed(1) + ' MB) exceeds 10 MB limit.', remove: true });
                        return;
                    }

                    const xhr = new XMLHttpRequest();
                    xhr.withCredentials = false;
                    xhr.open('POST', '{{ $uploadUrl }}');
                    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                    xhr.upload.onprogress = function(e) {
                        progress(e.loaded / e.total * 100);
                    };

                    xhr.onload = function() {
                        if (xhr.status === 403 || xhr.status === 401) {
                            reject({ message: 'HTTP Error: ' + xhr.status + ' (Unauthorized)', remove: true });
                            return;
                        }

                        if (xhr.status < 200 || xhr.status >= 300) {
                            reject('HTTP Error: ' + xhr.status);
                            return;
                        }

                        const json = JSON.parse(xhr.responseText);
                        if (!json || typeof json.location !== 'string') {
                            reject('Invalid JSON response');
                            return;
                        }

                        resolve(json.location);
                    };

                    xhr.onerror = function() {
                        reject('Image upload failed due to a network error.');
                    };

                    const formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());

                    xhr.send(formData);
                });
            },
            @endif
            setup: function(editor) {
                editor.on('init', function() {
                    if (isDarkMode) {
                        editor.getContainer().style.backgroundColor = '#1f2937';
                        editor.getContainer().style.borderColor = '#374151';
                    }
                });
                editor.on('change keyup NodeChange Undo Redo', function() {
                    editor.save();
                    const textarea = document.getElementById('{{ $elementId }}');
                    if (textarea) {
                        textarea.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                });
            }
        });
    }

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        setTimeout(initEditor, 50);
    } else {
        document.addEventListener('DOMContentLoaded', initEditor);
    }
})();
</script>
