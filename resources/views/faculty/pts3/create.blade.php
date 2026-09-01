<x-app-layout>
    @php
        $formPrefix = 'PTS';
        $isReverted = isset($pts3) && $pts3->status === 'reverted';

        $defaultExaminer = ['name'=>'', 'designation'=>'', 'organization'=>'', 'postal_address'=>'', 'email'=>'', 'phone_number'=>'', 'phone_country_code'=>'+91', 'phone_iso2'=>'in', 'website'=>'', 'research_area'=>'', 'has_consent'=>'0'];
        $defaultOeb = ['name'=>'', 'designation'=>'', 'department'=>'', 'email'=>'', 'phone_number'=>'', 'phone_country_code'=>'+91', 'phone_iso2'=>'in'];

        $indianData = isset($pts3) ? $pts3->indianExaminers->map(function($e) {
            return [
                'name' => $e->name, 'designation' => $e->designation, 'organization' => $e->organization,
                'postal_address' => $e->postal_address, 'email' => $e->email, 'phone_number' => $e->phone_number,
                'phone_country_code' => $e->phone_country_code ?? '+91', 'phone_iso2' => $e->phone_iso2 ?? 'in',
                'website' => $e->website, 'research_area' => $e->research_area, 'has_consent' => $e->has_consent ? '1' : '0'
            ];
        })->toArray() : [$defaultExaminer, $defaultExaminer];

        $intlData = isset($pts3) ? $pts3->internationalExaminers->map(function($e) {
            return [
                'name' => $e->name, 'designation' => $e->designation, 'organization' => $e->organization,
                'postal_address' => $e->postal_address, 'email' => $e->email, 'phone_number' => $e->phone_number,
                'phone_country_code' => $e->phone_country_code ?? '+91', 'phone_iso2' => $e->phone_iso2 ?? 'in',
                'website' => $e->website, 'research_area' => $e->research_area, 'has_consent' => $e->has_consent ? '1' : '0'
            ];
        })->toArray() : [$defaultExaminer, $defaultExaminer];

        $oebData = isset($pts3) ? $pts3->oebMembers->map(function($o) {
            return [
                'name' => $o->name, 'designation' => $o->designation, 'department' => $o->department,
                'email' => $o->email, 'phone_number' => $o->phone_number,
                'phone_country_code' => $o->phone_country_code ?? '+91', 'phone_iso2' => $o->phone_iso2 ?? 'in'
            ];
        })->toArray() : array_fill(0, 4, $defaultOeb);
    @endphp
    <div class="py-6" x-data="pts3Form()">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 space-y-4">

            <!-- Top Back to Dashboard Button -->
            <div>
                <x-back-to-dashboard-button />
            </div>

            <!-- Page Header Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                        {{ $isReverted ? __("Edit & Resubmit {$formPrefix}-3 Panel of Examiners") : __("Initiate {$formPrefix}-3 Panel of Examiners") }}
                    </h2>
                </div>
                @if($isReverted)
                    <div class="flex items-center space-x-3 shrink-0">
                        <span class="px-3.5 py-1.5 bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 text-xs font-extrabold rounded-full uppercase tracking-wider flex items-center shadow-xs border border-amber-200 dark:border-amber-800">
                            ⚠️ Reverted
                        </span>
                    </div>
                @endif
            </div>

            <!-- Error Alerts -->
            @if($errors->any())
                <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg shadow-sm">
                    <div class="font-bold">Please correct the errors below:</div>
                    <ul class="mt-1 list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ $isReverted ? route('faculty.pts3.update', $pts3->id) : route('faculty.pts3.store', $student->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6" @submit="return validateForm($event)">
                @csrf
                @if($isReverted)
                    @method('PUT')
                @endif

                @if($isReverted)
                    <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 rounded-xl space-y-2">
                        <div class="flex items-center space-x-2 text-amber-900 dark:text-amber-200">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <span class="font-bold text-sm">Form Reverted by {{ $pts3->getRevertedByRoleLabel() ?? 'Authority' }}</span>
                        </div>
                        @if($pts3->reversion_comment)
                            <div class="text-xs text-gray-700 dark:text-gray-300">
                                <strong>Reversion Comment:</strong>
                                <p class="italic bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-amber-200 dark:border-amber-900 mt-1 whitespace-pre-wrap break-words">{{ trim($pts3->reversion_comment) }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Section 1: Pre-filled Student Details -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">
                        1. Student Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Student Name</label>
                            <input type="text" value="{{ $studentUser->name }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Roll Number</label>
                            <input type="text" value="{{ $student->roll_number }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Department</label>
                            <input type="text" value="{{ $student->department->name ?? 'N/A' }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Main Supervisor</label>
                            <input type="text" value="{{ auth()->user()->name }}" readonly class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border-gray-300 dark:border-gray-600 cursor-not-allowed">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Name of Thesis -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        2. Name of Thesis
                    </h3>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Thesis Title <span class="text-red-500">*</span></label>
                        <input type="text" name="thesis_title" required x-model="thesisTitle" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" placeholder="Enter full title of thesis">
                    </div>
                </div>

                <!-- Section 3: Contact & Course Details -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        3. Contact &amp; Course Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Current Residential Address <span class="text-red-500">*</span></label>
                            <textarea name="current_address" required rows="3" x-model="currentAddress" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Recent Contact No. <span class="text-red-500">*</span></label>
                            <input type="text" name="recent_phone_number" required x-model="recentPhone" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <input type="hidden" name="recent_phone_country_code" value="+91">
                            <input type="hidden" name="recent_phone_iso2" value="in">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Alternate Contact No. (Optional)</label>
                            <input type="text" name="alternate_phone_number" x-model="alternatePhone" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Alternate Email Address (Optional)</label>
                            <input type="email" name="alternate_email" x-model="alternateEmail" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Total Course Credits Earned <span class="text-red-500">*</span></label>
                            <input type="number" step="0.5" min="0" name="course_credits_student" required x-model="courseCredits" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                </div>

                <!-- Examiners Components via Alpine -->
                <x-pts3-examiners-section type="indian" title="4. Indian Examiners Panel" />
                
                <x-pts3-examiners-section type="international" title="5. International Examiners Panel" />

                <!-- OEB Members -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">
                        6. Oral Examination Board (OEB) Faculty Members
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Please provide exactly 4 faculty members for the OEB.</p>
                    
                    <div class="space-y-6">
                        <template x-for="(oeb, index) in oebMembers" :key="index">
                            <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg relative bg-gray-50 dark:bg-gray-900/50">
                                <h4 class="font-bold text-gray-700 dark:text-gray-300 mb-3" x-text="`OEB Member ${index + 1}`"></h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Name *</label>
                                        <input type="text" x-model="oeb.name" :name="`oeb_members[${index}][name]`" required class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Designation *</label>
                                        <input type="text" x-model="oeb.designation" :name="`oeb_members[${index}][designation]`" required class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Department *</label>
                                        <input type="text" x-model="oeb.department" :name="`oeb_members[${index}][department]`" required class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Email *</label>
                                        <input type="email" x-model="oeb.email" :name="`oeb_members[${index}][email]`" required class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex justify-end pt-4 pb-12">
                    <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md transition-all">
                        {{ $isReverted ? 'Update & Resubmit PTS-3' : 'Submit & Forward PTS-3' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Define alpine data logic in script -->
    <script>
        function pts3Form() {
            return {
                thesisTitle: @json($pts3->thesis_title ?? $thesis->title ?? ''),
                courseCredits: @json($pts3->course_credits_student ?? $student->course_credits_earned ?? ''),
                currentAddress: @json($pts3->current_address ?? ''),
                recentPhone: @json($pts3->recent_phone_number ?? ''),
                alternatePhone: @json($pts3->alternate_phone_number ?? ''),
                alternateEmail: @json($pts3->alternate_email ?? ''),
                
                indianExaminers: @json($indianData),
                internationalExaminers: @json($intlData),
                oebMembers: @json($oebData),
                
                addExaminer(type) {
                    if (this[type].length < 4) {
                        this[type].push({name:'', designation:'', organization:'', postal_address:'', email:'', phone:'', website:'', research_area:'', has_consent:'0'});
                    }
                },
                removeExaminer(type, index) {
                    if (this[type].length > 2) {
                        this[type].splice(index, 1);
                    }
                },
                validateCombinations(type) {
                    let consentCount = this[type].filter(e => e.has_consent === '1').length;
                    let totalCount = this[type].length;
                    
                    if (totalCount === 2 && consentCount !== 2) return "For 2 examiners, both MUST have consent.";
                    if (totalCount === 3 && consentCount !== 1) return "For 3 examiners, exactly 1 MUST have consent, and 2 without consent.";
                    if (totalCount === 4 && consentCount !== 0) return "For 4 examiners, none should have consent.";
                    
                    return null;
                },
                validateForm(event) {
                    let err1 = this.validateCombinations('indianExaminers');
                    let err2 = this.validateCombinations('internationalExaminers');
                    
                    if (err1) {
                        alert("Indian Examiners Panel Error:\n" + err1);
                        event.preventDefault();
                        return false;
                    }
                    if (err2) {
                        alert("International Examiners Panel Error:\n" + err2);
                        event.preventDefault();
                        return false;
                    }
                    return true;
                }
            }
        }
    </script>
</x-app-layout>
