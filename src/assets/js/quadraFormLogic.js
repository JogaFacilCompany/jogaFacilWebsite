// assets/js/quadraFormLogic.js – camelCase enforced

(() => {
    const horariosContainer = document.getElementById('horariosContainer');
    const addHorarioBtn     = document.getElementById('addHorarioBtn');
    const cnpjInput         = document.getElementById('inputCnpj');

    if (!horariosContainer || !addHorarioBtn) return;

    const diasSemana = [
        { value: 0, label: 'Domingo' },
        { value: 1, label: 'Segunda' },
        { value: 2, label: 'Terça' },
        { value: 3, label: 'Quarta' },
        { value: 4, label: 'Quinta' },
        { value: 5, label: 'Sexta' },
        { value: 6, label: 'Sábado' },
    ];

    let horarioIndex = 0;

    const createHorarioRow = () => {
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 align-items-end horarioRow';
        row.innerHTML = `
            <div class="col-md-3">
                <select class="form-select formInput" name="horarios[${horarioIndex}][diaSemana]" required>
                    <option value="">Dia</option>
                    ${diasSemana.map(d => `<option value="${d.value}">${d.label}</option>`).join('')}
                </select>
            </div>
            <div class="col-md-3">
                <input type="time" class="form-control formInput" name="horarios[${horarioIndex}][horaInicio]" required>
            </div>
            <div class="col-md-3">
                <input type="time" class="form-control formInput" name="horarios[${horarioIndex}][horaFim]" required>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control formInput" name="horarios[${horarioIndex}][preco]" placeholder="Preço" step="0.01" min="0">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger btn-sm w-100 removeHorarioBtn" aria-label="Remover horário">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        horarioIndex++;
        horariosContainer.appendChild(row);

        row.querySelector('.removeHorarioBtn').addEventListener('click', () => {
            row.remove();
        });
    };

    addHorarioBtn.addEventListener('click', createHorarioRow);

    // Start with one row
    createHorarioRow();

    // ---- CNPJ Mask -------------------------------------------------------
    if (cnpjInput) {
        cnpjInput.addEventListener('input', (e) => {
            let raw = e.target.value.replace(/\D/g, '').substring(0, 14);

            if (raw.length > 12) {
                raw = raw.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2})/, '$1.$2.$3/$4-$5');
            } else if (raw.length > 8) {
                raw = raw.replace(/(\d{2})(\d{3})(\d{3})(\d{0,4})/, '$1.$2.$3/$4');
            } else if (raw.length > 5) {
                raw = raw.replace(/(\d{2})(\d{3})(\d{0,3})/, '$1.$2.$3');
            } else if (raw.length > 2) {
                raw = raw.replace(/(\d{2})(\d{0,3})/, '$1.$2');
            }

            e.target.value = raw;
        });
    }
})();
