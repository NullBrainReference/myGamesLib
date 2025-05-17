<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js" defer></script>
<nav x-data="{ open: false }" class="navbar navbar-expand-lg navbar-light bg-light border-b border-gray-100">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <x-application-logo class="h-9 w-auto text-gray-800" />
        </a>

        <!-- Toggle Button -->
        <button class="navbar-toggler" type="button" @click="open = !open">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse" :class="{ 'show': open }" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('index') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('shop') }}">Shop</a>
                </li>
            </ul>

            <!-- Auth Section -->
            @auth
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="btn btn-light">
                            {{ Auth::user()->name }}
                            <svg class="ms-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0-01-1.414 0l-4-4a1 1 0-010-1.414z"/>
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('library')">
                            My Library
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                Log Out
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">{{ __('Login') }}</a>
                <a href="{{ route('register') }}" class="btn btn-secondary ms-2">{{ __('Register') }}</a>
            @endauth
        </div>
    </div>
</nav>