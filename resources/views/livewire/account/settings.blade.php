<div>
    @section('title', 'Settings')

    @section('content')
        <x-container>
            <div class="max-w-2xl mx-auto">
                <x-h2>
                    {{ __('Account Settings') }}
                </x-h2>
                
                <div class="mt-8">
                    <form wire:submit="updateSettings">
                        <div class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                    {{ __('Name') }}
                                </label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       wire:model.live="name" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600">
                                @error('name') 
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                @enderror
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                    {{ __('Email') }}
                                </label>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       wire:model.live="email" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600">
                                @error('email') 
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                @enderror
                            </div>
                            
                            <div>
                                <label for="locale" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                    {{ __('Language') }}
                                </label>
                                <select name="locale" 
                                        id="locale" 
                                        wire:model.live="locale" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600">
                                    @foreach(config('locales.supported', []) as $code => $name)
                                        <option value="{{ $code }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('locale') 
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <button type="submit"
                                    class="inline-flex justify-center rounded-md border border-transparent bg-primary-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                {{ __('Update Settings') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </x-container>
    @endsection
</div>

