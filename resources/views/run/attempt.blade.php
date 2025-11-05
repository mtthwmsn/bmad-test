<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Record Attempt: {{ $competitor->name }} - Stage {{ $stage->order }}
                @if ($stage->name)
                    ({{ $stage->name }})
                @endif
            </h2>
            <a href="{{ route('run.dashboard', $competition) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Competitor and Stage Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Competitor</h3>
                            <div class="text-sm">
                                <div><span class="font-semibold">Name:</span> {{ $competitor->name }}</div>
                                <div><span class="font-semibold">Bar:</span> {{ $competitor->bar }}</div>
                                <div><span class="font-semibold">Country:</span> {{ $competitor->country }}</div>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Stage Details</h3>
                            <div class="text-sm">
                                <div><span class="font-semibold">Stage:</span> {{ $stage->order }}</div>
                                @if ($stage->name)
                                    <div><span class="font-semibold">Name:</span> {{ $stage->name }}</div>
                                @endif
                                <div><span class="font-semibold">Expected Duration:</span> {{ $stage->expected_seconds }}s</div>
                                <div><span class="font-semibold">Measures:</span> {{ $stage->measures->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timer Display -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 text-center">
                    <h3 class="text-lg font-semibold mb-4">Timer</h3>
                    <div id="timer-display" class="text-6xl font-bold mb-4 font-mono">00:00.000</div>
                    <div class="space-x-4">
                        <button id="start-btn" onclick="startTimer()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-6 rounded text-lg">
                            Start Timer
                        </button>
                        <button id="stop-btn" onclick="stopTimer()" disabled class="bg-red-500 hover:bg-red-700 text-white font-bold py-3 px-6 rounded text-lg disabled:opacity-50 disabled:cursor-not-allowed">
                            Stop Timer
                        </button>
                        <button id="reset-btn" onclick="resetTimer()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded text-lg">
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Measure Input Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Record Pour Results</h3>

                    <form id="attempt-form" method="POST" action="{{ route('run.store-attempt') }}">
                        @csrf

                        <!-- Hidden fields -->
                        <input type="hidden" name="competition_id" value="{{ $competition->id }}">
                        <input type="hidden" name="competitor_id" value="{{ $competitor->id }}">
                        <input type="hidden" name="stage_id" value="{{ $stage->id }}">
                        <input type="hidden" name="duration_ms" id="duration_ms" value="0">
                        <input type="hidden" name="started_at" id="started_at" value="">
                        <input type="hidden" name="ended_at" id="ended_at" value="">

                        <!-- Measure inputs -->
                        <div class="space-y-4 mb-6">
                            @foreach ($stage->measures as $measure)
                                <div class="border rounded p-4 bg-gray-50">
                                    <label for="measure_{{ $measure->id }}" class="block text-sm font-medium text-gray-700 mb-2">
                                        Measure {{ $measure->order }}
                                        <span class="text-gray-500">(Target: {{ $measure->target_ml }} ml)</span>
                                    </label>
                                    <div class="flex items-center space-x-2">
                                        <input
                                            type="number"
                                            step="0.1"
                                            min="0"
                                            name="results[{{ $loop->index }}][poured_ml]"
                                            id="measure_{{ $measure->id }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="Enter poured ml"
                                            required>
                                        <input type="hidden" name="results[{{ $loop->index }}][measure_id]" value="{{ $measure->id }}">
                                        <span class="text-sm text-gray-600">ml</span>
                                    </div>
                                    @php
                                        $diff = 0;
                                    @endphp
                                    <div class="text-xs text-gray-500 mt-1">
                                        Difference from target will be calculated upon entry
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Error display -->
                        @if ($errors->any())
                            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                <strong class="font-bold">Please correct the following errors:</strong>
                                <ul class="mt-2 list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Submit button -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('run.dashboard', $competition) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button
                                type="submit"
                                id="submit-btn"
                                disabled
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                                Save Attempt
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let startTime = null;
        let elapsedTime = 0;
        let timerInterval = null;
        let isRunning = false;

        function updateTimerDisplay() {
            const totalMs = elapsedTime;
            const minutes = Math.floor(totalMs / 60000);
            const seconds = Math.floor((totalMs % 60000) / 1000);
            const milliseconds = totalMs % 1000;

            const display = String(minutes).padStart(2, '0') + ':' +
                           String(seconds).padStart(2, '0') + '.' +
                           String(milliseconds).padStart(3, '0');

            document.getElementById('timer-display').textContent = display;
        }

        function startTimer() {
            if (isRunning) return;

            isRunning = true;
            startTime = Date.now() - elapsedTime;

            // Record start time
            document.getElementById('started_at').value = new Date(startTime).toISOString();

            timerInterval = setInterval(() => {
                elapsedTime = Date.now() - startTime;
                updateTimerDisplay();
            }, 10); // Update every 10ms for smooth display

            // Update button states
            document.getElementById('start-btn').disabled = true;
            document.getElementById('stop-btn').disabled = false;
        }

        function stopTimer() {
            if (!isRunning) return;

            isRunning = false;
            clearInterval(timerInterval);

            // Record end time and duration
            const endTime = Date.now();
            document.getElementById('ended_at').value = new Date(endTime).toISOString();
            document.getElementById('duration_ms').value = elapsedTime;

            // Update button states
            document.getElementById('start-btn').disabled = true;
            document.getElementById('stop-btn').disabled = true;
            document.getElementById('submit-btn').disabled = false;
        }

        function resetTimer() {
            isRunning = false;
            clearInterval(timerInterval);
            elapsedTime = 0;
            startTime = null;

            updateTimerDisplay();

            // Clear hidden fields
            document.getElementById('started_at').value = '';
            document.getElementById('ended_at').value = '';
            document.getElementById('duration_ms').value = '0';

            // Reset button states
            document.getElementById('start-btn').disabled = false;
            document.getElementById('stop-btn').disabled = true;
            document.getElementById('submit-btn').disabled = true;
        }

        // Add live calculation of differences for each measure
        document.addEventListener('DOMContentLoaded', function() {
            const measureInputs = document.querySelectorAll('input[name^="results"][name$="[poured_ml]"]');

            measureInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const measureDiv = this.closest('.border');
                    const label = measureDiv.querySelector('label');
                    const targetMatch = label.textContent.match(/Target: ([\d.]+) ml/);

                    if (targetMatch && this.value) {
                        const target = parseFloat(targetMatch[1]);
                        const poured = parseFloat(this.value);
                        const diff = poured - target;
                        const diffText = diff >= 0 ? `+${diff.toFixed(1)}` : diff.toFixed(1);

                        let diffDisplay = measureDiv.querySelector('.diff-display');
                        if (!diffDisplay) {
                            diffDisplay = document.createElement('div');
                            diffDisplay.className = 'diff-display text-sm mt-1 font-semibold';
                            measureDiv.appendChild(diffDisplay);
                        }

                        const diffClass = Math.abs(diff) <= 2 ? 'text-green-600' : 'text-red-600';
                        diffDisplay.className = `diff-display text-sm mt-1 font-semibold ${diffClass}`;
                        diffDisplay.textContent = `Difference: ${diffText} ml`;
                    }
                });
            });
        });

        // Prevent accidental navigation away
        window.addEventListener('beforeunload', function (e) {
            if (isRunning || (elapsedTime > 0 && !document.getElementById('submit-btn').disabled)) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    </script>
</x-app-layout>
