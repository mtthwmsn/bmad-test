<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add Competitor') }}
            </h2>
            <a href="{{ route('admin.competitors.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Back to Competitors
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.competitors.store') }}">
                        @csrf

                        <!-- First Name -->
                        <div class="mb-4">
                            <label for="first_name" class="block text-sm font-medium text-gray-700">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('first_name') border-red-500 @enderror">
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div class="mb-4">
                            <label for="last_name" class="block text-sm font-medium text-gray-700">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('last_name') border-red-500 @enderror">
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bar Name -->
                        <div class="mb-4">
                            <label for="bar_name" class="block text-sm font-medium text-gray-700">
                                Bar Name
                            </label>
                            <input type="text" name="bar_name" id="bar_name" value="{{ old('bar_name') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('bar_name') border-red-500 @enderror">
                            @error('bar_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Country Code -->
                        <div class="mb-4">
                            <label for="country_code" class="block text-sm font-medium text-gray-700">
                                Country Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="country_code" id="country_code" value="{{ old('country_code') }}" required
                                maxlength="2" placeholder="e.g., US, GB, FR"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('country_code') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">ISO 3166-1 alpha-2 code (2 characters)</p>
                            @error('country_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Instagram -->
                        <div class="mb-4">
                            <label for="instagram" class="block text-sm font-medium text-gray-700">
                                Instagram Handle
                            </label>
                            <input type="text" name="instagram" id="instagram" value="{{ old('instagram') }}"
                                placeholder="@username"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('instagram') border-red-500 @enderror">
                            @error('instagram')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Diffords Profile -->
                        <div class="mb-6">
                            <label for="diffords_profile" class="block text-sm font-medium text-gray-700">
                                Diffords Profile URL
                            </label>
                            <input type="url" name="diffords_profile" id="diffords_profile" value="{{ old('diffords_profile') }}"
                                placeholder="https://www.diffordsguide.com/..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('diffords_profile') border-red-500 @enderror">
                            @error('diffords_profile')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('admin.competitors.index') }}" class="text-gray-600 hover:text-gray-900">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Add Competitor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
