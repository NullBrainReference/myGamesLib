@extends('layouts.app')

@section('title', 'Vacancies - ' . $project->title)

@section('content')
<div x-data="{ 
    openTicketModal: false, 
    openSlotModal: false, 
    selectedPositionId: '', 
    selectedPositionTitle: '' 
}" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-12">

    {{-- Header & Navigation Bar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <a href="{{ route('projects.view', $project->id) }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-gray-800 transition-colors mb-2">
                ← Back to {{ $project->title }}
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Open Vacancies</h1>
            <p class="text-xs text-gray-500">Apply for positions to join the development team.</p>
        </div>

        {{-- Owner & Admin Action Buttons --}}
        <div class="flex flex-wrap items-center gap-2">
            @auth
                @if(auth()->user()->isAdmin() || $project->owners->contains(auth()->id()))
                    {{-- Request Position Slots Button --}}
                    <button type="button"
                            @click="openSlotModal = true"
                            class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center gap-1.5">
                        ➕ Request Position Slots
                    </button>

                    {{-- Review Applicants Button --}}
                    <a href="{{ route('projects.vacancies.applicants', $project->id) }}"
                       class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center gap-1.5">
                        📋 Review Applicants
                    </a>
                @endif

                {{-- Admin Debug Generator --}}
                @if(auth()->user()->isAdmin())
                    <form action="{{ route('projects.debug-vacancies', $project->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Generate 3 sample vacancies (Dev, Artist, Designer)?');"
                                class="px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center gap-1">
                            🛠️ Debug: Seed 3 Vacancies
                        </button>
                    </form>
                @endif
            @endauth
        </div>
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
                <div class="p-4 border border-gray-200 rounded-lg bg-white shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start gap-2 mb-2">
                            <h3 class="font-bold text-gray-900 text-sm">{{ $position->title }}</h3>

                            {{-- Ticket Capacity Badge --}}
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full shrink-0 {{ $position->remaining_tickets > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                {{ $position->remaining_tickets }} / {{ $position->tickets_amount }} Tickets
                            </span>
                        </div>

                        <p class="text-xs text-gray-600 mb-4 line-clamp-3">{{ $position->description }}</p>
                    </div>

                    <div>
                        @if($position->hasAvailableTickets())
                            {{-- Apply Form --}}
                            <form action="{{ route('positions.apply', $position->id) }}" method="POST" class="mt-2">
                                @csrf
                                <textarea name="reason" rows="2" required placeholder="Why are you applying..." class="w-full text-xs p-2.5 border border-gray-300 rounded-lg mb-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"></textarea>
                                <button type="submit" class="w-full sm:w-auto px-3 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition-colors">
                                    Apply (1 Ticket)
                                </button>
                            </form>
                        @else
                            {{-- Out of Capacity Alert --}}
                            <div class="p-3 bg-amber-50 border border-amber-200 text-amber-900 text-xs rounded-lg flex flex-col gap-1.5">
                                <span class="font-medium">All application tickets spent for this position.</span>

                                @if($project->owners->contains(auth()->id()) || auth()->user()->isAdmin())
                                    <button type="button" 
                                            @click="openTicketModal = true; selectedPositionId = '{{ $position->id }}'; selectedPositionTitle = '{{ e($position->title) }}'" 
                                            class="text-xs font-bold text-indigo-600 hover:text-indigo-800 underline text-left transition-colors">
                                        Request Admin for More Tickets →
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center my-8 shadow-sm">
            <h3 class="text-sm font-bold text-gray-800">No open vacancies yet</h3>
            <p class="text-xs text-gray-500 mt-1 mb-4">This project is not currently recruiting new team members.</p>
            
            @auth
                @if(auth()->user()->isAdmin() || $project->owners->contains(auth()->id()))
                    <button type="button"
                            @click="openSlotModal = true"
                            class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors gap-1.5">
                        ➕ Request Position Slots from Admin
                    </button>
                @endif
            @endauth
        </div>
    @endif

    {{-- MODAL 1: Request Additional Tickets for Existing Position --}}
    <div x-show="openTicketModal" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="openTicketModal = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         role="dialog" 
         aria-modal="true">
        
        <div @click.away="openTicketModal = false"
             x-show="openTicketModal"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             class="w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden text-left">
            
            <form action="{{ route('projects.request-tickets', $project->id) }}" method="POST">
                @csrf
                
                <div class="px-6 pt-6 pb-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Request Additional Tickets</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Extend application capacity for an existing position</p>
                    </div>
                    <button type="button" @click="openTicketModal = false" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-1 rounded-lg transition-colors text-lg leading-none">
                        ✕
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Target Position</label>
                        <select name="hire_position_id" x-model="selectedPositionId" required class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                            <option value="" disabled>-- Select Position --</option>
                            @foreach($positions as $pos)
                                <option value="{{ $pos->id }}">{{ $pos->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Additional Tickets Quantity (1-50)</label>
                        <input type="number" name="quantity" min="1" max="50" value="5" required class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Reason for Request</label>
                        <textarea name="reason" rows="3" minlength="10" maxlength="1000" required placeholder="Explain why you need additional ticket slots..." class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"></textarea>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row-reverse gap-2">
                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors text-center">
                        Send Request
                    </button>
                    <button type="button" @click="openTicketModal = false" class="w-full sm:w-auto px-4 py-2 bg-white border border-gray-300 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-100 transition-colors text-center">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL 2: Request New Position Slots (e.g., Developer, 3D Artist) --}}
    <div x-show="openSlotModal" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="openSlotModal = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         role="dialog" 
         aria-modal="true">
        
        <div @click.away="openSlotModal = false"
             x-show="openSlotModal"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             class="w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden text-left">
            
            <form action="{{ route('projects.request-position-slots', $project->id) }}" method="POST">
                @csrf
                
                <div class="px-6 pt-6 pb-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Request New Position Slots</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Submit new role request to platform admins for approval</p>
                    </div>
                    <button type="button" @click="openSlotModal = false" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-1 rounded-lg transition-colors text-lg leading-none">
                        ✕
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Requested Position Title</label>
                        <input type="text" name="title" required placeholder="e.g. Lead C++ Developer, 3D Character Artist" class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Quantity of Slots (1-20)</label>
                        <input type="number" name="quantity" min="1" max="20" value="1" required class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Reason / Scope for Request</label>
                        <textarea name="reason" rows="3" minlength="10" maxlength="1000" required placeholder="Explain why your project requires this new position slot..." class="w-full text-xs p-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"></textarea>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row-reverse gap-2">
                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors text-center">
                        Submit Slot Request
                    </button>
                    <button type="button" @click="openSlotModal = false" class="w-full sm:w-auto px-4 py-2 bg-white border border-gray-300 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-100 transition-colors text-center">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection