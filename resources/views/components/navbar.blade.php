<nav x-data="{ open: false, dropdownOpen: false }" class="bg-white border-b border-gray-200 relative z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center">

            <div class="flex items-center space-x-8">
                <a href="{{ route('index') }}" class="text-xl font-bold text-gray-900 tracking-tight hover:text-blue-600 transition">
                    MyGamesLib
                </a>

                <ul class="hidden lg:flex space-x-6 items-center m-0 ms-1 p-0 list-none">
                    <li>
                        <a class="text-sm font-medium text-gray-600 hover:text-gray-900 transition" href="{{ route('shop') }}">Games</a>
                    </li>
                    <li>
                        <a class="text-sm font-medium text-gray-600 hover:text-gray-900 transition" href="{{ route('blog.index') }}">Posts</a>
                    </li>
                    <li>
                        <a class="text-sm font-medium text-gray-600 hover:text-gray-900 transition" href="{{ route('forum') }}">Forum</a>
                    </li>
                    <li>
                        <a class="text-sm font-medium text-gray-600 hover:text-gray-900 transition" href="{{ route('projects') }}">Projects</a>
                    </li>
                </ul>
            </div>

            <div class="hidden lg:flex items-center space-x-4">

                {{-- <div class="flex items-center space-x-2 border-r border-gray-200 pe-4 text-gray-500">
                    <a href="#" class="p-2 rounded-full hover:text-gray-900 hover:bg-gray-100 transition relative" title="View Cart">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="absolute top-1 right-1 flex h-2 w-2 rounded-full bg-blue-600"></span>
                    </a>

                    <a href="#" class="p-2 rounded-full hover:text-gray-900 hover:bg-gray-100 transition" title="Help & Support">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </a>
                </div> --}}

                <div>
                    @auth
                    <div class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" @click.outside="dropdownOpen = false" class="bg-gray-50 border border-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-medium inline-flex items-center hover:bg-gray-100 focus:outline-none transition" type="button">
                            {{ Auth::user()->name }}
                            <svg class="ms-2 h-4 w-4 text-gray-400 transition-transform duration-200" :class="{'rotate-180': dropdownOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <ul x-show="dropdownOpen"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg py-1 z-50 list-none m-0 p-0">
                            <li><a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition" href="{{ route('profile') }}">Profile</a></li>
                            <li><a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition" href="{{ route('library') }}">My Library</a></li>

                            @if (Auth::user()->isAdmin())
                            <li><a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition" href="{{ route('dashboard') }}">Dashboard</a></li>
                            @endif

                            <li class="border-t border-gray-200 my-1"></li>
                            <li>
                                <a class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition" href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Log Out
                                </a>
                            </li>
                        </ul>
                    </div>
                    @else
                        <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-md shadow-sm transition">{{ __('Login') }}</a>
                        <a href="{{ route('register') }}" class="bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium px-4 py-2 rounded-md shadow-sm ms-3 transition">{{ __('Register') }}</a>
                    @endauth
                </div>
            </div>

            <div class="flex items-center lg:hidden">
                <button class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none transition" type="button" @click="open = !open">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="transform opacity-0 -translate-y-2"
         x-transition:enter-end="transform opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="transform opacity-100 translate-y-0"
         x-transition:leave-end="transform opacity-0 -translate-y-2"
         class="lg:hidden absolute top-full left-0 w-full bg-white border-b border-gray-200 px-4 pt-2 pb-4 space-y-1 z-40 shadow-lg">

        <a class="block text-gray-950 font-semibold px-3 py-2 rounded-md hover:bg-gray-100 transition" href="{{ route('index') }}">Home</a>
        <a class="block text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md hover:bg-gray-100 transition" href="{{ route('shop') }}">Games</a>
        <a class="block text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md hover:bg-gray-100 transition" href="{{ route('blog.index') }}">Posts</a>
        <a class="block text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md hover:bg-gray-100 transition" href="{{ route('forum') }}">Forum</a>
        <a class="block text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md hover:bg-gray-100 transition" href="{{ route('projects') }}">Projects</a>


        <div class="border-t border-gray-200 mt-2 pt-2">
            @auth
                <div class="px-3 py-1 text-xs font-bold uppercase tracking-wider text-gray-400">User: {{ Auth::user()->name }}</div>
                <a class="block px-3 py-2 rounded-md text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition" href="{{ route('profile') }}">Profile</a>
                <a class="block px-3 py-2 rounded-md text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition" href="{{ route('library') }}">My Library</a>
                @if (Auth::user()->isAdmin())
                    <a class="block px-3 py-2 rounded-md text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition" href="{{ route('dashboard') }}">Dashboard</a>
                @endif
                <a class="block px-3 py-2 rounded-md text-sm text-red-600 hover:bg-red-50 transition" href="#"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Log Out
                </a>
            @else
                <div class="flex flex-col space-y-2 pt-2">
                    <a href="{{ route('login') }}" class="w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-md shadow-sm">{{ __('Login') }}</a>
                    <a href="{{ route('register') }}" class="w-full text-center bg-gray-600 hover:bg-gray-700 text-white font-medium px-4 py-2 rounded-md shadow-sm">{{ __('Register') }}</a>
                </div>
            @endauth
        </div>
    </div>
</nav>
