// assets/js/arenaDetailLogic.js – camelCase enforced

(() => {
    // ---- Slot Data ---------------------------------------------------
    const slotsData = {
        manha: [
            { id: 's1', startTime: '08:00', price: 150, isAvailable: true },
            { id: 's2', startTime: '09:00', price: 150, isAvailable: false },
            { id: 's3', startTime: '10:00', price: 150, isAvailable: true },
            { id: 's4', startTime: '11:00', price: 150, isAvailable: true },
        ],
        tarde: [
            { id: 's5', startTime: '13:00', price: 180, isAvailable: true },
            { id: 's6', startTime: '14:00', price: 180, isAvailable: true },
            { id: 's7', startTime: '15:00', price: 180, isAvailable: false },
            { id: 's8', startTime: '16:00', price: 180, isAvailable: true },
            { id: 's9', startTime: '17:00', price: 180, isAvailable: true },
        ],
        noite: [
            { id: 's10', startTime: '19:00', price: 200, isAvailable: true },
            { id: 's11', startTime: '20:00', price: 200, isAvailable: true },
            { id: 's12', startTime: '21:00', price: 200, isAvailable: false },
            { id: 's13', startTime: '22:00', price: 200, isAvailable: true },
        ],
    };

    // ---- State -------------------------------------------------------
    let activePeriod    = 'manha';
    let selectedSlotId  = null;
    let isLobbyMode     = false;

    // ---- Elements ----------------------------------------------------
    const slotsGrid      = document.getElementById('slotsGrid');
    const confirmBtn     = document.getElementById('confirmBtn');
    const lobbyToggle    = document.getElementById('lobbyToggle');
    const lobbyRadioInner = document.getElementById('lobbyRadioInner');
    const periodTabs     = document.querySelectorAll('.periodTab');

    // ---- Render slots ------------------------------------------------
    const renderSlots = () => {
        slotsGrid.innerHTML = '';
        slotsData[activePeriod].forEach(slot => {
            const slotBtn = document.createElement('button');
            slotBtn.className = 'slotBtn';
            slotBtn.disabled  = !slot.isAvailable;
            slotBtn.innerHTML = `<div>${slot.startTime}</div><div class="slotPrice">R$ ${slot.price}</div>`;

            if (!slot.isAvailable) {
                slotBtn.style.opacity   = '0.4';
                slotBtn.style.cursor    = 'not-allowed';
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
        confirmBtn.onclick           = () => {
            alert(`Reserva confirmada!\nHorário: ${selectedSlot.startTime}\nPreço: R$ ${selectedSlot.price}\nModo Lobby: ${isLobbyMode ? 'Ativado' : 'Desativado'}`);
        };
    };

    // ---- Period tabs -------------------------------------------------
    periodTabs.forEach(tabEl => {
        tabEl.addEventListener('click', () => {
            activePeriod   = tabEl.getAttribute('data-period');
            selectedSlotId = null;
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
        });
    }

    // ---- Initial render ----------------------------------------------
    if (slotsGrid) {
        renderSlots();
    }
})();
