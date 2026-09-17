@extends('layouts.app')

@section('title', 'Applicant Review - ' . $project->title)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-12" x-data="{ openModal: false, selectedTicket: null, actionType: '' }">

    {{-- Navigation & Header --}}
    <div class="mb-6">
        <a href="{{ route('projects.vacancies', $project->id) }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-gray-800 transition mb-2">
            ← Back to Vacancies
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Applicant Tickets</h1>
        <p class="text-xs text-gray-500">Review, accept, or decline applications for {{ $project->title }}.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- Applicants Table --}}
    @if($tickets->count() > 0)
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
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
        </div>

        <div class="mt-4">
            {{ $tickets->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center my-8">
            <h3 class="text-sm font-bold text-gray-800">No applicants yet</h3>
            <p class="text-xs text-gray-500 mt-1">There are currently no submitted tickets for position vacancies.</p>
        </div>
    @endif

    {{-- Review Explanation Dialog / Modal --}}
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
</div>
@endsection
