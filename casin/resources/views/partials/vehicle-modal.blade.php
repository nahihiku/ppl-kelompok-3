<dialog class="vehicle-dialog" id="vehicle-modal" data-vehicle-modal aria-labelledby="vehicle-modal-title" aria-describedby="vehicle-modal-description">
    <div class="vehicle-dialog-header">
        <h2 id="vehicle-modal-title">Pilih Kendaraan Aktif</h2>
        <button type="button" class="vehicle-dialog-close" aria-label="Tutup pemilihan kendaraan" data-vehicle-modal-close>
            <img src="{{ asset('images/vehicle-dialog/close.svg') }}" width="25" height="25" alt="">
        </button>
    </div>

    <p id="vehicle-modal-description" class="vehicle-dialog-description">Pilih kendaraanmu agar stasiun yang ditampilkan sesuai dengan jenis konektornya.</p>

    <section data-vehicle-picker-view>
        <form class="vehicle-dialog-form" data-vehicle-form>
            <div class="vehicle-dialog-field">
                <label for="active-vehicle">Pilih Kendaraan</label>
                <div class="vehicle-dialog-select-wrap">
                    <select id="active-vehicle" name="vehicle" required data-vehicle-select>
                        <option value="" disabled selected>Pilih merek dan model kendaraan</option>
                        <option value="all">Semua Kendaraan / Semua Konektor</option>
                        @foreach ($vehicles as $vehicle)
                            <option value="{{ $vehicle['id'] }}" data-vehicle-brand="{{ $vehicle['brand'] }} {{ $vehicle['model'] }}" data-vehicle-connector="{{ $vehicle['default_connector_type'] }}">{{ $vehicle['brand'] }} {{ $vehicle['model'] }} · {{ $vehicle['default_connector_type'] }}</option>
                        @endforeach
                        <optgroup label="Tambahan di browser ini" data-custom-vehicles hidden></optgroup>
                    </select>
                    <img class="vehicle-dialog-chevron" src="{{ asset('images/vehicle-dialog/chevron.svg') }}" width="19" height="19" alt="">
                </div>
                @if ($vehicles->isEmpty())
                    <p class="vehicle-dialog-message">Belum ada kendaraan bawaan. Tambahkan kendaraanmu atau gunakan semua konektor.</p>
                @endif
                <p class="vehicle-dialog-message" role="alert" hidden data-vehicle-error></p>
                <p class="vehicle-dialog-message" role="status" hidden data-vehicle-status></p>
            </div>

            <div class="vehicle-dialog-actions">
                <button type="submit" class="vehicle-dialog-submit">Gunakan Kendaraan</button>
                <button type="button" class="vehicle-dialog-reset" aria-controls="vehicle-add-menu" data-vehicle-add-open>Tambah Kendaraan / Konektor</button>
            </div>
        </form>
    </section>

    <section id="vehicle-add-menu" data-vehicle-add-view hidden>
        <form class="vehicle-dialog-form" data-vehicle-add-form>
            <fieldset class="vehicle-add-kind">
                <legend>Yang ingin ditambahkan</legend>
                <label><input type="radio" name="kind" value="vehicle" checked> Kendaraan</label>
                <label><input type="radio" name="kind" value="connector"> Tipe konektor saja</label>
            </fieldset>

            <div class="vehicle-add-fields" data-vehicle-details>
                <div class="vehicle-dialog-field">
                    <label for="additional-vehicle-brand">Merek Kendaraan</label>
                    <input id="additional-vehicle-brand" type="text" name="brand" required maxlength="100" autocomplete="off" placeholder="Merek kendaraan Anda">
                </div>
                <div class="vehicle-dialog-field">
                    <label for="additional-vehicle-model">Model Kendaraan</label>
                    <input id="additional-vehicle-model" type="text" name="model" required maxlength="100" autocomplete="off" placeholder="Model kendaraan Anda">
                </div>
            </div>

            <div class="vehicle-dialog-field">
                <label for="additional-vehicle-connector">Jenis Konektor</label>
                <input id="additional-vehicle-connector" type="text" name="connector" required maxlength="100" autocomplete="off" list="vehicle-connector-suggestions" aria-describedby="additional-connector-help" placeholder="Pilih atau ketik jenis konektor">
                <datalist id="vehicle-connector-suggestions">
                    @foreach ($vehicles->pluck('default_connector_type')->merge(['CCS2', 'AC Type 2', 'CHAdeMO', 'DC Connector', 'AC Connector'])->filter()->unique() as $connector)
                        <option value="{{ $connector }}"></option>
                    @endforeach
                </datalist>
                <p id="additional-connector-help" class="vehicle-dialog-message">Pilih saran yang tersedia atau ketik tipe konektor lain.</p>
            </div>

            <p class="vehicle-dialog-message" role="alert" hidden data-vehicle-add-error></p>
            <div class="vehicle-dialog-actions">
                <button type="submit" class="vehicle-dialog-submit" data-vehicle-add-submit>Simpan Kendaraan</button>
                <button type="button" class="vehicle-dialog-reset" data-vehicle-add-back>Kembali ke Pilihan Kendaraan</button>
            </div>
        </form>
    </section>
</dialog>
