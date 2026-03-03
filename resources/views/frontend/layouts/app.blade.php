<!DOCTYPE html>
<html lang="bn" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $settings?->seo_description ?? 'বাংলাদেশের শীর্ষস্থানীয় অনলাইন সংবাদপত্র' }}">
    <meta name="keywords" content="{{ $settings?->seo_keywords ?? 'বাংলা নিউজ, সংবাদ' }}">
    <title>{{ $__env->yieldContent('title', $settings?->site_title ?? 'প্রথম ব্লগ') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- Non-blocking font load: media=print trick avoids render-blocking --}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap"></noscript>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Hind Siliguri','Noto Sans Bengali',sans-serif; }

        .category-badge { font-size:0.68rem; font-weight:700; letter-spacing:0.5px; text-transform:uppercase; }

        .card-hover { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .card-hover:hover { transform:translateY(-3px); box-shadow:0 10px 30px rgba(0,0,0,0.12); }

        .gradient-overlay { background:linear-gradient(to top,rgba(0,0,0,0.88) 0%,rgba(0,0,0,0.25) 65%,transparent 100%); }

        .sidebar-widget { border-left:4px solid #c0392b; }

        @keyframes ticker { 0%{transform:translateX(100%)} 100%{transform:translateX(-100%)} }
        .breaking-ticker { animation:ticker 28s linear infinite; }
        .breaking-ticker:hover { animation-play-state:paused; }

        /* SPA page transition */
        @keyframes fadeIn { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }
        [wire\:navigate] .page-content, .wire-nav-loaded { animation: fadeIn 0.25s ease; }

        /* Lazy image blur-up */
        .img-lazy { filter:blur(8px); transition:filter 0.4s ease; }
        .img-lazy.loaded { filter:blur(0); }

        /* Progress bar for SPA */
        #nprogress .bar { background:#c0392b !important; height:3px !important; }
        #nprogress .peg { box-shadow:0 0 10px #c0392b, 0 0 5px #c0392b !important; }

        html { scroll-behavior:smooth; }
    </style>

    @livewireStyles
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

    {{-- Topbar Notice (persisted across SPA navigations) --}}
    @if(isset($topbarNotice) && $topbarNotice)
    <div wire:ignore class="bg-gradient-to-r from-[#c0392b] to-[#e74c3c] text-white text-center text-sm py-2 px-4 font-medium">
        {{ $topbarNotice->title }}
    </div>
    @endif

    {{-- Top Date/Social Bar --}}
    <div wire:navigate.preserve wire:ignore class="bg-[#1a1a2e] text-gray-300 text-xs py-2">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
            <span>{{ now()->locale('bn')->isoFormat('dddd, D MMMM YYYY') }}</span>
            <div class="flex items-center gap-3">
                @if(isset($settings) && $settings?->facebook_url)
                <a href="{{ $settings->facebook_url }}" target="_blank" rel="noopener" aria-label="ফেসবুক" class="hover:text-white transition-colors">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                @endif
                @if(isset($settings) && $settings?->youtube_url)
                <a href="{{ $settings->youtube_url }}" target="_blank" rel="noopener" aria-label="ইউটিউব" class="hover:text-white transition-colors">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Header persisted in SPA --}}
    <header wire:navigate.preserve class="bg-white shadow-sm sticky top-0 z-50" style="box-shadow:0 2px 12px rgba(0,0,0,0.06)">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between py-3">
                {{-- Logo --}}
                <a href="{{ route('home') }}" wire:navigate aria-label="হোম" class="flex-shrink-0">
                    @if(isset($settings) && $settings?->logo)
                        <img src="{{ Storage::url($settings->logo) }}" alt="{{ $settings->site_name ?? 'প্রথম ব্লগ' }}" class="h-11 object-contain" loading="eager">
                    @else
                        <div class="flex items-center gap-2">
                            <div class="w-9 h-9 bg-[#c0392b] rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-white font-black text-xl leading-none">প</span>
                            </div>
                            <div>
                                <div class="text-xl font-black text-[#c0392b] leading-tight">{{ $settings?->site_name ?? 'প্রথম ব্লগ' }}</div>
                                <div class="text-[9px] text-gray-400 tracking-widest uppercase">বাংলাদেশের বিশ্বস্ত সংবাদ</div>
                            </div>
                        </div>
                    @endif
                </a>

                {{-- Ad --}}
                @if(isset($headerAd) && $headerAd)
                <div class="hidden lg:block">
                    @if($headerAd->image)
                    <a href="{{ $headerAd->url ?? '#' }}" target="_blank" rel="noopener">
                        <img src="{{ Storage::url($headerAd->image) }}" alt="{{ $headerAd->title }}" class="h-14 object-contain" loading="lazy">
                    </a>
                    @elseif($headerAd->code)
                    {!! $headerAd->code !!}
                    @endif
                </div>
                @endif

                {{-- Search + Mobile Toggle --}}
                <div class="flex items-center gap-2">
                    <a href="{{ route('search') }}" wire:navigate class="p-2 text-gray-500 hover:text-[#c0392b] transition-colors rounded-lg hover:bg-gray-50" aria-label="অনুসন্ধান">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/></svg>
                    </a>
                    <button
                        id="mobileMenuBtn"
                        class="lg:hidden p-2 text-gray-500 hover:text-[#c0392b] transition-colors rounded-lg hover:bg-gray-50"
                        aria-label="মেনু"
                        x-data
                        @click="$el.setAttribute('aria-expanded', document.getElementById('mobileNav').classList.toggle('flex') ? 'true' : 'false')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="bg-[#c0392b]" aria-label="প্রধান নেভিগেশন">
            <div class="max-w-7xl mx-auto px-4">
                <ul class="hidden lg:flex items-center overflow-x-auto">
                    <li>
                        <a href="{{ route('home') }}" wire:navigate class="block px-4 py-3 text-white text-sm font-semibold hover:bg-[#a93226] transition-colors whitespace-nowrap">
                            🏠 হোম
                        </a>
                    </li>
                    @if(isset($categories))
                    @foreach($categories as $cat)
                    <li>
                        <a href="{{ route('category', $cat->slug) }}" wire:navigate
                           class="block px-4 py-3 text-white text-sm font-semibold hover:bg-[#a93226] transition-colors whitespace-nowrap">
                            {{ $cat->translations->where('locale','bn')->first()?->name ?? $cat->translations->first()?->name ?? $cat->slug }}
                        </a>
                    </li>
                    @endforeach
                    @endif
                    <li>
                        <a href="{{ route('epaper') }}" wire:navigate class="block px-4 py-3 text-white text-sm font-semibold hover:bg-[#a93226] transition-colors whitespace-nowrap">
                            📰 ই-পেপার
                        </a>
                    </li>
                </ul>

                {{-- Mobile Nav --}}
                <ul id="mobileNav" class="lg:hidden hidden flex-col pb-2">
                    <li><a href="{{ route('home') }}" wire:navigate class="block px-4 py-2.5 text-white text-sm font-medium hover:bg-[#a93226]">🏠 হোম</a></li>
                    @if(isset($categories))
                    @foreach($categories as $cat)
                    <li>
                        <a href="{{ route('category', $cat->slug) }}" wire:navigate class="block px-4 py-2.5 text-white text-sm font-medium hover:bg-[#a93226]">
                            {{ $cat->translations->where('locale','bn')->first()?->name ?? $cat->slug }}
                        </a>
                    </li>
                    @endforeach
                    @endif
                    <li><a href="{{ route('epaper') }}" wire:navigate class="block px-4 py-2.5 text-white text-sm font-medium hover:bg-[#a93226]">📰 ই-পেপার</a></li>
                </ul>
            </div>
        </nav>
    </header>

    {{-- Breaking News Ticker (persisted) --}}
    @if(isset($breakingPosts) && count($breakingPosts ?? []) > 0)
    <div wire:navigate.preserve class="bg-white border-b border-gray-100 py-2 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 flex items-center gap-3">
            <span class="flex-shrink-0 bg-[#c0392b] text-white text-xs font-black px-3 py-1 rounded uppercase tracking-wide animate-pulse">সর্বশেষ</span>
            <div class="overflow-hidden flex-1 relative">
                <div class="whitespace-nowrap breaking-ticker inline-flex items-center gap-0">
                    @foreach($breakingPosts as $b)
                    <a href="{{ route('post', $b['slug']) }}" wire:navigate class="mr-14 hover:text-[#c0392b] transition-colors text-sm text-gray-700 inline-flex items-center gap-2">
                        <span class="text-[#c0392b] font-black text-xs">●</span>
                        {{ $b['title'] }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Page Content --}}
    <main class="max-w-7xl mx-auto px-4 py-6">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="bg-[#1a1a2e] text-gray-300 mt-12">
        <div class="max-w-7xl mx-auto px-4 py-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-9 h-9 bg-[#c0392b] rounded-lg flex items-center justify-center">
                            <span class="text-white font-black text-xl">প</span>
                        </div>
                        <span class="text-xl font-black text-white">{{ $settings?->site_name ?? 'প্রথম ব্লগ' }}</span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed mb-4">{{ $settings?->site_detail ?? 'বাংলাদেশের বিশ্বস্ত সংবাদ মাধ্যম।' }}</p>
                    <div class="flex gap-3">
                        @foreach(['facebook_url' => 'ফে', 'twitter_url' => 'টু', 'youtube_url' => 'ইউ', 'instagram_url' => 'ইন'] as $key => $label)
                        @if($settings?->$key)
                        <a href="{{ $settings->$key }}" target="_blank" rel="noopener"
                           class="w-8 h-8 bg-[#16213e] hover:bg-[#c0392b] rounded-full flex items-center justify-center transition-colors text-xs font-bold">
                            {{ $label }}
                        </a>
                        @endif
                        @endforeach
                    </div>
                </div>

                <div>
                    <h3 class="text-white font-bold text-xs uppercase tracking-wider mb-4 border-b border-[#c0392b] pb-2">বিভাগ</h3>
                    <ul class="space-y-1.5">
                        @if(isset($categories))
                        @foreach($categories->take(8) as $cat)
                        <li>
                            <a href="{{ route('category', $cat->slug) }}" wire:navigate class="text-gray-400 hover:text-white text-sm transition-colors flex items-center gap-1.5">
                                <span class="text-[#c0392b] font-bold">›</span>
                                {{ $cat->translations->where('locale','bn')->first()?->name ?? $cat->slug }}
                            </a>
                        </li>
                        @endforeach
                        @endif
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-bold text-xs uppercase tracking-wider mb-4 border-b border-[#c0392b] pb-2">যোগাযোগ</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        @if($settings?->contact_email)
                        <li class="flex gap-2 items-start">
                            <svg class="w-4 h-4 mt-0.5 text-[#c0392b] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 0 0 2.22 0L21 8M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z"/></svg>
                            <span>{{ $settings->contact_email }}</span>
                        </li>
                        @endif
                        @if($settings?->contact_phone)
                        <li class="flex gap-2 items-start">
                            <svg class="w-4 h-4 mt-0.5 text-[#c0392b] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 0 1 2-2h3.28a1 1 0 0 1 .948.684l1.498 4.493a1 1 0 0 1-.502 1.21l-2.257 1.13a11.042 11.042 0 0 0 5.516 5.516l1.13-2.257a1 1 0 0 1 1.21-.502l4.493 1.498a1 1 0 0 1 .684.949V19a2 2 0 0 1-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span>{{ $settings->contact_phone }}</span>
                        </li>
                        @endif
                        @if($settings?->contact_address)
                        <li class="flex gap-2 items-start">
                            <svg class="w-4 h-4 mt-0.5 text-[#c0392b] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 0 1-2.827 0l-4.244-4.243a8 8 0 1 1 11.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/></svg>
                            <span>{{ $settings->contact_address }}</span>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="border-t border-[#16213e] pt-6 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-gray-500">
                <p>&copy; {{ date('Y') }} {{ $settings?->site_name ?? 'প্রথম ব্লগ' }}। সর্বস্বত্ব সংরক্ষিত।</p>
                <div class="flex gap-4">
                    <a href="{{ route('epaper') }}" wire:navigate class="hover:text-gray-300">ই-পেপার</a>
                    <a href="{{ route('search') }}" wire:navigate class="hover:text-gray-300">অনুসন্ধান</a>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts

    <script>
        // Lazy image loader with blur-up effect
        (function() {
            function lazyLoad() {
                const imgs = document.querySelectorAll('img[data-src]');
                const io = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.onload = () => img.classList.add('loaded');
                            img.removeAttribute('data-src');
                            io.unobserve(img);
                        }
                    });
                }, { rootMargin: '200px 0px' });
                imgs.forEach(img => io.observe(img));
            }

            lazyLoad();

            // Re-init after SPA navigations
            document.addEventListener('livewire:navigated', lazyLoad);
        })();

        // Infinite scroll trigger
        (function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const el = entry.target;
                        const component = el.closest('[wire\\:id]');
                        if (component && window.Livewire) {
                            const id = component.getAttribute('wire:id');
                            Livewire.find(id)?.call(el.dataset.wireAction ?? 'loadMorePosts');
                        }
                    }
                });
            }, { rootMargin: '400px' });

            function bindSentinels() {
                document.querySelectorAll('[data-infinite-sentinel]').forEach(el => observer.observe(el));
            }

            document.addEventListener('livewire:init', bindSentinels);
            document.addEventListener('livewire:navigated', bindSentinels);
            Livewire.hook('commit', () => setTimeout(bindSentinels, 100));
        })();
    </script>

    @stack('scripts')
</body>
</html>
