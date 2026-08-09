<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Evaluate & Edit PTS-2 PhD Synopsis Form') }}
            </h2>
            <a href="{{ route('faculty.dashboard') }}" class="px-4 py-1.5 bg-gray-600 hover:bg-gray-700 text-white font-bold text-xs rounded-lg shadow transition">
                &larr; Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        action: 'approve',
        selectedFileName: '',
        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                this.selectedFileName = file.name;
            }
        }
    }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Read-Only Student Profile -->
            <div class="bg-indigo-900 text-white rounded-xl p-6 shadow-sm space-y-3">
                <div class="flex justify-between items-start border-b border-indigo-700 pb-3">
                    <div>
                        <span class="text-xs uppercase text-indigo-300 font-bold">PhD Student Profile (Read-Only)</span>
                        <h3 class="text-2xl font-bold mt-0.5">{{ $student->user->name }}</h3>
                    </div>
                    <span class="bg-indigo-700 text-indigo-100 text-xs font-mono font-bold px-3 py-1 rounded-full border border-indigo-600">
                        Roll: {{ $student->roll_number }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs pt-1">
                    <div>
                        <span class="text-indigo-300 block">Department:</span>
                        <strong class="text-white font-semibold text-sm">{{ $student->department->name ?? 'N/A' }}</strong>
                    </div>
                    <div>
                        <span class="text-indigo-300 block">Registration Date:</span>
                        <strong class="text-white font-semibold text-sm">{{ $student->date_registration }}</strong>
                    </div>
                    <div>
                        <span class="text-indigo-300 block">Joining Date:</span>
                        <strong class="text-white font-semibold text-sm">{{ $student->date_joining }}</strong>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                
                <form action="{{ route('faculty.pts2.update', $pts2->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <h4 class="font-bold text-base text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        1. Supervisor Edit Section (Synopsis Details & Uploaded File)
                    </h4>


                    <!-- Remarks -->
                    <div>
                        <label for="remarks" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            Remarks / Observations
                        </label>
                        <textarea name="remarks" id="remarks" rows="3"
                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">{{ old('remarks', $pts2->remarks) }}</textarea>
                    </div>

                    <!-- Submitted File Display & Optional Replacement -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/40 rounded-xl border border-gray-200 dark:border-gray-600 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">Submitted Synopsis Report Document</span>
                            <a href="{{ route('pts.document.serve', ['pts2', $pts2->id, 'synopsis_report_doc_path']) }}" target="_blank" class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded hover:bg-blue-200">
                                📄 Inspect Submitted Synopsis File
                            </a>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Replace Document (Optional):</label>
                            <input type="file" name="synopsis_report_doc" accept=".pdf,.doc,.docx" @change="handleFileSelect($event)" class="text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                    </div>

                    <h4 class="font-bold text-base text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2 pt-2">
                        2. Supervisor Evaluation & Action
                    </h4>

                    <!-- Action Radio Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Approve Card -->
                        <div @click="action = 'approve'" :class="action === 'approve' ? 'border-emerald-500 bg-emerald-50/40 dark:bg-emerald-950/20 ring-2 ring-emerald-500' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'" class="p-4 rounded-xl border cursor-pointer transition space-y-2">
                            <div class="flex items-center space-x-2">
                                <input type="radio" name="action" value="approve" x-model="action" class="text-emerald-600 focus:ring-emerald-500">
                                <span class="font-bold text-sm text-emerald-900 dark:text-emerald-300">✓ Approve & Forward</span>
                            </div>
                            <p class="text-xs text-gray-500">Approve synopsis report and forward form to Co-Supervisors / PSPC Committee.</p>
                        </div>

                        <!-- Revert Card -->
                        <div @click="action = 'revert'" :class="action === 'revert' ? 'border-red-500 bg-red-50/40 dark:bg-red-950/20 ring-2 ring-red-500' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'" class="p-4 rounded-xl border cursor-pointer transition space-y-2">
                            <div class="flex items-center space-x-2">
                                <input type="radio" name="action" value="revert" x-model="action" class="text-red-600 focus:ring-red-500">
                                <span class="font-bold text-sm text-red-900 dark:text-red-300">⚠️ Revert Back to Student</span>
                            </div>
                            <p class="text-xs text-gray-500">Revert form back to student with feedback comments for modifications.</p>
                        </div>
                    </div>

                    <!-- Comment Input -->
                    <div>
                        <label for="comment" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            Supervisor Evaluation Comments / Reversion Reason <span x-show="action === 'revert'" class="text-red-500">*</span>
                        </label>
                        <textarea name="comment" id="comment" rows="3" :required="action === 'revert'"
                            placeholder="Enter endorsement observations or reversion comments..."
                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                        <button type="submit" :class="action === 'approve' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-red-600 hover:bg-red-700'" class="px-6 py-2.5 text-white font-bold text-sm rounded-xl shadow transition">
                            Submit Evaluation & Decision &rarr;
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
