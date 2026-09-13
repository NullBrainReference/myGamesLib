<div id="mechanicModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4 transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6 relative border border-gray-100">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-100">
            <h3 id="mechanicModalTitle" class="text-base font-bold text-gray-900 flex items-center gap-2">
                <span class="p-1.5 bg-amber-100 text-amber-700 rounded-lg">⚙️</span>
                <span>Select or Propose Mechanic</span>
            </h3>
            <button type="button" onclick="closeCreateMechanicModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold leading-none">&times;</button>
        </div>

        <!-- STEP 1: Search & Lookup -->
        <div id="mechanicStepSearch" class="space-y-4">
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                        Search Existing Mechanics
                    </label>
                    <span class="text-[10px] text-gray-400">Syntax: <code>mechanic:game</code></span>
                </div>
                <input type="text" 
                       id="mechanicSearchInput" 
                       oninput="handleMechanicSearch(this.value)"
                       class="w-full rounded-lg border-gray-300 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" 
                       placeholder="e.g. cra:mine, :mine, or Crafting" 
                       autocomplete="off">
            </div>

            <!-- Search Results Container -->
            <div id="mechanicSearchResults" class="max-h-56 overflow-y-auto space-y-1.5 border rounded-lg p-2 bg-gray-50 text-sm hidden divide-y divide-gray-100">
                <!-- Results populated via JS -->
            </div>

            <div id="mechanicSearchEmpty" class="text-xs text-gray-500 text-center py-3">
                Type <code>Crafting:Minecart</code> to search mechanic by game, or <code>:mine</code> for game mechanics.
            </div>

            <div class="pt-3 border-t border-gray-100 flex justify-between items-center">
                <span class="text-xs text-gray-500">Not found in database?</span>
                <button type="button" 
                        onclick="switchMechanicStep('create')" 
                        class="px-3 py-1.5 bg-amber-500 text-white text-xs font-bold rounded-lg hover:bg-amber-600 shadow-sm transition-colors">
                    + Create New Mechanic
                </button>
            </div>
        </div>

        <!-- STEP 2: Create / Propose Variant Form -->
        <form id="mechanicModalForm" action="" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="comment_id" id="mechanicModalCommentId" value="">
            <input type="hidden" name="parent_id" id="mechanicModalParentId" value="">
            <input type="hidden" name="game_id" id="mechanicModalGameId" value="">
            <input type="hidden" name="project_id" id="mechanicModalProjectId" value="">

            <div id="selectedParentBadge" class="hidden mb-3 p-2 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800 flex justify-between items-center">
                <span>Proposing variant for: <strong id="parentTitleText"></strong></span>
                <button type="button" onclick="clearSelectedParent()" class="text-amber-600 hover:text-amber-900 font-bold">&times;</button>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                    Mechanic Title <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       id="mechanicTitleInput"
                       required 
                       class="w-full rounded-lg border-gray-300 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" 
                       placeholder="e.g. Double Jump with Cooldown">
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                    Description & Suggestion <span class="text-red-500">*</span>
                </label>
                <textarea name="content" 
                          id="mechanicContentInput"
                          rows="4" 
                          required 
                          class="w-full rounded-lg border-gray-300 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" 
                          placeholder="Describe how the mechanic works or your proposed changes..."></textarea>
            </div>

            <div class="flex justify-between items-center text-xs">
                <button type="button" 
                        onclick="switchMechanicStep('search')" 
                        class="text-gray-500 hover:text-gray-700 underline font-medium">
                    &larr; Back to Search
                </button>

                <div class="flex gap-2">
                    <button type="button" 
                            onclick="closeCreateMechanicModal()" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-amber-500 text-white font-bold rounded-lg hover:bg-amber-600 shadow-sm transition-colors flex items-center gap-1.5">
                        <span id="mechanicSubmitBtnText">Submit Proposal</span> &rarr;
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>

<script>
let searchDebounceTimer;

window.openCreateMechanicModal = function(actionUrl, commentId = null, context = {}) {
    const modal = document.getElementById('mechanicModal');
    const form = document.getElementById('mechanicModalForm');
    
    if (form && modal) {
        form.setAttribute('action', actionUrl);
        document.getElementById('mechanicModalCommentId').value = commentId || '';
        document.getElementById('mechanicModalGameId').value = context.game_id || '';
        document.getElementById('mechanicModalProjectId').value = context.project_id || '';
        
        clearSelectedParent();
        switchMechanicStep('search');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
};

window.closeCreateMechanicModal = function() {
    const modal = document.getElementById('mechanicModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
};

window.switchMechanicStep = function(step) {
    const searchStep = document.getElementById('mechanicStepSearch');
    const createForm = document.getElementById('mechanicModalForm');
    
    if (step === 'search') {
        searchStep.classList.remove('hidden');
        createForm.classList.add('hidden');
    } else {
        searchStep.classList.add('hidden');
        createForm.classList.remove('hidden');
    }
};

window.handleMechanicSearch = function(query) {
    clearTimeout(searchDebounceTimer);
    const resultsContainer = document.getElementById('mechanicSearchResults');
    const emptyMsg = document.getElementById('mechanicSearchEmpty');

    if (query.trim().length < 1) {
        resultsContainer.classList.add('hidden');
        emptyMsg.textContent = 'Type cra:mine to search mechanic by game, or :mine for game mechanics.';
        emptyMsg.classList.remove('hidden');
        return;
    }

    searchDebounceTimer = setTimeout(() => {
        const gameId = document.getElementById('mechanicModalGameId').value;
        const projectId = document.getElementById('mechanicModalProjectId').value;

        fetch(`/mechanics/search?query=${encodeURIComponent(query)}&game_id=${gameId}&project_id=${projectId}`)
            .then(res => res.json())
            .then(data => {
                resultsContainer.innerHTML = '';
                if (data.length === 0) {
                    resultsContainer.classList.add('hidden');
                    emptyMsg.textContent = 'No matching mechanics found. You can create a new one below.';
                    emptyMsg.classList.remove('hidden');
                } else {
                    emptyMsg.classList.add('hidden');
                    resultsContainer.classList.remove('hidden');
                    
                    data.forEach(item => {
                        const gameBadge = item.game 
                            ? `<span class="bg-indigo-100 text-indigo-700 text-[10px] font-bold px-1.5 py-0.5 rounded border border-indigo-200">🎮 ${item.game.title}</span>` 
                            : `<span class="bg-gray-100 text-gray-600 text-[10px] font-semibold px-1.5 py-0.5 rounded border border-gray-200">Generic</span>`;

                        const div = document.createElement('div');
                        div.className = 'pt-2 pb-2 px-1 hover:bg-white rounded cursor-pointer transition-colors flex justify-between items-center gap-2';
                        div.innerHTML = `
                            <div class="overflow-hidden">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="font-bold text-gray-800 text-xs">${item.title}</span>
                                    ${gameBadge}
                                </div>
                                <div class="text-[11px] text-gray-500 truncate max-w-xs">${item.content || ''}</div>
                            </div>
                            <button type="button" class="shrink-0 text-[11px] bg-amber-100 text-amber-800 px-2.5 py-1 rounded font-semibold hover:bg-amber-200 transition-colors">
                                Propose Variant &rarr;
                            </button>
                        `;
                        div.onclick = () => selectParentMechanic(item);
                        resultsContainer.appendChild(div);
                    });
                }
            });
    }, 250);
};

window.selectParentMechanic = function(mechanic) {
    document.getElementById('mechanicModalParentId').value = mechanic.mechanic_id;
    document.getElementById('mechanicTitleInput').value = mechanic.title;
    document.getElementById('parentTitleText').textContent = mechanic.title + (mechanic.game ? ` (${mechanic.game.title})` : '');
    document.getElementById('selectedParentBadge').classList.remove('hidden');
    document.getElementById('mechanicSubmitBtnText').textContent = 'Submit Variant Proposal';
    
    switchMechanicStep('create');
};

window.clearSelectedParent = function() {
    document.getElementById('mechanicModalParentId').value = '';
    document.getElementById('mechanicTitleInput').value = '';
    document.getElementById('mechanicContentInput').value = '';
    document.getElementById('selectedParentBadge').classList.add('hidden');
    document.getElementById('mechanicSubmitBtnText').textContent = 'Submit New Mechanic';
};
</script>