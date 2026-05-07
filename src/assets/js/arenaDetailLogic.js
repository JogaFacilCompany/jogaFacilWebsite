// assets/js/arenaDetailLogic.js – camelCase enforced

(() => {
    // ---- State -------------------------------------------------------
    let activePeriod    = 'manha';
    let selectedSlotId  = null;
    let isLobbyMode     = false;

    // ---- Elements ----------------------------------------------------
    const slotsGrid      = document.getElementById('slotsGrid');
    const confirmBtn     = document.getElementById('confirmBtn');
    const lobbyToggle    = document.getElementById('lobbyToggle');
    const selectedHorarioId = document.getElementById('selectedHorarioId');
    const selectedModoLobby = document.getElementById('selectedModoLobby');
    const periodTabs     = document.querySelectorAll('.periodTab');
    const slotsData      = slotsGrid ? JSON.parse(slotsGrid.dataset.slots || '{}') : {};

    const formatPrice = (price) => Number(price).toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    // ---- Render slots ------------------------------------------------
    const renderSlots = () => {
        slotsGrid.innerHTML = '';

        const periodSlots = slotsData[activePeriod] || [];
        if (!periodSlots.length) {
            slotsGrid.innerHTML = '<p class="small text-secondary mb-0">Nenhum horário neste período.</p>';
            return;
        }

        periodSlots.forEach(slot => {
            const slotBtn = document.createElement('button');
            slotBtn.className = 'slotBtn';
            slotBtn.type = 'button';
            slotBtn.disabled  = !slot.isAvailable;
            slotBtn.innerHTML = `<div>${slot.startTime}</div><div class="slotPrice">R$ ${formatPrice(slot.price)}</div>`;

            if (!slot.isAvailable) {
                slotBtn.classList.add('unavailable');
                slotBtn.innerHTML += '<div class="slotStatus">Ocupado</div>';
            } else if (selectedSlotId === slot.id) {
                slotBtn.classList.add('selected');
            }

            slotBtn.addEventListener('click', () => {
                if (!slot.isAvailable) return;
                selectedSlotId = slot.id;
                updateConfirmButton(slot);
                renderSlots();
            });

            slotsGrid.appendChild(slotBtn);
        });
    };

    // ---- Update confirm button ---------------------------------------
    const updateConfirmButton = (selectedSlot) => {
        confirmBtn.disabled          = false;
        confirmBtn.className         = 'bookingConfirmBtn enabled';
        confirmBtn.textContent       = `Confirmar Reserva – ${selectedSlot.startTime}`;
        selectedHorarioId.value      = selectedSlot.id;
    };

    // ---- Period tabs -------------------------------------------------
    periodTabs.forEach(tabEl => {
        tabEl.addEventListener('click', () => {
            activePeriod   = tabEl.getAttribute('data-period');
            selectedSlotId = null;
            selectedHorarioId.value = '';
            confirmBtn.disabled    = true;
            confirmBtn.className   = 'bookingConfirmBtn disabled';
            confirmBtn.textContent = 'Selecione um horário';
            periodTabs.forEach(t => t.classList.remove('active'));
            tabEl.classList.add('active');
            renderSlots();
        });
    });

    // ---- Lobby toggle ------------------------------------------------
    if (lobbyToggle) {
        lobbyToggle.addEventListener('click', () => {
            isLobbyMode = !isLobbyMode;
            lobbyToggle.classList.toggle('active', isLobbyMode);
            selectedModoLobby.value = isLobbyMode ? '1' : '0';
        });
    }

    // ---- Initial render ----------------------------------------------
    if (slotsGrid) {
        renderSlots();
    }
})();
