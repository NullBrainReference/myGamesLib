@extends('layouts.app')

@section('title', 'Vacancies - ' . $project->title)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-12">

    {{-- Header & Back Navigation --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <a href="{{ route('projects.view', $project->id) }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-gray-800 transition mb-2">
                ← Back to {{ $project->title }}
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Open Vacancies</h1>
            <p class="text-xs text-gray-500">Apply for positions to join the development team.</p>
        </div>
        {{-- Review Applicants Button for Owners & Admins --}}
        @auth
            @if(auth()->user()->isAdmin() || $project->owners->contains(auth()->id()))
                <a href="{{ route('projects.vacancies.applicants', $project->id) }}"
                   class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center gap-1.5">
                    📋 Review Applicants
                </a>
            @endif
        @endauth

        {{-- Debug Admin Action --}}
        @auth
            @if(auth()->user()->isAdmin())
                <form action="{{ route('projects.debug-vacancies', $project->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Generate 3 sample vacancies (Dev, Artist, Designer)?');"
                            class="px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center gap-1">
                        🛠️ Debug: Add 3 Vacancies (2 Slots Each)
                    </button>
                </form>
            @endif
        @endauth
    </div>

    {{-- System Flash Messages --}}
    @if(session('success'))
        <div class="mb-6 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-3 bg-red-50 border border-red-200 text-red-800 text-xs font-medium rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- Vacancies Grid --}}
    @if($positions->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($positions as $position)
                @php
                    $acceptedCount = $position->acceptedTicketsCount();
                    $userTicket = auth()->check() ? $position->tickets->where('user_id', auth()->id())->first() : null;
                @endphp

                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm flex flex-col justify-between">
                    <div>
                        {{-- Title & Slots --}}
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <h3 class="text-base font-bold text-gray-900">{{ $position->title }}</h3>
                            <span class="px-2 py-0.5 text-[11px] font-semibold rounded bg-indigo-50 text-indigo-700 border border-indigo-100 shrink-0">
                                {{ $acceptedCount }} / {{ $position->tickets_amount }} Slots Filled
                            </span>
                        </div>

                        {{-- Description --}}
                        <p class="text-xs text-gray-600 leading-relaxed mb-4">
                            {{ $position->description }}
                        </p>

                        {{-- Global Skill Tags --}}
                        @if($position->skills->count() > 0)
                            <div class="flex flex-wrap gap-1.5 mb-5">
                                @foreach($position->skills as $skill)
                                    <span class="px-2 py-0.5 text-[10px] font-medium bg-gray-100 text-gray-600 rounded">
                                        #{{ $skill->title }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Application Section --}}
                    <div class="pt-4 border-t border-gray-100">
                        @auth
                            @if($userTicket)
                                <div class="p-2.5 rounded-lg bg-gray-50 border border-gray-200 text-center">
                                    <span class="text-xs font-semibold text-gray-600">
                                        Status:
                                        @if($userTicket->success === true)
                                            <strong class="text-emerald-600">Accepted ✅</strong>
                                        @elseif($userTicket->success === false)
                                            <strong class="text-red-500">Declined ❌</strong>
                                        @else
                                            <strong class="text-amber-600">Under Review ⏳</strong>
                                        @endif
                                    </span>
                                </div>
                            @elseif($position->isFilled())
                                <button disabled class="w-full py-2 bg-gray-100 text-gray-400 text-xs font-semibold rounded-lg cursor-not-allowed">
                                    Position Filled
                                </button>
                            @else
                                {{-- Apply Accordion / Form --}}
                                <details class="group">
                                    <summary class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg text-center cursor-pointer transition list-none">
                                        Apply for Position
                                    </summary>

                                    <form action="{{ route('positions.apply', $position->id) }}" method="POST" class="mt-3 space-y-3">
                                        @csrf
                                        <div>
                                            <label class="block text-[11px] font-bold uppercase text-gray-600 mb-1">Motivation / Pitch</label>
                                            <textarea name="reason" rows="3" required placeholder="Why are you a good fit for this role?"
                                                      class="w-full p-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                                        </div>
                                        <button type="submit" class="w-full py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm transition">
                                            Submit Ticket
                                        </button>
                                    </form>
                                </details>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="block w-full py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg text-center transition">
                                Log in to Apply
                            </a>
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center my-8">
            <h3 class="text-sm font-bold text-gray-800">No open vacancies yet</h3>
            <p class="text-xs text-gray-500 mt-1">This project is not currently recruiting new team members.</p>
        </div>
    @endif
</div>
@endsection
