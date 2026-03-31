// assets/js/arenaDetailLogic.js – camelCase enforced

(() => {
    const config = window.arenaConfig;
    if (!config) return;

    // ---- Elements --------------------------------------------------------
    const datePicker     = document.getElementById('datePicker');
    const slotsGrid      = document.getElementById('slotsGrid');
    const slotsLoading   = document.getElementById('slotsLoading');
    const slotsEmpty     = document.getElementById('slotsEmpty');
    const confirmBtn     = document.getElementById('confirmBtn');
    const reservaForm    = document.getElementById('reservaForm');
    const formDataReserva = document.getElementById('formDataReserva');
    const formHoraInicio = document.getElementById('formHoraInicio');
    const formHoraFim    = document.getElementById('formHoraFim');

    // ---- State -----------------------------------------------------------
    let selectedSlot = null;

    // ---- Fetch slots from API --------------------------------------------
    const fetchSlots = async (selectedDate) => {
        slotsGrid.innerHTML = '';
        slotsEmpty.style.display = 'none';
        slotsLoading.style.display = 'block';
        resetConfirmButton();

        try {
            const response = await fetch(
                `${config.apiUrl}?quadraId=${config.quadraId}&data=${selectedDate}`
            );
            const data = await response.json();

            slotsLoading.style.display = 'none';

            if (!data.sucesso && data.mensagem) {
                slotsEmpty.textContent = data.mensagem;
                slotsEmpty.style.display = 'block';
                return;
            }

            if (!data.slots || data.slots.length === 0) {
                slotsEmpty.textContent = 'Nenhum horário disponível para esta data.';
                slotsEmpty.style.display = 'block';
                return;
            }

            renderSlots(data.slots);
        } catch (error) {
            slotsLoading.style.display = 'none';
            slotsEmpty.textContent = 'Erro ao buscar horários. Tente novamente.';
            slotsEmpty.style.display = 'block';
        }
    };

    // ---- Render slots ----------------------------------------------------
    const renderSlots = (slots) => {
        slotsGrid.innerHTML = '';

        slots.forEach(slot => {
            const slotBtn = document.createElement('button');
            slotBtn.className = 'slotBtn';
            slotBtn.type = 'button';

            const horaInicioFormatted = slot.horaInicio.substring(0, 5);
            const horaFimFormatted   = slot.horaFim.substring(0, 5);
            const precoFormatted     = parseFloat(slot.preco).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

            slotBtn.innerHTML = `<div>${horaInicioFormatted}–${horaFimFormatted}</div><div class="slotPrice">${precoFormatted}</div>`;

            if (!slot.disponivel) {
                slotBtn.classList.add('slotUnavailable');
                slotBtn.disabled = true;
                slotBtn.setAttribute('aria-label', `Horário ${horaInicioFormatted} indisponível`);
            } else {
                slotBtn.setAttribute('aria-label', `Reservar horário ${horaInicioFormatted} por ${precoFormatted}`);

                if (selectedSlot && selectedSlot.horaInicio === slot.horaInicio) {
                    slotBtn.classList.add('selected');
                }

                slotBtn.addEventListener('click', () => {
                    if (!config.canBook) return;
                    selectedSlot = slot;
                    updateConfirmButton(slot);
                    renderSlots(slots);
                });
            }

            slotsGrid.appendChild(slotBtn);
        });
    };

    // ---- Confirm button --------------------------------------------------
    const updateConfirmButton = (slot) => {
        if (!confirmBtn) return;

        const horaFormatted = slot.horaInicio.substring(0, 5);
        const precoFormatted = parseFloat(slot.preco).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

        confirmBtn.disabled = false;
        confirmBtn.className = 'bookingConfirmBtn enabled';
        confirmBtn.textContent = `Confirmar – ${horaFormatted} (${precoFormatted})`;
    };

    const resetConfirmButton = () => {
        selectedSlot = null;
        if (!confirmBtn) return;
        confirmBtn.disabled = true;
        confirmBtn.className = 'bookingConfirmBtn disabled';
        confirmBtn.textContent = 'Selecione um horário';
    };

    // ---- Confirm click → submit form ------------------------------------
    if (confirmBtn) {
        confirmBtn.addEventListener('click', () => {
            if (!selectedSlot || !datePicker.value) return;

            formDataReserva.value = datePicker.value;
            formHoraInicio.value  = selectedSlot.horaInicio;
            formHoraFim.value     = selectedSlot.horaFim;
            reservaForm.submit();
        });
    }

    // ---- Date picker change ----------------------------------------------
    if (datePicker) {
        datePicker.addEventListener('change', () => {
            const selectedDate = datePicker.value;
            if (selectedDate) {
                fetchSlots(selectedDate);
            }
        });

        // Auto-select today and fetch slots
        const today = new Date().toISOString().split('T')[0];
        datePicker.value = today;
        fetchSlots(today);
    }
})();
