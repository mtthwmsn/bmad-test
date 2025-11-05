<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Run Competition') }}: {{ $competition->name }}
            </h2>
            <a href="{{ route('admin.competitions.edit', $competition) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back to Edit
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Competition Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Competition Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="font-semibold">Status:</span>
                            <span class="ml-2 px-2 py-1 rounded text-sm
                                @if($competition->status === 'draft') bg-yellow-100 text-yellow-800
                                @elseif($competition->status === 'active') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($competition->status) }}
                            </span>
                        </div>
                        <div>
                            <span class="font-semibold">Location:</span> {{ $competition->location }}
                        </div>
                        <div>
                            <span class="font-semibold">Event Date:</span> {{ $competition->event_date }}
                        </div>
                        <div>
                            <span class="font-semibold">Competitors:</span> {{ $competition->competitors->count() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stages Overview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Stages</h3>
                    @if ($competition->stages->isEmpty())
                        <p class="text-gray-500">No stages defined for this competition.</p>
                    @else
                        <div class="space-y-2">
                            @foreach ($competition->stages as $stage)
                                <div class="border rounded p-3 bg-gray-50">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <span class="font-semibold">Stage {{ $stage->order }}</span>
                                            @if ($stage->name)
                                                <span class="text-gray-600">- {{ $stage->name }}</span>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            Expected: {{ $stage->expected_seconds }}s |
                                            Measures: {{ $stage->measures->count() }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Competitors and Attempt Grid -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Start Attempts</h3>

                    @if ($competition->competitors->isEmpty())
                        <p class="text-gray-500">No competitors assigned to this competition.</p>
                    @elseif ($competition->stages->isEmpty())
                        <p class="text-gray-500">No stages defined. Please add stages before running attempts.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Competitor
                                        </th>
                                        @foreach ($competition->stages as $stage)
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Stage {{ $stage->order }}
                                                @if ($stage->name)
                                                    <br><span class="text-gray-400 normal-case">{{ $stage->name }}</span>
                                                @endif
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($competition->competitors as $competitor)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $competitor->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $competitor->bar }}, {{ $competitor->country }}</div>
                                            </td>
                                            @foreach ($competition->stages as $stage)
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    @php
                                                        $attempt = $competition->attempts
                                                            ->where('competitor_id', $competitor->id)
                                                            ->where('stage_id', $stage->id)
                                                            ->first();
                                                    @endphp

                                                    @if ($attempt)
                                                        <div class="text-sm">
                                                            <span class="px-2 py-1 rounded text-xs
                                                                @if($attempt->status === 'completed') bg-green-100 text-green-800
                                                                @elseif($attempt->status === 'disqualified') bg-red-100 text-red-800
                                                                @else bg-blue-100 text-blue-800
                                                                @endif">
                                                                {{ ucfirst($attempt->status) }}
                                                            </span>
                                                            @if ($attempt->duration_ms)
                                                                <div class="text-xs text-gray-500 mt-1">
                                                                    {{ number_format($attempt->duration_ms / 1000, 2) }}s
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <a href="{{ route('run.create-attempt', ['competition' => $competition, 'competitor' => $competitor, 'stage' => $stage]) }}"
                                                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                                            Start
                                                        </a>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Attempts Log -->
            @if ($competition->attempts->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">Recent Attempts</h3>
                        <div class="space-y-2">
                            @foreach ($competition->attempts->sortByDesc('created_at')->take(10) as $attempt)
                                <div class="border rounded p-3 bg-gray-50">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <span class="font-semibold">{{ $attempt->competitor->name }}</span>
                                            <span class="text-gray-600">- Stage {{ $attempt->stage->order }}</span>
                                            @if ($attempt->stage->name)
                                                <span class="text-gray-500">({{ $attempt->stage->name }})</span>
                                            @endif
                                        </div>
                                        <div class="text-sm">
                                            <span class="px-2 py-1 rounded text-xs
                                                @if($attempt->status === 'completed') bg-green-100 text-green-800
                                                @elseif($attempt->status === 'disqualified') bg-red-100 text-red-800
                                                @else bg-blue-100 text-blue-800
                                                @endif">
                                                {{ ucfirst($attempt->status) }}
                                            </span>
                                            @if ($attempt->duration_ms)
                                                <span class="text-gray-600 ml-2">{{ number_format($attempt->duration_ms / 1000, 2) }}s</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
