<div id="mechanicModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4 transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6 relative border border-gray-100">
        <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-100">
            <h3 id="mechanicModalTitle" class="text-base font-bold text-gray-900 flex items-center gap-2">
                <span class="p-1.5 bg-amber-100 text-amber-700 rounded-lg">⚙️</span>
                Propose Mechanic
            </h3>
            <button type="button" 
                    onclick="closeCreateMechanicModal()" 
                    class="text-gray-400 hover:text-gray-600 text-xl font-bold leading-none">&times;</button>
        </div>

        <form id="mechanicModalForm" action="" method="POST">
            @csrf
            <input type="hidden" name="comment_id" id="mechanicModalCommentId" value="">

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                    Mechanic Title <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="mechanic_title" 
                       required 
                       class="w-full rounded-lg border-gray-300 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" 
                       placeholder="e.g. Double Jump with Cooldown">
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                    Description & Comment <span class="text-red-500">*</span>
                </label>
                <textarea name="content" 
                          rows="4" 
                          required 
                          class="w-full rounded-lg border-gray-300 text-sm focus:border-amber-500 focus:ring-amber-500 shadow-sm" 
                          placeholder="Describe how the mechanic works and why it fits the project..."></textarea>
            </div>

            <div class="flex justify-end gap-2 text-xs">
                <button type="button" 
                        onclick="closeCreateMechanicModal()" 
                        class="px-4 py-2 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-amber-500 text-white font-bold rounded-lg hover:bg-amber-600 shadow-sm transition-colors flex items-center gap-1.5">
                    <span>Submit Proposal</span> &rarr;
                </button>
            </div>
        </form>
    </div>
</div>

<script>
window.openCreateMechanicModal = function(actionUrl, commentId = null, title = 'Propose Mechanic') {
    const modal = document.getElementById('mechanicModal');
    const form = document.getElementById('mechanicModalForm');
    const modalTitle = document.getElementById('mechanicModalTitle');
    const commentInput = document.getElementById('mechanicModalCommentId');

    if (form && modal) {
        form.setAttribute('action', actionUrl);
        if (modalTitle) modalTitle.innerHTML = `<span class="p-1.5 bg-amber-100 text-amber-700 rounded-lg">⚙️</span> ${title}`;
        if (commentInput) commentInput.value = commentId || '';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    } else {
        console.error('Modal or Form element missing in current DOM');
    }
};

window.closeCreateMechanicModal = function() {
    const modal = document.getElementById('mechanicModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
};
</script>