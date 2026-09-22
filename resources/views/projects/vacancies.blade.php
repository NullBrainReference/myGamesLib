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
                <div class="p-4 border rounded-lg bg-white shadow-sm mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="font-bold text-gray-900">{{ $position->title }}</h3>

                        {{-- Ticket Status Badge --}}
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $position->remaining_tickets > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                            {{ $position->remaining_tickets }} / {{ $position->tickets_amount }} Tickets Available
                        </span>
                    </div>

                    <p class="text-xs text-gray-600 mb-3">{{ $position->description }}</p>

                    @if($position->hasAvailableTickets())
                        {{-- Apply Form --}}
                        <form action="{{ route('positions.apply', $position->id) }}" method="POST">
                            @csrf
                            <textarea name="reason" rows="2" required placeholder="Why are you applying..." class="w-full text-xs p-2 border rounded-lg mb-2"></textarea>
                            <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700">
                                Apply (1 Ticket)
                            </button>
                        </form>
                    @else
                        {{-- Out of Tickets Alert --}}
                        <div class="p-2.5 bg-amber-50 border border-amber-200 text-amber-800 text-xs rounded-lg flex justify-between items-center">
                            <span>All application tickets spent for this position.</span>

                            @if($project->owners->contains(auth()->id()))
                                <button type="button" @click="openRequestModal = true" class="text-xs font-bold text-indigo-600 underline">
                                    Request Admin for More Tickets
                                </button>
                            @endif
                        </div>
                    @endif
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
