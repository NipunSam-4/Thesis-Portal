<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Submit PTS-2 PhD Synopsis Report') }}
            </h2>
            <a href="{{ route('student.dashboard') }}" class="px-4 py-1.5 bg-gray-600 hover:bg-gray-700 text-white font-bold text-xs rounded-lg shadow transition">
                &larr; Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        selectedFileName: '{{ $pts2 ? basename($pts2->synopsis_report_doc_path) : '' }}',
        selectedFileSize: '',
        filePreviewUrl: '{{ $pts2 ? route('pts.document.serve', ['pts2', $pts2->id, 'synopsis_report_doc_path']) : '' }}',
        fileError: '',

        handleFileSelect(event) {
            const file = event.target.files[0];
            this.fileError = '';
            if (!file) return;

            // File size validation: 10 MB limit (10240 KB)
            if (file.size > 10240 * 1024) {
                this.fileError = 'File size exceeds the 10 MB limit (' + (file.size / (1024 * 1024)).toFixed(2) + ' MB). Please choose a smaller file.';
                event.target.value = '';
                return;
            }

            this.selectedFileName = file.name;
            this.selectedFileSize = (file.size / 1024).toFixed(1) + ' KB';
            this.filePreviewUrl = URL.createObjectURL(file);
        },

        triggerFileInput() {
            document.getElementById('synopsis_report_doc_input').click();
        },

        deleteFile() {
            this.selectedFileName = '';
            this.selectedFileSize = '';
            this.filePreviewUrl = '';
            this.fileError = '';
            const input = document.getElementById('synopsis_report_doc_input');
            if (input) input.value = '';
        }
    }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Reversion Feedback Banner -->
            @if($pts2 && $pts2->status === 'reverted')
                <div class="p-5 bg-amber-50 dark:bg-amber-900/40 border-l-4 border-amber-500 rounded-xl shadow-sm space-y-2">
                    <div class="flex items-center text-amber-800 dark:text-amber-200 font-bold text-base">
                        <span class="mr-2 text-lg">⚠️</span> PTS-2 Form Reverted by {{ $pts2->getRevertedByRoleLabel() }}
                    </div>
                    <p class="text-xs text-amber-700 dark:text-amber-300">
                        <strong>Reason / Comments:</strong> "{{ $pts2->getReversionComment() ?: 'Please modify the synopsis report and resubmit.' }}"
                    </p>
                </div>
            @endif

            <!-- Form Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                
                <!-- Read-Only Student Profile Header -->
                <div class="p-4 bg-gray-50 dark:bg-gray-700/40 rounded-xl border border-gray-200 dark:border-gray-600 grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                    <div>
                        <span class="text-gray-500 block">Student Name</span>
                        <strong class="text-gray-900 dark:text-white font-bold text-sm">{{ $student->user->name }}</strong>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Roll Number</span>
                        <strong class="text-gray-900 dark:text-white font-mono text-sm">{{ $student->roll_number }}</strong>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Department</span>
                        <strong class="text-gray-900 dark:text-white text-sm">{{ $student->department->name ?? 'N/A' }}</strong>
                    </div>
                </div>

                <form action="{{ route('student.pts2.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf


                    <!-- Additional Remarks -->
                    <div>
                        <label for="remarks" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            Additional Remarks / Comments (Optional)
                        </label>
                        <textarea name="remarks" id="remarks" rows="3"
                            placeholder="Any additional observations or information for the evaluation committee..."
                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">{{ old('remarks', $pts2->remarks ?? '') }}</textarea>
                    </div>

                    <!-- Synopsis Report Document Upload Card -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">
                            Upload Synopsis Report Document (PDF/DOCX, Max 10 MB) <span class="text-red-500">*</span>
                        </label>

                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center bg-gray-50/50 dark:bg-gray-800/50 space-y-4">
                            <input type="file" name="synopsis_report_doc" id="synopsis_report_doc_input" class="hidden"
                                accept=".pdf,.doc,.docx" @change="handleFileSelect($event)">

                            <div x-show="!selectedFileName" class="space-y-2">
                                <svg class="w-12 h-12 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                <button type="button" @click="triggerFileInput()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow transition">
                                    Select Synopsis File
                                </button>
                                <p class="text-xs text-gray-500">Allowed formats: PDF, DOC, DOCX (Limit: 10 MB)</p>
                            </div>

                            <!-- Selected File Card -->
                            <div x-show="selectedFileName" x-cloak class="p-4 bg-white dark:bg-gray-700 rounded-xl border border-indigo-200 dark:border-indigo-800 flex flex-col md:flex-row items-center justify-between gap-4 text-left">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-lg text-indigo-600 dark:text-indigo-300">
                                        📄
                                    </div>
                                    <div>
                                        <div class="font-bold text-xs text-gray-900 dark:text-white" x-text="selectedFileName"></div>
                                        <div class="text-[10px] text-gray-500" x-text="selectedFileSize"></div>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <a :href="filePreviewUrl" target="_blank" x-show="filePreviewUrl" class="px-3 py-1.5 bg-blue-100 text-blue-800 text-xs font-bold rounded-lg hover:bg-blue-200">
                                        📄 View File
                                    </a>
                                    <button type="button" @click="deleteFile()" class="px-3 py-1.5 bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800 text-xs font-bold rounded-lg hover:bg-red-200 flex items-center">
                                        <svg class="w-3.5 h-3.5 mr-1 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete File
                                    </button>
                                </div>
                            </div>

                            <p x-show="fileError" x-text="fileError" class="text-xs font-bold text-red-600 mt-2" x-cloak></p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow transition">
                            Submit PTS-2 Synopsis Form &rarr;
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
