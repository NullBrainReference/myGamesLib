@props(['game'])

<div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden mb-6">
    <div class="bg-gray-50 border-b border-gray-200 py-3 px-4 flex items-center justify-between">
        <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 4H4a2 2 0 00-2 2v12a2 2 0 002 2h7M13 4h7a2 2 0 012 2v12a2 2 0 01-2 2h-7M11 4v16"></path>
            </svg>
            Core Gameplay Mechanics
        </h3>

        <div class="flex items-center gap-2">
            @auth
                @if(Auth::user()->isAdmin())
                    <button type="button"
                            class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-semibold shadow-sm transition-colors"
                            onclick="openCreateMechanicModal('{{ route('mechanics.store', ['game_id' => $game->game_id]) }}')">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                        Add Global System
                    </button>
                @endif
            @endauth
            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                {{ $game->mechanics->count() }} Systems Registered
            </span>
        </div>
    </div>

    <div class="p-4">
        @if($game->mechanics->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($game->mechanics as $mechanic)
                    <div class="group border border-gray-100 bg-gray-50 hover:bg-white hover:border-blue-200 rounded-lg p-3.5 transition-all duration-200 shadow-none hover:shadow-sm flex flex-col justify-between">
                        <div class="flex items-start gap-2.5">
                            <span class="mt-1 flex h-2 w-2 relative top-0.5 shrink-0 rounded-full bg-blue-500 group-hover:scale-125 transition-transform"></span>
                            <div class="w-full">
                                <div class="flex justify-between items-start gap-2 mb-1">
                                    <h4 class="font-bold text-gray-900 text-sm group-hover:text-blue-600 transition-colors">
                                        {{ $mechanic->title }}
                                    </h4>

                                    @auth
                                        @if(Auth::user()->isAdmin())
                                            <div class="opacity-40 group-hover:opacity-100 transition-opacity flex items-center gap-1.5 shrink-0 bg-white shadow-sm border border-gray-200 rounded px-1 py-0.5">
                                                <button type="button" class="text-xs text-amber-600 hover:text-amber-700 font-medium px-1 hover:underline"
                                                        onclick="openEditMechanicModal('{{ route('mechanics.update', $mechanic->mechanic_id) }}', '{{ addslashes($mechanic->title) }}', '{{ addslashes($mechanic->content) }}')">
                                                    Edit
                                                </button>
                                                <span class="text-gray-300 text-xs">|</span>
                                                <form action="{{ route('mechanics.destroy', $mechanic->mechanic_id) }}" method="POST" class="inline m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium px-1 hover:underline"
                                                            onclick="return confirm('Completely clear this mechanic registry node across all catalog tables?')">
                                                        Wipe
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    @endauth
                                </div>
                                <p class="text-xs text-gray-600 leading-relaxed" style="white-space: pre-line;">
                                    {{ $mechanic->content }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-6 text-gray-400 text-xs border border-dashed border-gray-200 rounded-lg bg-gray-50">
                No structural core gameplay mechanics have been indexed for this record module yet.
            </div>
        @endif
    </div>
</div>

@auth
    @if(Auth::user()->isAdmin())
        @push('scripts')
        <script>
            window.MechanicManager = {
                modal: document.getElementById('mechanicModal'),
                form: document.getElementById('mechanicForm'),
                titleText: document.getElementById('mechanicModalTitle'),
                methodContainer: document.getElementById('mechanicFormMethod'),
                inputTitle: document.getElementById('mechanic_title'),
                inputContent: document.getElementById('mechanic_content'),

                openCreate(actionRoute) {
                    if (!this.modal) return;
                    this.form.reset();
                    this.form.action = actionRoute;
                    this.titleText.innerText = "Append Global Platform Classification Mechanic";
                    this.methodContainer.innerHTML = "";
                    this.modal.classList.remove('hidden');
                },

                openEdit(actionRoute, currentTitle, currentContent) {
                    if (!this.modal) return;
                    this.form.action = actionRoute;
                    this.titleText.innerText = "Modify Operational System Parameters";
                    this.methodContainer.innerHTML = `<input type="hidden" name="_method" value="PUT">`;

                    this.inputTitle.value = currentTitle;
                    this.inputContent.value = currentContent;
                    this.modal.classList.remove('hidden');
                },

                close() {
                    if (!this.modal) return;
                    this.modal.classList.add('hidden');
                }
            };

            // 💡 Global bridges: This connects your existing HTML onclick triggers to the Manager methods
            window.openCreateMechanicModal = function(actionRoute) {
                window.MechanicManager.openCreate(actionRoute);
            };

            window.openEditMechanicModal = function(actionRoute, title, content) {
                window.MechanicManager.openEdit(actionRoute, title, content);
            };

            window.closeMechanicModal = function() {
                window.MechanicManager.close();
            };
        </script>
        @endpush
    @endif
@endauth
