import { addCustomVehicle, readCustomVehicles, customVehicleName, customVehicleLabel } from './custom-vehicles.js';

export function initVehicleSelector({ readVehicle, onChange, storageKey }) {
    const modal = document.querySelector('[data-vehicle-modal]');
    if (!modal) return;

    const form = modal.querySelector('[data-vehicle-form]');
    const select = modal.querySelector('[data-vehicle-select]');
    const error = modal.querySelector('[data-vehicle-error]');
    const pickerView = modal.querySelector('[data-vehicle-picker-view]');
    const addView = modal.querySelector('[data-vehicle-add-view]');
    const addForm = modal.querySelector('[data-vehicle-add-form]');
    const addError = modal.querySelector('[data-vehicle-add-error]');
    const status = modal.querySelector('[data-vehicle-status]');
    const customGroup = modal.querySelector('[data-custom-vehicles]');
    const title = modal.querySelector('#vehicle-modal-title');
    const description = modal.querySelector('#vehicle-modal-description');
    const baseVehicles = Array.from(select.options).filter((option) => option.dataset.vehicleBrand)
        .map((option) => ({ name: option.dataset.vehicleBrand, connector: option.dataset.vehicleConnector }));
    const openButtons = document.querySelectorAll('[data-vehicle-modal-open]');
    let activeVehicle = readVehicle();
    let opener = null;
    let previousOverflow = '';

    const showView = (view) => {
        const adding = view === 'add';
        pickerView.hidden = adding;
        addView.hidden = !adding;
        title.textContent = adding ? 'Tambah Kendaraan / Konektor' : 'Pilih Kendaraan Aktif';
        description.textContent = adding
            ? 'Tambahkan kendaraan atau tipe konektor yang belum tersedia. Data disimpan di browser ini.'
            : 'Pilih kendaraanmu agar stasiun yang ditampilkan sesuai dengan jenis konektornya.';
        modal.scrollTop = 0;
        (adding ? addForm.elements.brand : select).focus();
    };

    const loadCustomOptions = () => {
        customGroup.replaceChildren();
        customGroup.hidden = true;
        const entries = readCustomVehicles(localStorage);
        entries.forEach((entry) => {
            const option = document.createElement('option');
            option.value = entry.id;
            option.textContent = customVehicleLabel(entry);
            option.dataset.vehicleBrand = customVehicleName(entry);
            option.dataset.vehicleConnector = entry.connector;
            option.dataset.customVehicleId = entry.id;
            option.dataset.vehicleKind = entry.kind;
            customGroup.append(option);
        });
        customGroup.hidden = entries.length === 0;
    };

    const renderVehicle = (vehicle) => {
        document.querySelectorAll('[data-active-vehicle-brand]').forEach((element) => {
            element.textContent = vehicle?.brand || 'Merek Kendaraan';
        });
        document.querySelectorAll('[data-active-vehicle-connector]').forEach((element) => {
            element.textContent = vehicle ? `Konektor ${vehicle.connector}` : 'Jenis Konektor';
        });
    };

    renderVehicle(activeVehicle);
    openButtons.forEach((button) => {
        button.setAttribute('aria-haspopup', 'dialog');
        button.setAttribute('aria-controls', modal.id);
        button.addEventListener('click', () => {
            if (modal.open) return;
            opener = button;
            error.hidden = true;
            status.hidden = true;
            select.setCustomValidity('');
            try {
                loadCustomOptions();
            } catch (cause) {
                error.textContent = cause.message || 'Daftar tambahan tidak dapat dibaca dari browser.';
                error.hidden = false;
            }
            const savedOption = Array.from(select.options).find((option) =>
                activeVehicle?.customId && option.value === activeVehicle.customId)
                || Array.from(select.options).find((option) =>
                option.value && option.dataset.vehicleBrand === activeVehicle?.brand
                && option.dataset.vehicleConnector === activeVehicle?.connector);
            select.value = savedOption?.value || (activeVehicle ? '' : 'all');
            previousOverflow = document.body.style.overflow;
            document.body.style.overflow = 'hidden';
            modal.showModal();
            showView('picker');
        });
    });

    modal.querySelector('[data-vehicle-modal-close]').addEventListener('click', () => modal.close());
    modal.addEventListener('close', () => {
        document.body.style.overflow = previousOverflow;
        opener?.focus();
    });
    modal.addEventListener('cancel', (event) => {
        if (!addView.hidden) {
            event.preventDefault();
            showView('picker');
        }
    });
    modal.addEventListener('click', (event) => {
        // Clicks in the dialog padding stay inside; only the backdrop dismisses it.
        if (event.target !== modal) return;
        const bounds = modal.getBoundingClientRect();
        if (event.clientX < bounds.left || event.clientX > bounds.right
            || event.clientY < bounds.top || event.clientY > bounds.bottom) modal.close();
    });
    select.addEventListener('invalid', () => select.setCustomValidity('Pilih kendaraan terlebih dahulu.'));
    select.addEventListener('change', () => {
        select.setCustomValidity('');
        error.hidden = true;
        status.hidden = true;
    });

    const saveVehicle = (vehicle) => {
        try {
            if (vehicle) localStorage.setItem(storageKey, JSON.stringify(vehicle));
            else localStorage.removeItem(storageKey);
        } catch {
            error.textContent = 'Pilihan belum tersimpan. Izinkan penyimpanan situs pada browser, lalu coba lagi.';
            error.hidden = false;
            return;
        }
        activeVehicle = vehicle;
        renderVehicle(vehicle);
        onChange();
        modal.close();
    };

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        if (!form.reportValidity()) return;
        const option = select.selectedOptions[0];
        saveVehicle(option.value === 'all' ? null : {
            brand: option.dataset.vehicleBrand,
            connector: option.dataset.vehicleConnector,
            ...(option.dataset.customVehicleId ? { customId: option.dataset.customVehicleId, kind: option.dataset.vehicleKind } : {}),
        });
    });

    const updateAdditionKind = () => {
        const connectorOnly = addForm.elements.kind.value === 'connector';
        modal.querySelector('[data-vehicle-details]').hidden = connectorOnly;
        ['brand', 'model'].forEach((field) => { addForm.elements[field].disabled = connectorOnly; });
        modal.querySelector('[data-vehicle-add-submit]').textContent = connectorOnly ? 'Simpan Konektor' : 'Simpan Kendaraan';
        addError.hidden = true;
    };
    addForm.querySelectorAll('[name="kind"]').forEach((radio) => radio.addEventListener('change', updateAdditionKind));
    modal.querySelector('[data-vehicle-add-open]').addEventListener('click', () => {
        addForm.reset();
        addForm.querySelectorAll('input[type="text"]').forEach((input) => input.setCustomValidity(''));
        updateAdditionKind();
        showView('add');
    });
    modal.querySelector('[data-vehicle-add-back]').addEventListener('click', () => showView('picker'));

    const invalidMessages = {
        brand: 'Isi merek kendaraan terlebih dahulu.',
        model: 'Isi model kendaraan terlebih dahulu.',
        connector: 'Isi jenis konektor terlebih dahulu.',
    };
    addForm.querySelectorAll('input[type="text"]').forEach((input) => {
        input.addEventListener('invalid', () => input.setCustomValidity(invalidMessages[input.name]));
        input.addEventListener('input', () => {
            input.setCustomValidity('');
            addError.hidden = true;
        });
    });
    addForm.addEventListener('submit', (event) => {
        event.preventDefault();
        if (!addForm.reportValidity()) return;
        try {
            const entry = addCustomVehicle(Object.fromEntries(new FormData(addForm)), localStorage, baseVehicles);
            loadCustomOptions();
            select.value = entry.id;
            error.hidden = true;
            showView('picker');
            status.textContent = `${customVehicleLabel(entry)} ditambahkan. Tekan Gunakan Kendaraan untuk menerapkan.`;
            status.hidden = false;
        } catch (cause) {
            addError.textContent = cause.field || cause.message?.includes('Daftar tambahan')
                ? cause.message : 'Pilihan belum tersimpan. Penyimpanan browser tidak tersedia atau penuh. Coba lagi.';
            addError.hidden = false;
            if (cause.field) addForm.elements[cause.field]?.focus?.();
        }
    });
}
