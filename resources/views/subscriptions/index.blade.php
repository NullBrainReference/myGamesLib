@extends('layouts.app')

@section('title', 'Subscriptions & Membership Plans')

@section('content')
<div class="container py-8 max-w-5xl mx-auto px-4">
    <x-callback-message />

    <div class="text-center mb-10">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Upgrade Your myGamesLib Workspace</h1>
        <p class="text-gray-600 text-sm max-w-xl mx-auto">
            Unlock advanced project forking capabilities and priority position ticket submissions.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch">
        {{-- Plus Plan Card --}}
        <div class="bg-white rounded-xl border border-indigo-200 shadow-sm hover:shadow-md transition flex flex-col p-6 relative">
            @if($user->subscriptionPlan() === 'plus')
                <span class="absolute -top-3 right-4 bg-indigo-600 text-white text-[10px] uppercase tracking-wider font-extrabold px-3 py-1 rounded-full shadow-sm">
                    Current Active Plan
                </span>
            @endif

            <div class="mb-4">
                <h3 class="text-xl font-bold text-gray-900">Plus Plan</h3>
                <p class="text-xs text-gray-500 mt-1">Ideal for active creators & system modders</p>
                <div class="mt-3 text-2xl font-black text-indigo-600">$4.99 <span class="text-xs text-gray-500 font-normal">/ month</span></div>
            </div>

            <ul class="space-y-3 text-xs text-gray-600 mb-6 flex-grow">
                <li class="flex items-center gap-2">
                    <span class="text-emerald-500 font-bold">✓</span> <strong>Unlimited Project Forking</strong>
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-emerald-500 font-bold">✓</span> Anti-spam protection (Max 3 unapproved forks simultaneously)
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-emerald-500 font-bold">✓</span> Access to community mechanic blueprint libraries
                </li>
            </ul>

            <form action="{{ route('subscriptions.subscribe') }}" method="POST">
                @csrf
                <input type="hidden" name="plan" value="plus">
                <button type="submit" 
                        class="w-full py-2.5 px-4 rounded-lg font-bold text-xs text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-sm {{ $user->subscriptionPlan() === 'plus' ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ $user->subscriptionPlan() === 'plus' ? 'disabled' : '' }}>
                    {{ $user->subscriptionPlan() === 'plus' ? 'Active Plan' : 'Subscribe to Plus' }}
                </button>
            </form>
        </div>

        {{-- Enterprise Plan Card --}}
        <div class="bg-white rounded-xl border-2 border-emerald-500 shadow-md hover:shadow-lg transition flex flex-col p-6 relative">
            @if($user->subscriptionPlan() === 'enterprise')
                <span class="absolute -top-3 right-4 bg-emerald-600 text-white text-[10px] uppercase tracking-wider font-extrabold px-3 py-1 rounded-full shadow-sm">
                    Current Active Plan
                </span>
            @endif

            <div class="mb-4">
                <h3 class="text-xl font-bold text-gray-900">Enterprise Plan</h3>
                <p class="text-xs text-gray-500 mt-1">For development studios & power contributors</p>
                <div class="mt-3 text-2xl font-black text-emerald-600">$14.99 <span class="text-xs text-gray-500 font-normal">/ month</span></div>
            </div>

            <ul class="space-y-3 text-xs text-gray-600 mb-6 flex-grow">
                <li class="flex items-center gap-2">
                    <span class="text-emerald-500 font-bold">✓</span> <strong>Includes all Plus Plan features</strong>
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-emerald-500 font-bold">✓</span> <strong>Bypasses Position Ticket Limits</strong> when applying for vacancy roles
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-emerald-500 font-bold">✓</span> Priority recruitment status across project teams
                </li>
            </ul>

            <form action="{{ route('subscriptions.subscribe') }}" method="POST">
                @csrf
                <input type="hidden" name="plan" value="enterprise">
                <button type="submit" 
                        class="w-full py-2.5 px-4 rounded-lg font-bold text-xs text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-sm {{ $user->subscriptionPlan() === 'enterprise' ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ $user->subscriptionPlan() === 'enterprise' ? 'disabled' : '' }}>
                    {{ $user->subscriptionPlan() === 'enterprise' ? 'Active Plan' : 'Subscribe to Enterprise' }}
                </button>
            </form>
        </div>
    </div>

    @if($activeSubscription)
        <div class="mt-8 text-center">
            <form action="{{ route('subscriptions.cancel') }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        onclick="return confirm('Are you sure you want to cancel your subscription?');"
                        class="text-xs text-red-500 hover:text-red-700 underline">
                    Cancel current subscription
                </button>
            </form>
        </div>
    @endif
</div>
@endsection