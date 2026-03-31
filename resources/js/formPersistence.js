export function initFormPersistence({
    formSelector,
    uploadFormSelector,
    titleSelector,
    contentSelector,
    localStoragePrefix
}) {
    const uploadForm = document.querySelector(uploadFormSelector);
    const form = document.querySelector(formSelector);
    const title = document.getElementById(titleSelector);
    const content = document.getElementById(contentSelector);

    // Restore from localStorage
    if (title && localStorage.getItem(localStoragePrefix + '_title')) {
        title.value = localStorage.getItem(localStoragePrefix + '_title');
    }
    if (content && localStorage.getItem(localStoragePrefix + '_content')) {
        content.value = localStorage.getItem(localStoragePrefix + '_content');
    }

    const syncHidden = () => {
        if (!uploadForm || !title || !content) return;
        const hiddenTitle = uploadForm.querySelector('input[name="title"]');
        const hiddenContent = uploadForm.querySelector('input[name="content"]');
        if (hiddenTitle) hiddenTitle.value = title.value;
        if (hiddenContent) hiddenContent.value = content.value;
    };

    title?.addEventListener('input', () => {
        localStorage.setItem(localStoragePrefix + '_title', title.value);
    });
    content?.addEventListener('input', () => {
        localStorage.setItem(localStoragePrefix + '_content', content.value);
    });

    uploadForm?.addEventListener('submit', function() {
        syncHidden();
        localStorage.setItem(localStoragePrefix + '_title', title.value);
        localStorage.setItem(localStoragePrefix + '_content', content.value);
    });

    form?.addEventListener('submit', function() {
        localStorage.removeItem(localStoragePrefix + '_title');
        localStorage.removeItem(localStoragePrefix + '_content');
    });
}

export function clearForm(titleSelector, contentSelector, localStoragePrefix) {
    const title = document.getElementById(titleSelector);
    const content = document.getElementById(contentSelector);
    if (title) title.value = '';
    if (content) content.value = '';
    localStorage.removeItem(localStoragePrefix + '_title');
    localStorage.removeItem(localStoragePrefix + '_content');
}

export function copyToClipboard(elementId) {
    const codeElement = document.getElementById(elementId);
    const text = codeElement.textContent || codeElement.innerText;
    navigator.clipboard.writeText(text).catch(err => {
        console.error('Failed to copy: ', err);
    });
}
