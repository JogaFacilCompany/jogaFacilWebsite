// assets/js/dashboardLocadorLogic.js – camelCase enforced

document.addEventListener('DOMContentLoaded', () => {
    const slotsContainer = document.getElementById('slotsContainer');
    if (!slotsContainer) return;

    const horarios         = JSON.parse(slotsContainer.dataset.horarios || '{}');
    let selectedSlot       = null;
    let activeTab          = 'Manhã';

    function renderSlots() {
        slotsContainer.innerHTML = '';
        const currentSlots = horarios[activeTab] || [];

        currentSlots.forEach(hora => {
            const slotBtn       = document.createElement('button');
            slotBtn.className   = `slotBtn ${selectedSlot === hora ? 'selected' : ''}`;
            slotBtn.innerHTML   = `${hora}<div class="slotPrice">R$ 150</div>`;
            slotBtn.onclick     = () => { selectedSlot = hora; renderSlots(); updateActionButton(); };
            slotsContainer.appendChild(slotBtn);
        });
    }

    function selectTab(tabName) {
        activeTab    = tabName;
        selectedSlot = null;

        document.querySelectorAll('.periodTab').forEach(tabEl => {
            tabEl.classList.toggle('active', tabEl.textContent === tabName);
        });

        renderSlots();
        updateActionButton();
    }

    function updateActionButton() {
        const actionBtn = document.getElementById('btnSalvarEstado');
        if (!actionBtn) return;

        actionBtn.textContent = selectedSlot
            ? `Bloquear / Reservar as ${selectedSlot}`
            : 'Selecione um horário';
        actionBtn.classList.toggle('enabled', !!selectedSlot);
    }

    // Bind period tabs
    document.querySelectorAll('.periodTab').forEach(tabEl => {
        tabEl.addEventListener('click', () => selectTab(tabEl.textContent));
    });

    renderSlots();
});
