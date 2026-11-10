<?php

namespace Tests\Feature;

use App\Models\Station;
use App\Models\StationConnector;
use App\Models\StationReport;
use App\Models\StationReview;
use App\Models\User;
use App\Models\VehicleMaster;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_filament_login_route_redirects_to_unified_user_login_page(): void
    {
        $response = $this->get('/admin/login');

        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_guest_accessing_admin_panel_redirects_to_unified_login_page(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_login_via_unified_login_page_and_sees_admin_dashboard_button(): void
    {
        $this->withoutVite();

        $admin = User::factory()->admin()->create([
            'name' => 'Admin Utama',
            'email' => 'admin@casin.id',
            'password' => 'adminsecret123',
        ]);

        $loginResponse = $this->post(route('login.store'), [
            'email' => 'admin@casin.id',
            'password' => 'adminsecret123',
        ]);

        $loginResponse->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($admin);

        $homeResponse = $this->actingAs($admin)->get(route('home'));
        $homeResponse->assertStatus(200)
            ->assertSee('Hai, Admin Utama')
            ->assertSee('Dashboard Admin')
            ->assertSee('data-admin-dashboard-btn', false)
            ->assertSee(url('/admin'), false);

        $stationsResponse = $this->actingAs($admin)->get(route('stations.index'));
        $stationsResponse->assertStatus(200)
            ->assertSee('Dashboard Admin')
            ->assertSee('data-admin-dashboard-btn', false);
    }

    public function test_admin_can_access_filament_admin_panel_after_login(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin Panel',
            'email' => 'panel.admin@casin.id',
            'password' => 'adminsecret123',
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200)
            ->assertSee('Dashboard')
            ->assertSee('Total Pengguna')
            ->assertSee('Total Stasiun')
            ->assertSee('Total Kendaraan')
            ->assertSee('Total Laporan')
            ->assertSee('Total Ulasan')
            ->assertSee('Stasiun Aktif')
            ->assertSee('Pertumbuhan pengguna')
            ->assertSee('Ringkasan laporan')
            ->assertSee('Status laporan komunitas')
            ->assertSee('Stasiun terbaru')
            ->assertSee('CasIn Hub Sudirman')
            ->assertSee('Nadia Putri mengulas CasIn Hub Sudirman')
            ->assertSee('Data User')
            ->assertSee('Data Stasiun')
            ->assertSee('SPKLU PLN Embong Wungu')
            ->assertSee('Tambah Data Stasiun')
            ->assertSee('Edit Data Stasiun')
            ->assertSee('Data Kendaraan')
            ->assertSee('IONIQ 5')
            ->assertSee('Tambah Data Kendaraan')
            ->assertSee('Edit Data Kendaraan')
            ->assertSee('Data Konektor')
            ->assertSee('Data Laporan')
            ->assertSee('Data Ulasan')
            ->assertSee('Profil Admin')
            ->assertSee('Logout');
    }

    public function test_regular_user_does_not_see_admin_button_and_cannot_access_admin_panel(): void
    {
        $this->withoutVite();

        $user = User::factory()->create([
            'name' => 'Pengendara Biasa',
            'email' => 'user@casin.id',
            'role' => 'user',
            'password' => 'usersecret123',
        ]);

        $homeResponse = $this->actingAs($user)->get(route('home'));
        $homeResponse->assertStatus(200)
            ->assertSee('Hai, Pengendara Biasa')
            ->assertDontSee('data-admin-dashboard-btn', false);

        $adminResponse = $this->actingAs($user)->get('/admin');
        $adminResponse->assertStatus(403);
    }

    public function test_admin_login_fails_with_wrong_password(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin CasIn',
            'email' => 'admin@casin.id',
            'password' => 'correctpassword123',
        ]);

        $response = $this->from(route('login'))->post(route('login.store'), [
            'email' => 'admin@casin.id',
            'password' => 'wrongpassword999',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_admin_login_via_api_returns_admin_role_and_flag(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin API',
            'email' => 'admin.api@casin.id',
            'password' => 'adminsecret123',
        ]);

        $response = $this->postJson(route('api.login'), [
            'email' => 'admin.api@casin.id',
            'password' => 'adminsecret123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'user' => [
                        'email' => 'admin.api@casin.id',
                        'role' => 'admin',
                        'is_admin' => true,
                    ],
                ],
            ]);
    }

    public function test_admin_can_logout_from_filament_panel_and_redirect_to_unified_login(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@casin.id',
        ]);

        $response = $this->actingAs($admin)->post('/admin/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_active_sanctum_token_can_synchronize_web_session_for_admin_access(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Naufal Admin',
            'email' => 'naufal@casin.id',
            'password' => 'password123',
        ]);

        $token = $admin->createToken('casin_test_sync')->plainTextToken;

        $response = $this->postJson(route('auth.session-sync'), [
            'token' => $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'user' => [
                        'email' => 'naufal@casin.id',
                        'role' => 'admin',
                        'is_admin' => true,
                    ],
                ],
            ]);

        $this->assertAuthenticatedAs($admin);

        $adminDashboardResponse = $this->get('/admin');
        $adminDashboardResponse->assertStatus(200);
    }

    public function test_sync_session_fails_with_invalid_token(): void
    {
        $response = $this->postJson(route('auth.session-sync'), [
            'token' => 'invalid_or_expired_token_xyz',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertGuest();
    }

    public function test_filament_dashboard_displays_live_database_records_when_populated(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'naufalrabbani146@gmail.com')->firstOrFail();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200)
            ->assertSee('PLN SPKLU Substation Central')
            ->assertSee('Ioniq 5')
            ->assertSee('naufalrabbani146@gmail.com')
            ->assertSee('Dispenser CCS2 dan AC Type 2 berfungsi dengan baik')
            ->assertSee('Area parkir luas, atap peneduh aman saat hujan')
            ->assertDontSee('SPKLU PLN Embong Wungu')
            ->assertDontSee('Nadia Putri mengulas CasIn Hub Sudirman');
    }

    public function test_admin_can_crud_station_and_persist_to_database(): void
    {
        $admin = User::factory()->admin()->create();

        $createResponse = $this->actingAs($admin)->postJson('/admin/api/stations', [
            'name' => 'SPKLU Test Database Baru',
            'operator_type' => 'government',
            'address' => 'Jl. Jend. Sudirman Kav. 10, Jakarta',
            'latitude' => -6.21462,
            'longitude' => 106.84513,
            'operating_hours' => '24 Jam',
            'price_per_kwh' => 2466,
            'require_app_name' => 'PLN Mobile',
            'current_status' => 'normal',
            'connector_type' => 'CCS2',
            'power_kw' => 60,
            'available_connectors' => 2,
            'total_connectors' => 3,
        ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('success', true);

        $stationId = $createResponse->json('data.id_stations');
        $this->assertDatabaseHas('stations', [
            'id_stations' => $stationId,
            'name' => 'SPKLU Test Database Baru',
            'operator_type' => 'government',
            'current_status' => 'normal',
        ]);
        $this->assertDatabaseHas('station_connectors', [
            'station_id' => $stationId,
            'connector_type' => 'CCS2',
            'charging_speed_category' => 'ultra',
            'available_connectors' => 2,
            'total_connectors' => 3,
        ]);

        $updateResponse = $this->actingAs($admin)->putJson("/admin/api/stations/{$stationId}", [
            'name' => 'SPKLU Test Database Updated',
            'operator_type' => 'commercial',
            'address' => 'Jl. Thamrin No. 99, Jakarta',
            'latitude' => -6.19123,
            'longitude' => 106.82345,
            'operating_hours' => '08.00 - 22.00',
            'price_per_kwh' => 3000,
            'require_app_name' => 'Voltron',
            'current_status' => 'busy',
        ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('stations', [
            'id_stations' => $stationId,
            'name' => 'SPKLU Test Database Updated',
            'operator_type' => 'commercial',
            'current_status' => 'busy',
        ]);

        $deleteResponse = $this->actingAs($admin)->deleteJson("/admin/api/stations/{$stationId}");
        $deleteResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('stations', [
            'id_stations' => $stationId,
        ]);
    }

    public function test_admin_can_crud_vehicle_and_persist_to_database(): void
    {
        $admin = User::factory()->admin()->create();

        $createResponse = $this->actingAs($admin)->postJson('/admin/api/vehicles', [
            'brand' => 'BYD',
            'model' => 'Sealion 7',
            'category' => 'Mobil Listrik',
            'battery_capacity_kwh' => 82.5,
            'default_connector_type' => 'CCS2',
        ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('success', true);

        $vehicleId = $createResponse->json('data.id_vehicle_masters');
        $this->assertDatabaseHas('vehicle_masters', [
            'id_vehicle_masters' => $vehicleId,
            'brand' => 'BYD',
            'model' => 'Sealion 7',
        ]);

        $updateResponse = $this->actingAs($admin)->putJson("/admin/api/vehicles/{$vehicleId}", [
            'brand' => 'BYD',
            'model' => 'Sealion 7 Performance',
            'category' => 'Mobil Listrik',
            'battery_capacity_kwh' => 91.3,
            'default_connector_type' => 'CCS2',
        ]);

        $updateResponse->assertStatus(200);
        $this->assertDatabaseHas('vehicle_masters', [
            'id_vehicle_masters' => $vehicleId,
            'model' => 'Sealion 7 Performance',
        ]);

        $deleteResponse = $this->actingAs($admin)->deleteJson("/admin/api/vehicles/{$vehicleId}");
        $deleteResponse->assertStatus(200);
        $this->assertDatabaseMissing('vehicle_masters', [
            'id_vehicle_masters' => $vehicleId,
        ]);
    }

    public function test_admin_can_crud_connector_and_persist_to_database(): void
    {
        $admin = User::factory()->admin()->create();
        $station = Station::create([
            'name' => 'SPKLU Konektor Uji',
            'operator_type' => 'government',
            'address' => 'Jl. Gatot Subroto No. 12',
            'latitude' => -6.22,
            'longitude' => 106.82,
            'operating_hours' => '24 Jam',
            'price_per_kwh' => 2466,
            'require_app_name' => 'PLN Mobile',
            'current_status' => 'normal',
        ]);

        $createResponse = $this->actingAs($admin)->postJson('/admin/api/connectors', [
            'station_id' => $station->id_stations,
            'connector_type' => 'CHAdeMO',
            'power_kw' => 50,
            'charging_speed_category' => 'ultra',
            'current_type' => 'DC',
            'available_connectors' => 1,
            'total_connectors' => 2,
        ]);

        $createResponse->assertStatus(201);
        $connectorId = $createResponse->json('data.id_station_connectors');

        $this->assertDatabaseHas('station_connectors', [
            'id_station_connectors' => $connectorId,
            'station_id' => $station->id_stations,
            'connector_type' => 'CHAdeMO',
            'available_connectors' => 1,
            'total_connectors' => 2,
        ]);

        $updateResponse = $this->actingAs($admin)->putJson("/admin/api/connectors/{$connectorId}", [
            'station_id' => $station->id_stations,
            'connector_type' => 'AC Type 2',
            'power_kw' => 22,
            'charging_speed_category' => 'fast',
            'current_type' => 'AC',
            'available_connectors' => 5,
            'total_connectors' => 4,
        ]);

        $updateResponse->assertStatus(200);
        $this->assertDatabaseHas('station_connectors', [
            'id_station_connectors' => $connectorId,
            'connector_type' => 'AC Type 2',
            'available_connectors' => 4,
            'total_connectors' => 4,
        ]);

        $deleteResponse = $this->actingAs($admin)->deleteJson("/admin/api/connectors/{$connectorId}");
        $deleteResponse->assertStatus(200);
        $this->assertDatabaseMissing('station_connectors', [
            'id_station_connectors' => $connectorId,
        ]);
    }

    public function test_admin_can_manage_users_reports_reviews_and_own_profile_in_database(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin Awal',
            'email' => 'admin.utama@casin.id',
        ]);
        $otherUser = User::factory()->create([
            'name' => 'Pengguna Uji',
            'email' => 'pengguna.uji@casin.id',
            'role' => 'user',
        ]);

        // Self-demotion & self-deletion protection
        $this->actingAs($admin)
            ->patchJson("/admin/api/users/{$admin->id}/role", ['role' => 'user'])
            ->assertStatus(422);

        $this->actingAs($admin)
            ->deleteJson("/admin/api/users/{$admin->id}")
            ->assertStatus(422);

        // Promote other user
        $this->actingAs($admin)
            ->patchJson("/admin/api/users/{$otherUser->id}/role", ['role' => 'admin'])
            ->assertStatus(200)
            ->assertJsonPath('data.role', 'admin');

        $this->assertDatabaseHas('users', [
            'id' => $otherUser->id,
            'role' => 'admin',
        ]);

        // Create station, report, review
        $station = Station::create([
            'name' => 'SPKLU Pengawasan',
            'operator_type' => 'private',
            'address' => 'Jl. Kemang Selatan No. 5',
            'latitude' => -6.27,
            'longitude' => 106.81,
            'operating_hours' => '24 Jam',
            'price_per_kwh' => 2500,
            'require_app_name' => 'Shell Recharge',
            'current_status' => 'normal',
        ]);

        $report = StationReport::create([
            'id_stations' => $station->id_stations,
            'connector_type' => 'CCS2',
            'power_kw' => 50,
            'charging_speed_category' => 'ultra',
            'status' => 'normal',
            'notes' => 'Pengecekan unit 1',
            'reported_at' => now(),
        ]);

        $review = StationReview::create([
            'id_stations' => $station->id_stations,
            'id_user' => $otherUser->id,
            'rating' => 4,
            'comment' => 'Lokasi mudah dijangkau',
        ]);

        // Updating report status syncs station status
        $this->actingAs($admin)
            ->patchJson("/admin/api/reports/{$report->id_station_report}/status", ['status' => 'rusak'])
            ->assertStatus(200);

        $this->assertDatabaseHas('station_reports', [
            'id_station_report' => $report->id_station_report,
            'status' => 'rusak',
        ]);
        $this->assertDatabaseHas('stations', [
            'id_stations' => $station->id_stations,
            'current_status' => 'technical_issue',
        ]);

        // Delete report & review & other user
        $this->actingAs($admin)
            ->deleteJson("/admin/api/reports/{$report->id_station_report}")
            ->assertStatus(200);
        $this->assertDatabaseMissing('station_reports', ['id_station_report' => $report->id_station_report]);

        $this->actingAs($admin)
            ->deleteJson("/admin/api/reviews/{$review->id_station_review}")
            ->assertStatus(200);
        $this->assertDatabaseMissing('station_reviews', ['id_station_review' => $review->id_station_review]);

        $otherVehicle = VehicleMaster::create([
            'category' => 'Mobil Listrik',
            'brand' => 'Hyundai',
            'model' => 'Ioniq 5',
            'default_connector_type' => 'CCS2',
            'battery_capacity_kwh' => 72.6,
        ]);
        \Illuminate\Support\Facades\DB::table('user_vehicle')->insert([
            'user_id' => $otherUser->id,
            'vehicle_id' => $otherVehicle->id_vehicle_masters,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $otherUser->createToken('temp_user_token');

        $this->actingAs($admin)
            ->deleteJson("/admin/api/users/{$otherUser->id}")
            ->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $otherUser->id]);
        $this->assertDatabaseMissing('user_vehicle', ['user_id' => $otherUser->id]);
        $this->assertDatabaseMissing('personal_access_tokens', ['tokenable_id' => $otherUser->id]);

        // Update admin profile
        $this->actingAs($admin)
            ->patchJson('/admin/api/profile', [
                'name' => 'Admin Utama Diperbarui',
                'phone' => '081298765432',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ])
            ->assertStatus(200)
            ->assertJsonPath('data.name', 'Admin Utama Diperbarui');

        $admin->refresh();
        $this->assertSame('Admin Utama Diperbarui', $admin->name);
        $this->assertSame('081298765432', $admin->phone);
        $this->assertTrue(Hash::check('newpassword123', $admin->password));
    }

    public function test_non_admin_user_cannot_access_admin_api_endpoints(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->actingAs($user)
            ->postJson('/admin/api/stations', [
                'name' => 'Illegal Station',
            ])
            ->assertStatus(403);

        $this->actingAs($user)
            ->patchJson('/admin/api/profile', [
                'name' => 'Illegal Update',
            ])
            ->assertStatus(403);
    }

    public function test_sync_session_returns_csrf_token_and_preserves_existing_authenticated_session(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'sync.csrf@casin.id',
        ]);
        $token = $admin->createToken('casin_test_csrf')->plainTextToken;

        $this->actingAs($admin)->get('/')->assertStatus(200);
        $beforeSessionId = session()->getId();
        $beforeCsrf = csrf_token();

        $response = $this->withCredentials()
            ->withCookie(config('session.cookie'), $beforeSessionId)
            ->postJson(route('auth.session-sync'), [
                'token' => $token,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'csrf_token',
                    'user' => ['id', 'name', 'email', 'role', 'is_admin'],
                ],
            ]);

        $this->assertSame($beforeCsrf, $response->json('data.csrf_token'));
        $this->assertSame($beforeSessionId, session()->getId());
    }

    public function test_logout_endpoint_supports_both_web_redirect_and_json_response(): void
    {
        $user = User::factory()->create();

        $webResponse = $this->actingAs($user)->post(route('logout'));
        $webResponse->assertRedirect(route('login'));
        $this->assertGuest();

        $jsonResponse = $this->actingAs($user)->postJson(route('logout'));
        $jsonResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
        $this->assertGuest();
    }

    public function test_expired_csrf_token_on_logout_redirects_cleanly_instead_of_419(): void
    {
        $handler = app(ExceptionHandler::class);

        $guestResponse = $handler->render(Request::create('/logout', 'POST'), new TokenMismatchException());
        $this->assertSame(302, $guestResponse->getStatusCode());
        $this->assertSame(route('login'), $guestResponse->headers->get('Location'));

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $authResponse = $handler->render(Request::create('/logout', 'POST'), new TokenMismatchException());
        $this->assertSame(302, $authResponse->getStatusCode());
        $this->assertSame(route('home'), $authResponse->headers->get('Location'));
    }

    public function test_admin_remote_pagination_endpoints_work_and_dashboard_html_stays_compact_at_scale(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'naufalrabbani146@gmail.com')->firstOrFail();

        $dashboard = $this->actingAs($admin)->get('/admin');
        $dashboard->assertStatus(200);
        $html = (string) $dashboard->getContent();
        $this->assertLessThan(550000, strlen($html), 'HTML /admin terlalu besar untuk 2.400+ stasiun.');
        $this->assertSame(1, preg_match('/<tbody data-admin-stations-tbody>(.*?)<\/tbody>/s', $html, $stBody));
        $this->assertSame(10, preg_match_all('/<tr\s+data-station-row\b/', $stBody[1]));
        $this->assertSame(1, preg_match('/<tbody data-admin-connectors-tbody>(.*?)<\/tbody>/s', $html, $cnBody));
        $this->assertSame(10, preg_match_all('/<tr\s+data-connector-row\b/', $cnBody[1]));
        $this->assertStringContainsString('data-connector-station-search', $html);

        $stationsPage = $this->actingAs($admin)->getJson('/admin/api/stations?page=2&per_page=10&operator_type=government');
        $stationsPage->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.current_page', 2)
            ->assertJsonCount(10, 'data.items');

        $searchStations = $this->actingAs($admin)->getJson('/admin/api/stations?search=Nabire');
        $searchStations->assertStatus(200)
            ->assertJsonPath('success', true);
        $this->assertGreaterThanOrEqual(1, $searchStations->json('data.total'));

        $connectorsPage = $this->actingAs($admin)->getJson('/admin/api/connectors?page=1&per_page=10&connector_type=DC%20Connector');
        $connectorsPage->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(10, 'data.items');

        $options = $this->actingAs($admin)->getJson('/admin/api/station-options?q=Jakarta');
        $options->assertStatus(200)
            ->assertJsonPath('success', true);
        $this->assertNotEmpty($options->json('data'));

        $regular = User::factory()->create(['role' => 'user']);
        $this->actingAs($regular)->getJson('/admin/api/stations')->assertStatus(403);
        $this->actingAs($regular)->getJson('/admin/api/connectors')->assertStatus(403);
    }
}
