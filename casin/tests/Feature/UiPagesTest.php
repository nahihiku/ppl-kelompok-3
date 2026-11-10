<?php

namespace Tests\Feature;

use Tests\TestCase;

class UiPagesTest extends TestCase
{
    public function test_landing_page_renders_figma_ui_and_preserves_location_widget(): void
    {
        $this->withoutVite();

        $response = $this->get(route('home'));

        $response->assertStatus(200)
            ->assertSee('1.000+ Stasiun Pengisian Daya Terverifikasi')
            ->assertSee('Temukan Stasiun Pengisian Daya Terdekat')
            ->assertSee('Cari Stasiun Sekarang')
            ->assertSee('Kendaraan Aktif')
            ->assertSee('Pilih Kendaraan')
            ->assertSee('Charging jadi lebih pasti.')
            ->assertSee('Peta Sebaran Stasiun Pengisian Daya')
            ->assertSee('id="stations-overview-map"', false)
            ->assertSee('id="stations-leaflet-canvas"', false)
            ->assertSee('Stasiun Terdekat')
            ->assertSee('PLN SPKLU Substation Central')
            ->assertSee('Lihat di Peta')
            ->assertSee('id="location-widget"', false);
    }

    public function test_login_and_register_pages_render_figma_auth_ui(): void
    {
        $this->withoutVite();

        $loginResponse = $this->get(route('login'));
        $loginResponse->assertStatus(200)
            ->assertSee('Selamat Datang di CasIn.id')
            ->assertSee('Masukkan email terdaftar')
            ->assertSee('Masukkan Password')
            ->assertSee('Lupa Password?')
            ->assertSee('Masuk dengan Google')
            ->assertSee('Daftar Sekarang');

        $registerResponse = $this->get(route('register'));
        $registerResponse->assertStatus(200)
            ->assertSee('Daftar Akun CasIn.id')
            ->assertSee('Daftar dengan Google')
            ->assertSee(route('google.redirect'), false)
            ->assertSee(route('register.store'), false);
    }

    public function test_stations_index_and_filter_work_properly(): void
    {
        $this->withoutVite();

        $response = $this->get(route('stations.index'));
        $response->assertStatus(200)
            ->assertSee('Stasiun Terdekat')
            ->assertSee('Diurutkan berdasarkan jarak terdekat')
            ->assertSee('PLN SPKLU Substation Central')
            ->assertSee('Lihat Detail');

        $filtered = $this->get(route('stations.index', ['q' => 'Substation Central']));
        $filtered->assertStatus(200)
            ->assertSee('PLN SPKLU Substation Central')
            ->assertSee('1 Ditemukan');
    }

    public function test_station_detail_page_and_report_work_properly(): void
    {
        $this->withoutVite();

        $response = $this->get(route('stations.show', '1'));
        $response->assertStatus(200)
            ->assertSee('PLN SPKLU Substation Central')
            ->assertSee('id="station-detail-map"', false)
            ->assertSee('https://www.google.com/maps/dir/?api=1&destination=-6.175392,106.790512')
            ->assertSee('Navigate')
            ->assertSee('Lapor')
            ->assertSee('Kotak Pengisian')
            ->assertSee('Ulasan')
            ->assertSee('Fasilitas Umum')
            ->assertSee('3/4 Konektor Tersedia')
            ->assertSee('Mobi AC Stand')
            ->assertSee('AC TYPE 2 - 22kW');

        $reportResponse = $this->post(route('stations.report', '1'), [
            'status' => 'busy',
            'notes' => 'Antrean 2 mobil',
        ]);

        $reportResponse->assertRedirect(route('stations.show', '1'));
    }
}
