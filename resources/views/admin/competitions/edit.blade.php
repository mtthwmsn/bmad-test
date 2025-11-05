<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Competition') }}
            </h2>
            <a href="{{ route('admin.competitions.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Back to Competitions
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.competitions.update', $competition) }}">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Competition Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $competition->name) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- City -->
                        <div class="mb-4">
                            <label for="city" class="block text-sm font-medium text-gray-700">
                                City <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="city" id="city" value="{{ old('city', $competition->city) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('city') border-red-500 @enderror">
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Country Code -->
                        <div class="mb-4">
                            <label for="country_code" class="block text-sm font-medium text-gray-700">
                                Country Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="country_code" id="country_code" value="{{ old('country_code', $competition->country_code) }}" required
                                maxlength="2" placeholder="e.g., US, GB, FR"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('country_code') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">ISO 3166-1 alpha-2 code (2 characters)</p>
                            @error('country_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date -->
                        <div class="mb-4">
                            <label for="date" class="block text-sm font-medium text-gray-700">
                                Competition Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="date" id="date" value="{{ old('date', $competition->date->format('Y-m-d')) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('date') border-red-500 @enderror">
                            @error('date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-6">
                            <label for="status" class="block text-sm font-medium text-gray-700">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" id="status" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('status') border-red-500 @enderror">
                                <option value="">Select status...</option>
                                <option value="draft" {{ old('status', $competition->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="active" {{ old('status', $competition->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ old('status', $competition->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('admin.competitions.index') }}" class="text-gray-600 hover:text-gray-900">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Competition
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Stages Section -->
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Stages & Measures</h3>
                        <button type="button" onclick="document.getElementById('addStageForm').classList.toggle('hidden')" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                            + Add Stage
                        </button>
                    </div>

                    <!-- Add Stage Form -->
                    <div id="addStageForm" class="hidden mb-6 p-4 border rounded bg-gray-50">
                        <h4 class="font-semibold mb-3">Add New Stage</h4>
                        <form method="POST" action="{{ route('admin.competitions.stages.store', $competition) }}">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="stage_order" class="block text-sm font-medium text-gray-700">Order *</label>
                                    <input type="number" name="order" id="stage_order" min="1" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label for="stage_name" class="block text-sm font-medium text-gray-700">Name</label>
                                    <input type="text" name="name" id="stage_name"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label for="stage_expected_seconds" class="block text-sm font-medium text-gray-700">Expected Seconds *</label>
                                    <input type="number" name="expected_seconds" id="stage_expected_seconds" min="1" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end gap-2">
                                <button type="button" onclick="document.getElementById('addStageForm').classList.add('hidden')" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                                    Cancel
                                </button>
                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Add Stage
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Existing Stages -->
                    @if ($competition->stages->count() > 0)
                        <div class="space-y-4">
                            @foreach ($competition->stages as $stage)
                                <div class="border rounded-lg p-4 bg-white">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="px-2 py-1 bg-indigo-100 text-indigo-800 text-xs font-semibold rounded">
                                                    Stage {{ $stage->order }}
                                                </span>
                                            </div>
                                            <h4 class="font-semibold text-lg">{{ $stage->name ?? 'Unnamed Stage' }}</h4>
                                            <p class="text-sm text-gray-600">Expected: {{ $stage->expected_seconds }}s</p>
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="button" onclick="document.getElementById('editStageForm{{ $stage->id }}').classList.toggle('hidden')" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                                Edit
                                            </button>
                                            <form method="POST" action="{{ route('admin.stages.destroy', $stage) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this stage? All measures will also be deleted.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Edit Stage Form -->
                                    <div id="editStageForm{{ $stage->id }}" class="hidden mb-4 p-4 border rounded bg-gray-50">
                                        <h5 class="font-semibold mb-3 text-sm">Edit Stage</h5>
                                        <form method="POST" action="{{ route('admin.stages.update', $stage) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700">Order *</label>
                                                    <input type="number" name="order" value="{{ $stage->order }}" min="1" required
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700">Name</label>
                                                    <input type="text" name="name" value="{{ $stage->name }}"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700">Expected Seconds *</label>
                                                    <input type="number" name="expected_seconds" value="{{ $stage->expected_seconds }}" min="1" required
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                </div>
                                            </div>
                                            <div class="mt-4 flex justify-end gap-2">
                                                <button type="button" onclick="document.getElementById('editStageForm{{ $stage->id }}').classList.add('hidden')" class="text-gray-600 hover:text-gray-900 px-4 py-2 text-sm">
                                                    Cancel
                                                </button>
                                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                                    Update Stage
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Measures Section -->
                                    <div class="mt-4 pl-4 border-l-2 border-gray-200">
                                        <div class="flex justify-between items-center mb-3">
                                            <h5 class="font-semibold text-sm text-gray-700">Measures</h5>
                                            <button type="button" onclick="document.getElementById('addMeasureForm{{ $stage->id }}').classList.toggle('hidden')" class="text-green-600 hover:text-green-800 text-xs">
                                                + Add Measure
                                            </button>
                                        </div>

                                        <!-- Add Measure Form -->
                                        <div id="addMeasureForm{{ $stage->id }}" class="hidden mb-3 p-3 border rounded bg-gray-50">
                                            <form method="POST" action="{{ route('admin.stages.measures.store', $stage) }}">
                                                @csrf
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-700">Target Volume (ml) *</label>
                                                        <input type="number" name="target_ml" step="0.1" min="0.1" required
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-700">Order *</label>
                                                        <input type="number" name="order" min="1" required
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                    </div>
                                                </div>
                                                <div class="mt-3 flex justify-end gap-2">
                                                    <button type="button" onclick="document.getElementById('addMeasureForm{{ $stage->id }}').classList.add('hidden')" class="text-gray-600 hover:text-gray-900 px-3 py-1 text-xs">
                                                        Cancel
                                                    </button>
                                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                        Add Measure
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Existing Measures -->
                                        @if ($stage->measures->count() > 0)
                                            <div class="space-y-2">
                                                @foreach ($stage->measures as $measure)
                                                    <div class="p-3 border rounded bg-white">
                                                        <div class="flex justify-between items-center">
                                                            <div class="flex-1">
                                                                <div class="flex items-center gap-2">
                                                                    <span class="px-2 py-0.5 bg-purple-100 text-purple-800 text-xs font-semibold rounded">
                                                                        Measure #{{ $measure->order }}
                                                                    </span>
                                                                    <span class="font-medium text-sm">{{ $measure->target_ml }}ml</span>
                                                                </div>
                                                            </div>
                                                            <div class="flex gap-2">
                                                                <button type="button" onclick="document.getElementById('editMeasureForm{{ $measure->id }}').classList.toggle('hidden')" class="text-indigo-600 hover:text-indigo-900 text-xs">
                                                                    Edit
                                                                </button>
                                                                <form method="POST" action="{{ route('admin.measures.destroy', $measure) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this measure?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="text-red-600 hover:text-red-900 text-xs">
                                                                        Delete
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>

                                                        <!-- Edit Measure Form -->
                                                        <div id="editMeasureForm{{ $measure->id }}" class="hidden mt-3 p-3 border rounded bg-gray-50">
                                                            <form method="POST" action="{{ route('admin.measures.update', $measure) }}">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                                    <div>
                                                                        <label class="block text-xs font-medium text-gray-700">Target Volume (ml) *</label>
                                                                        <input type="number" name="target_ml" value="{{ $measure->target_ml }}" step="0.1" min="0.1" required
                                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                                    </div>
                                                                    <div>
                                                                        <label class="block text-xs font-medium text-gray-700">Order *</label>
                                                                        <input type="number" name="order" value="{{ $measure->order }}" min="1" required
                                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                                    </div>
                                                                </div>
                                                                <div class="mt-3 flex justify-end gap-2">
                                                                    <button type="button" onclick="document.getElementById('editMeasureForm{{ $measure->id }}').classList.add('hidden')" class="text-gray-600 hover:text-gray-900 px-3 py-1 text-xs">
                                                                        Cancel
                                                                    </button>
                                                                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                                        Update Measure
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-sm text-gray-500 italic">No measures yet. Click "+ Add Measure" to create one.</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">No stages yet. Click "+ Add Stage" to create the first stage for this competition.</p>
                    @endif
                </div>
            </div>

            <!-- Competitor Assignment Section -->
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-6">Assigned Competitors</h3>

                    <form method="POST" action="{{ route('admin.competitions.assign-competitors', $competition) }}">
                        @csrf

                        <!-- Search/Filter Input -->
                        <div class="mb-4">
                            <input type="text"
                                   id="competitorFilter"
                                   placeholder="Filter competitors by name..."
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Competitors List -->
                        @if ($allCompetitors->count() > 0)
                            <div id="competitorsList" class="space-y-2 max-h-96 overflow-y-auto border rounded p-4 bg-gray-50">
                                @foreach ($allCompetitors as $competitor)
                                    <div class="competitor-item flex items-center p-2 hover:bg-white rounded"
                                         data-name="{{ strtolower($competitor->first_name . ' ' . $competitor->last_name) }}">
                                        <label class="flex items-center cursor-pointer w-full">
                                            <input type="checkbox"
                                                   name="competitor_ids[]"
                                                   value="{{ $competitor->id }}"
                                                   {{ in_array($competitor->id, $assignedCompetitorIds) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <span class="ml-3 text-sm">
                                                <span class="font-medium">{{ $competitor->first_name }} {{ $competitor->last_name }}</span>
                                                @if ($competitor->bar_name)
                                                    <span class="text-gray-500">- {{ $competitor->bar_name }}</span>
                                                @endif
                                                <span class="text-gray-400">({{ $competitor->country_code }})</span>
                                            </span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4 flex justify-between items-center">
                                <p class="text-sm text-gray-600">
                                    <span id="selectedCount">{{ count($assignedCompetitorIds) }}</span> competitor(s) selected
                                </p>
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Save Competitor Assignments
                                </button>
                            </div>
                        @else
                            <p class="text-gray-600">No competitors available. <a href="{{ route('admin.competitors.create') }}" class="text-blue-500 hover:text-blue-700">Create a competitor first</a>.</p>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for filtering and count -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterInput = document.getElementById('competitorFilter');
            const competitorItems = document.querySelectorAll('.competitor-item');
            const selectedCount = document.getElementById('selectedCount');
            const checkboxes = document.querySelectorAll('input[name="competitor_ids[]"]');

            // Filter functionality
            if (filterInput) {
                filterInput.addEventListener('input', function() {
                    const filterValue = this.value.toLowerCase();

                    competitorItems.forEach(item => {
                        const name = item.dataset.name;
                        if (name.includes(filterValue)) {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }

            // Update selected count
            function updateCount() {
                const checked = document.querySelectorAll('input[name="competitor_ids[]"]:checked').length;
                if (selectedCount) {
                    selectedCount.textContent = checked;
                }
            }

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateCount);
            });
        });
    </script>
</x-app-layout>
