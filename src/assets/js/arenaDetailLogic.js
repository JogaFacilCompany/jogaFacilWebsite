// assets/js/arenaDetailLogic.js – camelCase enforced

(() => {
    let activePeriod    = 'manha';
    let selectedSlotId  = null;
    let isLobbyMode     = false;

    const slotsGrid         = document.getElementById('slotsGrid');
    const confirmBtn        = document.getElementById('confirmBtn');
    const lobbyToggle       = document.getElementById('lobbyToggle');
    const lobbyOptionsPanel = document.getElementById('lobbyOptionsPanel');
    const lobbyCodeField    = document.getElementById('lobbyCodeField');
    const codigoAcessoInput = document.getElementById('codigoAcessoInput');
    const visibilidadeRadios = document.querySelectorAll('input[name="visibilidade_lobby"]');
    const selectedHorarioId = document.getElementById('selectedHorarioId');
    const selectedModoLobby = document.getElementById('selectedModoLobby');
    const periodTabs        = document.querySelectorAll('.periodTab');
    const slotsData         = slotsGrid ? JSON.parse(slotsGrid.dataset.slots || '{}') : {};

    const formatPrice = (price) => Number(price).toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

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

    const updateConfirmButton = (selectedSlot) => {
        confirmBtn.disabled          = false;
        confirmBtn.className         = 'bookingConfirmBtn enabled';
        confirmBtn.textContent       = `Confirmar Reserva – ${selectedSlot.startTime}`;
        selectedHorarioId.value      = selectedSlot.id;
    };

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

    const updateLobbyVisibilityUi = () => {
        const isPrivate = document.querySelector('input[name="visibilidade_lobby"]:checked')?.value === 'privado';
        if (lobbyCodeField) {
            lobbyCodeField.classList.toggle('d-none', !isPrivate);
        }
        if (codigoAcessoInput) {
            codigoAcessoInput.required = isPrivate && isLobbyMode;
            if (!isPrivate) {
                codigoAcessoInput.value = '';
            }
        }
    };

    if (lobbyToggle) {
        lobbyToggle.addEventListener('click', () => {
            isLobbyMode = !isLobbyMode;
            lobbyToggle.classList.toggle('active', isLobbyMode);
            selectedModoLobby.value = isLobbyMode ? '1' : '0';
            if (lobbyOptionsPanel) {
                lobbyOptionsPanel.classList.toggle('d-none', !isLobbyMode);
            }
            updateLobbyVisibilityUi();
        });
    }

    visibilidadeRadios.forEach(radioEl => {
        radioEl.addEventListener('change', updateLobbyVisibilityUi);
    });

    if (slotsGrid) {
        renderSlots();
    }
})();
