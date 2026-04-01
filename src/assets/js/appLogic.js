// assets/js/appLogic.js – camelCase enforced

document.addEventListener('DOMContentLoaded', () => {

    // --- Search Handler ---
    const searchButton = document.getElementById('searchButton');
    const searchInput  = document.getElementById('searchInput');

    const handleSearchEvent = () => {
        const queryTerm = searchInput ? searchInput.value.trim() : '';
        if (queryTerm) {
            console.log(`Searching for: ${queryTerm}`);
        }
    };

    if (searchButton) searchButton.addEventListener('click', handleSearchEvent);
    if (searchInput) {
        searchInput.addEventListener('keypress', (eventObject) => {
            if (eventObject.key === 'Enter') { eventObject.preventDefault(); handleSearchEvent(); }
        });
    }

    // --- CPF Mask ---
    const cpfInput = document.getElementById('inputCpf');
    if (cpfInput) {
        cpfInput.addEventListener('input', (eventObject) => {
            let rawValue = eventObject.target.value.replace(/\D/g, '');
            rawValue = rawValue.substring(0, 11);

            if (rawValue.length > 9) {
                rawValue = rawValue.replace(/(\d{3})(\d{3})(\d{3})(\d{0,2})/, '$1.$2.$3-$4');
            } else if (rawValue.length > 6) {
                rawValue = rawValue.replace(/(\d{3})(\d{3})(\d{0,3})/, '$1.$2.$3');
            } else if (rawValue.length > 3) {
                rawValue = rawValue.replace(/(\d{3})(\d{0,3})/, '$1.$2');
            }

            eventObject.target.value = rawValue;
        });
    }

    // --- Bootstrap Form Validation ---
    const allForms = document.querySelectorAll('form[novalidate]');
    allForms.forEach(formElement => {
        formElement.addEventListener('submit', (eventObject) => {
            if (!formElement.checkValidity()) {
                eventObject.preventDefault();
                eventObject.stopPropagation();
            }
            formElement.classList.add('was-validated');
        });
    });

    // --- Edit Modal: populate fields ---
    const editButtons = document.querySelectorAll('.editarBtn');
    editButtons.forEach(buttonElement => {
        buttonElement.addEventListener('click', () => {
            const targetId    = buttonElement.getAttribute('data-id');
            const targetNome  = buttonElement.getAttribute('data-nome');
            const targetEmail = buttonElement.getAttribute('data-email');

            const editarId    = document.getElementById('editarId');
            const editarNome  = document.getElementById('editarNome');
            const editarEmail = document.getElementById('editarEmail');

            if (editarId)    editarId.value    = targetId;
            if (editarNome)  editarNome.value  = targetNome;
            if (editarEmail) editarEmail.value = targetEmail;
        });
    });

    // --- Delete Confirmation ---
    const deleteForms = document.querySelectorAll('.deleteForm');
    deleteForms.forEach(formElement => {
        formElement.addEventListener('submit', (eventObject) => {
            const isConfirmed = confirm('Tem certeza que deseja remover este usuário?');
            if (!isConfirmed) eventObject.preventDefault();
        });
    });

});
