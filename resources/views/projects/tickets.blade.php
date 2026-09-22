@extends('layouts.app')

@section('title', 'Applicant Review - ' . $project->title)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-12" x-data="{ openModal: false, openRequestModal: false, selectedTicket: null, actionType: '' }">

    {{-- Navigation & Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('projects.vacancies', $project->id) }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-gray-800 transition mb-2">
                ← Back to Vacancies
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Applicant Tickets</h1>
            <p class="text-xs text-gray-500">Review, accept, or decline applications for {{ $project->title }}.</p>
        </div>

        {{-- Request Ticket Expansion Button --}}
        <div>
            <button type="button"
                    @click="openRequestModal = true"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Request Slot Extension
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- Applicants Main Card --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">

        {{-- Card Header & Filter Bar (Single Source of Truth) --}}
        <div class="p-4 border-b border-gray-200 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-2">
                <h2 class="text-sm font-bold text-gray-800">Submitted Applications</h2>
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-indigo-100 text-indigo-700">
                    {{ $tickets->total() }} Total
                </span>
            </div>

            <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2 flex-nowrap shrink-0">
                <label for="filter_position" class="text-xs font-medium text-gray-600 whitespace-nowrap shrink-0">
                    Filter Position:
                </label>

                {{-- Zero-Jump Select Wrapper --}}
                <div class="relative flex items-center shrink-0">
                    <select id="filter_position"
                            name="position_id"
                            onchange="this.form.submit()"
                            class="appearance-none text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 py-1.5 pl-3 pr-8 bg-white text-gray-700 font-medium shadow-sm outline-none cursor-pointer w-[180px] sm:w-[220px] truncate">
                        <option value="">All Positions</option>
                        @foreach($project->hirePositions as $pos)
                            <option value="{{ $pos->id }}" {{ request('position_id') == $pos->id ? 'selected' : '' }}>
                                {{ $pos->title }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Integrated Absolute Control Icon (No Layout Shifts) --}}
                    <div class="absolute right-2 flex items-center">
                        @if(request('position_id'))
                            <a href="{{ url()->current() }}"
                               title="Clear position filter"
                               class="p-0.5 text-gray-400 hover:text-red-600 hover:bg-gray-100 rounded-md transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                        @else
                            <div class="pointer-events-none text-gray-400 pr-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- Table Content or Empty State --}}
        @if($tickets->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                            <th class="py-3 px-4">Applicant</th>
                            <th class="py-3 px-4">Position</th>
                            <th class="py-3 px-4">Motivation / Pitch</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs">
                        @foreach($tickets as $ticket)
                            <tr class="hover:bg-gray-50/50 transition">
                                {{-- User Info --}}
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <a href="{{ route('profile.view', $ticket->user->id) }}" class="font-bold text-gray-900 hover:text-indigo-600 transition flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-xs">
                                            {{ strtoupper(substr($ticket->user->name, 0, 1)) }}
                                        </div>
                                        <span>{{ $ticket->user->name }}</span>
                                    </a>
                                    <span class="text-[10px] text-gray-400 block ml-9">{{ $ticket->created_at->diffForHumans() }}</span>
                                </td>

                                {{-- Position --}}
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="font-semibold text-gray-800">{{ $ticket->position->title }}</span>
                                </td>

                                {{-- Motivation / Reason --}}
                                <td class="py-3 px-4 max-w-xs">
                                    <p class="text-gray-600 leading-relaxed truncate" title="{{ $ticket->reason }}">
                                        {{ $ticket->reason }}
                                    </p>
                                </td>

                                {{-- Status Badge --}}
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @if($ticket->success === true)
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            Accepted ✅
                                        </span>
                                    @elseif($ticket->success === false)
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-red-100 text-red-800 border border-red-300">
                                            Declined ❌
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-amber-100 text-amber-800 border border-amber-300">
                                            Pending ⏳
                                        </span>
                                    @endif

                                    @if($ticket->explanation)
                                        <p class="text-[10px] text-gray-500 mt-1 italic max-w-xs truncate" title="Note: {{ $ticket->explanation }}">
                                            Note: "{{ $ticket->explanation }}"
                                        </p>
                                    @endif
                                </td>

                                {{-- Action Buttons --}}
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button"
                                                @click="selectedTicket = {{ $ticket->id }}; actionType = 'accept'; openModal = true"
                                                class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-[11px] rounded border border-emerald-200 transition">
                                            Accept
                                        </button>
                                        <button type="button"
                                                @click="selectedTicket = {{ $ticket->id }}; actionType = 'decline'; openModal = true"
                                                class="px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-700 font-bold text-[11px] rounded border border-red-200 transition">
                                            Decline
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100">
                {{ $tickets->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <h3 class="text-sm font-bold text-gray-800">No applicants found</h3>
                <p class="text-xs text-gray-500 mt-1">There are currently no tickets matching your position filter.</p>
            </div>
        @endif
    </div>

    {{-- 1. Review Explanation Dialog / Modal --}}
    <div x-show="openModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        x-transition>
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-gray-200" @click.away="openModal = false">
            <h3 class="text-sm font-bold text-gray-900 mb-1" x-text="actionType === 'accept' ? 'Accept Application Ticket' : 'Decline Application Ticket'"></h3>
            <p class="text-xs text-gray-500 mb-4">Provide a reason or feedback for this decision.</p>

            <form x-bind:action="'/tickets/' + selectedTicket + '/review'" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="action" :value="actionType">

                <div>
                    <label class="block text-[11px] font-bold uppercase text-gray-700 mb-1">Reason / Feedback *</label>
                    <textarea name="reason" rows="3" required placeholder="Write the reason for this decision..."
                            class="w-full p-2.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="openModal = false" class="px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">
                        Cancel
                    </button>
                    <button type="submit"
                            :class="actionType === 'accept' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-red-600 hover:bg-red-700'"
                            class="px-4 py-1.5 text-white text-xs font-bold rounded-lg transition"
                            x-text="actionType === 'accept' ? 'Confirm Accept' : 'Confirm Decline'">
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 2. Request Extension Dialog / Modal --}}
    <div x-show="openRequestModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        x-transition>
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-gray-200" @click.away="openRequestModal = false">
            <h3 class="text-sm font-bold text-gray-900 mb-1">Request Ticket Slot Increase</h3>
            <p class="text-xs text-gray-500 mb-4">Submit a request to admins to expand available slots for a position.</p>

            <form action="{{ route('projects.request-tickets', $project->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold uppercase text-gray-700 mb-1">Target Position *</label>
                    <select name="hire_position_id" required class="w-full p-2.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="" disabled selected>Select position...</option>
                        @foreach($project->hirePositions as $pos)
                            <option value="{{ $pos->id }}">{{ $pos->title }} (Current Slots: {{ $pos->tickets_amount }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase text-gray-700 mb-1">Additional Slots Requested *</label>
                    <input type="number" name="quantity" min="1" max="50" value="1" required class="w-full p-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase text-gray-700 mb-1">Reason for Extension *</label>
                    <textarea name="reason" rows="3" required placeholder="Explain why more slots are required for this project position..." class="w-full p-2.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="openRequestModal = false" class="px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
