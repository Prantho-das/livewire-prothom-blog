<?php

use Livewire\Component;

new class extends Component
{
    public string $locale = 'bn';

    public function mount()
    {
        $this->locale = session('locale', config('app.locale'));
    }

    public function setLocale($locale)
    {
        if (in_array($locale, ['en', 'bn'])) {
            session(['locale' => $locale]);
            $this->redirect(request()->header('Referer') ?? '/', navigate: true);
        }
    }
};
?>

<div class="relative" x-data="{ open: false }" @click.away="open = false">
    <button @click="open = !open" class="flex items-center gap-1 p-2 text-gray-500 hover:text-[#c0392b] transition-colors rounded-lg hover:bg-gray-50" aria-label="ভাষা পরিবর্তন">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
        <span class="text-sm font-medium uppercase">{{ $locale }}</span>
    </button>
    <div x-cloak x-show="open" x-transition.opacity.duration.200ms class="absolute right-0 mt-2 py-2 w-32 bg-white rounded-lg shadow-xl border border-gray-100 z-50">
        <button wire:click="setLocale('bn')" @click="open = false" class="w-full text-left px-4 py-2 text-sm {{ $locale === 'bn' ? 'text-[#c0392b] bg-red-50 font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
            বাংলা (BN)
        </button>
        <button wire:click="setLocale('en')" @click="open = false" class="w-full text-left px-4 py-2 text-sm {{ $locale === 'en' ? 'text-[#c0392b] bg-red-50 font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
            English (EN)
        </button>
    </div>
</div>
