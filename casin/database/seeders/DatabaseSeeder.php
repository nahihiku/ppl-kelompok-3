<?php

namespace Database\Seeders;

use App\Models\ConnectorType;
use App\Models\Operator;
use App\Models\Station;
use App\Models\StationReport;
use App\Models\StationReview;
use App\Models\User;
use App\Models\VehicleMaster;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Pengguna Utama dan Administrator
        User::updateOrCreate(
            ['email' => 'admin@casin.id'],
            [
                'name' => 'Admin CasIn.id',
                'role' => 'admin',
                'password' => 'password',
            ]
        );

        $naufalAccount = User::firstOrNew(['email' => 'naufalrabbani146@gmail.com']);
        $naufalAccount->name = 'Naufal';
        $naufalAccount->role = 'admin';
        if (! $naufalAccount->exists || empty($naufalAccount->password)) {
            $naufalAccount->password = Str::random(32);
        }
        $naufalAccount->save();

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'role' => 'user',
                'password' => 'password',
            ]
        );

        // 2. Data Master Kendaraan Listrik (EV)
        $vehicles = [
            [
                'category' => 'Mobil Listrik',
                'brand' => 'Hyundai',
                'model' => 'Ioniq 5',
                'default_connector_type' => 'CCS2',
                'battery_capacity_kwh' => 72.6,
            ],
            [
                'category' => 'Mobil Listrik',
                'brand' => 'Hyundai',
                'model' => 'Ioniq 6',
                'default_connector_type' => 'CCS2',
                'battery_capacity_kwh' => 77.4,
            ],
            [
                'category' => 'Mobil Listrik',
                'brand' => 'Hyundai',
                'model' => 'Kona Electric',
                'default_connector_type' => 'CCS2',
                'battery_capacity_kwh' => 66.0,
            ],
            [
                'category' => 'Mobil Listrik',
                'brand' => 'Wuling',
                'model' => 'Air EV Long Range',
                'default_connector_type' => 'AC Type 2',
                'battery_capacity_kwh' => 26.7,
            ],
            [
                'category' => 'Mobil Listrik',
                'brand' => 'Wuling',
                'model' => 'BinguoEV 410km',
                'default_connector_type' => 'CCS2',
                'battery_capacity_kwh' => 37.9,
            ],
            [
                'category' => 'Mobil Listrik',
                'brand' => 'Wuling',
                'model' => 'Cloud EV',
                'default_connector_type' => 'CCS2',
                'battery_capacity_kwh' => 50.6,
            ],
            [
                'category' => 'Mobil Listrik',
                'brand' => 'BYD',
                'model' => 'Seal Performance',
                'default_connector_type' => 'CCS2',
                'battery_capacity_kwh' => 82.5,
            ],
            [
                'category' => 'Mobil Listrik',
                'brand' => 'BYD',
                'model' => 'Atto 3',
                'default_connector_type' => 'CCS2',
                'battery_capacity_kwh' => 60.48,
            ],
            [
                'category' => 'Mobil Listrik',
                'brand' => 'BYD',
                'model' => 'Dolphin',
                'default_connector_type' => 'CCS2',
                'battery_capacity_kwh' => 60.48,
            ],
            [
                'category' => 'Mobil Listrik',
                'brand' => 'Chery',
                'model' => 'Omoda E5',
                'default_connector_type' => 'CCS2',
                'battery_capacity_kwh' => 61.0,
            ],
            [
                'category' => 'Mobil Listrik',
                'brand' => 'MG',
                'model' => 'MG 4 EV',
                'default_connector_type' => 'CCS2',
                'battery_capacity_kwh' => 51.0,
            ],
            [
                'category' => 'Mobil Listrik',
                'brand' => 'BMW',
                'model' => 'i4 eDrive40',
                'default_connector_type' => 'CCS2',
                'battery_capacity_kwh' => 83.9,
            ],
            [
                'category' => 'Mobil Listrik',
                'brand' => 'Kia',
                'model' => 'EV6 GT-Line',
                'default_connector_type' => 'CCS2',
                'battery_capacity_kwh' => 77.4,
            ],
            [
                'category' => 'Motor Listrik',
                'brand' => 'Alva',
                'model' => 'Alva One',
                'default_connector_type' => 'AC Type 2',
                'battery_capacity_kwh' => 2.7,
            ],
            [
                'category' => 'Motor Listrik',
                'brand' => 'Gesits',
                'model' => 'Gesits G1',
                'default_connector_type' => 'AC Type 2',
                'battery_capacity_kwh' => 1.44,
            ],
            [
                'category' => 'Motor Listrik',
                'brand' => 'Polytron',
                'model' => 'Fox-R',
                'default_connector_type' => 'AC Type 2',
                'battery_capacity_kwh' => 3.75,
            ],
        ];

        foreach ($vehicles as $vehicle) {
            VehicleMaster::updateOrCreate(
                ['brand' => $vehicle['brand'], 'model' => $vehicle['model']],
                $vehicle
            );
        }

        // 3. Data Sebaran Stasiun Pengisian Daya (SPKLU) Lengkap
        $stations = [
            [
                'name' => 'PLN SPKLU Substation Central',
                'operator_type' => 'government',
                'address' => 'Jl. Letjen S. Parman No. 28, Jakarta Barat',
                'latitude' => -6.175392,
                'longitude' => 106.790512,
                'operating_hours' => '24 Jam',
                'price_per_kwh' => 2000,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 50, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 3],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 22, 'charging_speed_category' => 'fast', 'current_type' => 'AC', 'available_connectors' => 1, 'total_connectors' => 1],
                ],
            ],
            [
                'name' => 'PLN SPKLU Gambir Utama',
                'operator_type' => 'government',
                'address' => 'Jl. Medan Merdeka Timur No. 1, Jakarta Pusat',
                'latitude' => -6.176655,
                'longitude' => 106.830589,
                'operating_hours' => '24 Jam',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 50, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 3, 'total_connectors' => 4],
                ],
            ],
            [
                'name' => 'ION Mobility Sudirman',
                'operator_type' => 'private',
                'address' => 'Jl. Jenderal Sudirman Kav. 52-53, Jakarta Selatan',
                'latitude' => -6.2254,
                'longitude' => 106.8067,
                'operating_hours' => '06.00 - 22.00',
                'price_per_kwh' => 2500,
                'require_app_name' => 'ION Mobility',
                'current_status' => 'busy',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 60, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 0, 'total_connectors' => 2],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 22, 'charging_speed_category' => 'fast', 'current_type' => 'AC', 'available_connectors' => 1, 'total_connectors' => 2],
                ],
            ],
            [
                'name' => 'PLN SPKLU Kantor Distribusi Disjaya Gambir',
                'operator_type' => 'government',
                'address' => 'Jl. M.I. Ridwan Rais No. 1, Gambir, Jakarta Pusat',
                'latitude' => -6.180250,
                'longitude' => 106.833120,
                'operating_hours' => '24 Jam',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 100, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 2],
                    ['connector_type' => 'CHAdeMO', 'power_kw' => 50, 'charging_speed_category' => 'fast', 'current_type' => 'DC', 'available_connectors' => 1, 'total_connectors' => 1],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 22, 'charging_speed_category' => 'fast', 'current_type' => 'AC', 'available_connectors' => 2, 'total_connectors' => 2],
                ],
            ],
            [
                'name' => 'PLN SPKLU Kantor Pusat PLN Kebayoran Baru',
                'operator_type' => 'government',
                'address' => 'Jl. Trunojoyo Blok M-I No. 135, Kebayoran Baru, Jakarta Selatan',
                'latitude' => -6.239850,
                'longitude' => 106.799320,
                'operating_hours' => '24 Jam',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 150, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 3, 'total_connectors' => 4],
                    ['connector_type' => 'CCS2', 'power_kw' => 50, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 2],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 22, 'charging_speed_category' => 'fast', 'current_type' => 'AC', 'available_connectors' => 2, 'total_connectors' => 2],
                ],
            ],
            [
                'name' => 'PLN SPKLU Senayan City Mall',
                'operator_type' => 'commercial',
                'address' => 'Jl. Asia Afrika No. 19, Gelora, Tanah Abang, Jakarta Pusat',
                'latitude' => -6.227280,
                'longitude' => 106.797820,
                'operating_hours' => '10.00 - 22.00 WIB',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 60, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 3],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 22, 'charging_speed_category' => 'fast', 'current_type' => 'AC', 'available_connectors' => 3, 'total_connectors' => 4],
                ],
            ],
            [
                'name' => 'PLN SPKLU Central Park Mall',
                'operator_type' => 'commercial',
                'address' => 'Jl. Letjen S. Parman Kav. 28, Tanjung Duren Selatan, Jakarta Barat',
                'latitude' => -6.177240,
                'longitude' => 106.790690,
                'operating_hours' => '10.00 - 22.00 WIB',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 50, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 1, 'total_connectors' => 2],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 22, 'charging_speed_category' => 'fast', 'current_type' => 'AC', 'available_connectors' => 2, 'total_connectors' => 2],
                ],
            ],
            [
                'name' => 'PLN SPKLU Mall Kelapa Gading 3',
                'operator_type' => 'commercial',
                'address' => 'Jl. Boulevard Raya Blok M, Kelapa Gading Timur, Jakarta Utara',
                'latitude' => -6.157810,
                'longitude' => 106.908420,
                'operating_hours' => '10.00 - 22.00 WIB',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 60, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 2],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 22, 'charging_speed_category' => 'fast', 'current_type' => 'AC', 'available_connectors' => 3, 'total_connectors' => 4],
                ],
            ],
            [
                'name' => 'PLN SPKLU Pondok Indah Mall 2',
                'operator_type' => 'commercial',
                'address' => 'Jl. Metro Pondok Indah Kav. IV, Kebayoran Lama, Jakarta Selatan',
                'latitude' => -6.265740,
                'longitude' => 106.784150,
                'operating_hours' => '10.00 - 22.00 WIB',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'busy',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 50, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 0, 'total_connectors' => 2],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 22, 'charging_speed_category' => 'fast', 'current_type' => 'AC', 'available_connectors' => 1, 'total_connectors' => 2],
                ],
            ],
            [
                'name' => 'PLN SPKLU Gandaria City',
                'operator_type' => 'commercial',
                'address' => 'Jl. Sultan Iskandar Muda, Kebayoran Lama Utara, Jakarta Selatan',
                'latitude' => -6.244360,
                'longitude' => 106.783630,
                'operating_hours' => '10.00 - 22.00 WIB',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 50, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 1, 'total_connectors' => 2],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 11, 'charging_speed_category' => 'standard', 'current_type' => 'AC', 'available_connectors' => 2, 'total_connectors' => 2],
                ],
            ],
            [
                'name' => 'PLN SPKLU Bandara Soekarno-Hatta T3',
                'operator_type' => 'government',
                'address' => 'Gedung Parkir Domestik Terminal 3 Bandara Soekarno-Hatta, Pajang, Tangerang',
                'latitude' => -6.125430,
                'longitude' => 106.663180,
                'operating_hours' => '24 Jam',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 120, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 4, 'total_connectors' => 4],
                    ['connector_type' => 'CCS2', 'power_kw' => 50, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 2],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 22, 'charging_speed_category' => 'fast', 'current_type' => 'AC', 'available_connectors' => 2, 'total_connectors' => 2],
                ],
            ],
            [
                'name' => 'PLN SPKLU AEON Mall BSD City',
                'operator_type' => 'commercial',
                'address' => 'Jl. BSD Raya Utama, Pagedangan, Kec. Pagedangan, Kabupaten Tangerang',
                'latitude' => -6.304180,
                'longitude' => 106.643750,
                'operating_hours' => '10.00 - 22.00 WIB',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 50, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 2],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 22, 'charging_speed_category' => 'fast', 'current_type' => 'AC', 'available_connectors' => 2, 'total_connectors' => 2],
                ],
            ],
            [
                'name' => 'PLN SPKLU Bintaro Xchange Mall',
                'operator_type' => 'commercial',
                'address' => 'CBD Bintaro Jaya Blok O-2, Pd. Jaya, Pondok Aren, Kota Tangerang Selatan',
                'latitude' => -6.287250,
                'longitude' => 106.726580,
                'operating_hours' => '10.00 - 22.00 WIB',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 60, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 1, 'total_connectors' => 2],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 22, 'charging_speed_category' => 'fast', 'current_type' => 'AC', 'available_connectors' => 2, 'total_connectors' => 2],
                ],
            ],
            [
                'name' => 'PLN SPKLU Summarecon Mall Serpong',
                'operator_type' => 'commercial',
                'address' => 'Jl. Boulevard Gading Serpong, Pakulonan Barat, Kelapa Dua, Kabupaten Tangerang',
                'latitude' => -6.241510,
                'longitude' => 106.628860,
                'operating_hours' => '10.00 - 22.00 WIB',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 50, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 2],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 22, 'charging_speed_category' => 'fast', 'current_type' => 'AC', 'available_connectors' => 1, 'total_connectors' => 2],
                ],
            ],
            [
                'name' => 'PLN SPKLU Summarecon Mall Bekasi',
                'operator_type' => 'commercial',
                'address' => 'Jl. Bulevar Ahmad Yani Blok M, Marga Mulya, Bekasi Utara, Kota Bekasi',
                'latitude' => -6.225920,
                'longitude' => 106.999810,
                'operating_hours' => '10.00 - 22.00 WIB',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 60, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 3],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 22, 'charging_speed_category' => 'fast', 'current_type' => 'AC', 'available_connectors' => 2, 'total_connectors' => 2],
                ],
            ],
            [
                'name' => 'PLN SPKLU Margo City Depok',
                'operator_type' => 'commercial',
                'address' => 'Jl. Margonda Raya No. 358, Kemiri Muka, Beji, Kota Depok',
                'latitude' => -6.373240,
                'longitude' => 106.834610,
                'operating_hours' => '10.00 - 22.00 WIB',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 50, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 2],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 22, 'charging_speed_category' => 'fast', 'current_type' => 'AC', 'available_connectors' => 1, 'total_connectors' => 2],
                ],
            ],
            [
                'name' => 'PLN SPKLU UP3 Bogor Pajajaran',
                'operator_type' => 'government',
                'address' => 'Jl. Pajajaran No. 233, Bantarjati, Bogor Utara, Kota Bogor',
                'latitude' => -6.582410,
                'longitude' => 106.808720,
                'operating_hours' => '24 Jam',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 50, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 2],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 22, 'charging_speed_category' => 'fast', 'current_type' => 'AC', 'available_connectors' => 1, 'total_connectors' => 1],
                ],
            ],
            [
                'name' => 'PLN SPKLU Rest Area KM 57A Tol Jakarta-Cikampek',
                'operator_type' => 'government',
                'address' => 'Rest Area KM 57A Tol Jakarta-Cikampek, Gintungkerta, Klari, Kabupaten Karawang',
                'latitude' => -6.368530,
                'longitude' => 107.362140,
                'operating_hours' => '24 Jam',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 200, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 2],
                    ['connector_type' => 'CCS2', 'power_kw' => 100, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 2],
                    ['connector_type' => 'CCS2', 'power_kw' => 50, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 2],
                    ['connector_type' => 'CHAdeMO', 'power_kw' => 50, 'charging_speed_category' => 'fast', 'current_type' => 'DC', 'available_connectors' => 1, 'total_connectors' => 1],
                ],
            ],
            [
                'name' => 'PLN SPKLU Rest Area KM 62B Tol Jakarta-Cikampek',
                'operator_type' => 'government',
                'address' => 'Rest Area KM 62B Tol Jakarta-Cikampek, Gintungkerta, Klari, Kabupaten Karawang',
                'latitude' => -6.376820,
                'longitude' => 107.391260,
                'operating_hours' => '24 Jam',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'busy',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 200, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 1, 'total_connectors' => 2],
                    ['connector_type' => 'CCS2', 'power_kw' => 50, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 0, 'total_connectors' => 2],
                ],
            ],
            [
                'name' => 'PLN SPKLU Rest Area KM 130A Tol Cipali',
                'operator_type' => 'government',
                'address' => 'Rest Area KM 130A Tol Cikopo-Palimanan, Sanca, Gantar, Kabupaten Indramayu',
                'latitude' => -6.529140,
                'longitude' => 108.012580,
                'operating_hours' => '24 Jam',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 100, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 2],
                    ['connector_type' => 'CCS2', 'power_kw' => 50, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 2],
                ],
            ],
            [
                'name' => 'PLN SPKLU Rest Area KM 207A Tol Palikanci',
                'operator_type' => 'government',
                'address' => 'Rest Area KM 207A Tol Palimanan-Kanci, Setupatmo, Mundu, Kabupaten Cirebon',
                'latitude' => -6.772580,
                'longitude' => 108.571430,
                'operating_hours' => '24 Jam',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 150, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 2],
                    ['connector_type' => 'CCS2', 'power_kw' => 50, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 1, 'total_connectors' => 2],
                ],
            ],
            [
                'name' => 'PLN SPKLU Gedung Sate Bandung',
                'operator_type' => 'government',
                'address' => 'Jl. Diponegoro No. 22, Citarum, Bandung Wetan, Kota Bandung',
                'latitude' => -6.902480,
                'longitude' => 107.618750,
                'operating_hours' => '24 Jam',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 50, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 2],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 22, 'charging_speed_category' => 'fast', 'current_type' => 'AC', 'available_connectors' => 2, 'total_connectors' => 2],
                ],
            ],
            [
                'name' => 'Shell Recharge Kemang',
                'operator_type' => 'private',
                'address' => 'Jl. Kemang Raya No. 42, Bangka, Mampang Prapatan, Jakarta Selatan',
                'latitude' => -6.260710,
                'longitude' => 106.813420,
                'operating_hours' => '24 Jam',
                'price_per_kwh' => 2466,
                'require_app_name' => 'Shell Asia App',
                'current_status' => 'technical_issue',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 60, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 0, 'total_connectors' => 2],
                ],
            ],
            [
                'name' => 'Shell Recharge Antasari 1',
                'operator_type' => 'private',
                'address' => 'Jl. Pangeran Antasari No. 32A, Cilandak Barat, Jakarta Selatan',
                'latitude' => -6.284560,
                'longitude' => 106.804120,
                'operating_hours' => '24 Jam',
                'price_per_kwh' => 2466,
                'require_app_name' => 'Shell Asia App',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 50, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 1, 'total_connectors' => 2],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 11, 'charging_speed_category' => 'standard', 'current_type' => 'AC', 'available_connectors' => 1, 'total_connectors' => 1],
                ],
            ],
            [
                'name' => 'Shell Recharge Pluit',
                'operator_type' => 'private',
                'address' => 'Jl. Pluit Selatan Raya No. 10, Pluit, Penjaringan, Jakarta Utara',
                'latitude' => -6.126480,
                'longitude' => 106.791540,
                'operating_hours' => '24 Jam',
                'price_per_kwh' => 2466,
                'require_app_name' => 'Shell Asia App',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 60, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 2],
                ],
            ],
            [
                'name' => 'Starvo Fast Charging Pacific Place',
                'operator_type' => 'commercial',
                'address' => 'Basement P2 Pacific Place Mall, SCBD, Jl. Jend. Sudirman, Jakarta Selatan',
                'latitude' => -6.224850,
                'longitude' => 106.809820,
                'operating_hours' => '10.00 - 22.00 WIB',
                'price_per_kwh' => 2500,
                'require_app_name' => 'Starvo App',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 60, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 2],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 22, 'charging_speed_category' => 'fast', 'current_type' => 'AC', 'available_connectors' => 2, 'total_connectors' => 2],
                ],
            ],
            [
                'name' => 'Starvo Mall Grand Indonesia',
                'operator_type' => 'commercial',
                'address' => 'East Mall Parkir Basement B1, Jl. M.H. Thamrin No. 1, Jakarta Pusat',
                'latitude' => -6.195180,
                'longitude' => 106.820920,
                'operating_hours' => '10.00 - 22.00 WIB',
                'price_per_kwh' => 2500,
                'require_app_name' => 'Starvo App',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 60, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 1, 'total_connectors' => 2],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 22, 'charging_speed_category' => 'fast', 'current_type' => 'AC', 'available_connectors' => 3, 'total_connectors' => 4],
                ],
            ],
            [
                'name' => 'Charge+ Plaza Indonesia',
                'operator_type' => 'commercial',
                'address' => 'Parkir P1 Plaza Indonesia, Jl. M.H. Thamrin Kav. 28-30, Jakarta Pusat',
                'latitude' => -6.192310,
                'longitude' => 106.822640,
                'operating_hours' => '10.00 - 22.00 WIB',
                'price_per_kwh' => 2500,
                'require_app_name' => 'Charge+ App',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 120, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 2],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 22, 'charging_speed_category' => 'fast', 'current_type' => 'AC', 'available_connectors' => 2, 'total_connectors' => 2],
                ],
            ],
            [
                'name' => 'Voltron Fast Charging Senopati',
                'operator_type' => 'private',
                'address' => 'Jl. Senopati No. 71, Selong, Kebayoran Baru, Jakarta Selatan',
                'latitude' => -6.233820,
                'longitude' => 106.811950,
                'operating_hours' => '07.00 - 23.00 WIB',
                'price_per_kwh' => 2500,
                'require_app_name' => 'Voltron App',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 100, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 2],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 22, 'charging_speed_category' => 'fast', 'current_type' => 'AC', 'available_connectors' => 1, 'total_connectors' => 2],
                ],
            ],
            [
                'name' => 'Astra Otopower Menara Astra',
                'operator_type' => 'private',
                'address' => 'Ground Floor Menara Astra, Jl. Jend. Sudirman Kav. 5-6, Jakarta Pusat',
                'latitude' => -6.208150,
                'longitude' => 106.821480,
                'operating_hours' => '06.00 - 22.00 WIB',
                'price_per_kwh' => 2475,
                'require_app_name' => 'Astra Otoshop',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 150, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 2],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 22, 'charging_speed_category' => 'fast', 'current_type' => 'AC', 'available_connectors' => 2, 'total_connectors' => 2],
                ],
            ],
            [
                'name' => 'Hyundai EV Ultra Fast Plaza Senayan',
                'operator_type' => 'private',
                'address' => 'Area Parkir VIP Plaza Senayan, Jl. Asia Afrika No. 8, Gelora, Jakarta Pusat',
                'latitude' => -6.225120,
                'longitude' => 106.799750,
                'operating_hours' => '10.00 - 22.00 WIB',
                'price_per_kwh' => 2466,
                'require_app_name' => 'MyHyundai ID',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 200, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 2],
                ],
            ],
            [
                'name' => 'PLN SPKLU Kantor PLN UID Bali Denpasar',
                'operator_type' => 'government',
                'address' => 'Jl. Letda Tantular No. 1, Dangin Puri Klod, Denpasar Timur, Bali',
                'latitude' => -8.670420,
                'longitude' => 115.228910,
                'operating_hours' => '24 Jam',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 50, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 2],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 22, 'charging_speed_category' => 'fast', 'current_type' => 'AC', 'available_connectors' => 2, 'total_connectors' => 2],
                ],
            ],
            [
                'name' => 'PLN SPKLU Bandara I Gusti Ngurah Rai Bali',
                'operator_type' => 'government',
                'address' => 'Area Parkir Internasional Bandara Ngurah Rai, Tuban, Kuta, Badung, Bali',
                'latitude' => -8.748150,
                'longitude' => 115.167230,
                'operating_hours' => '24 Jam',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 150, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 3, 'total_connectors' => 4],
                    ['connector_type' => 'CCS2', 'power_kw' => 50, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 2],
                ],
            ],
            [
                'name' => 'PLN SPKLU Balai Pemuda Surabaya',
                'operator_type' => 'government',
                'address' => 'Jl. Gubernur Suryo No. 15, Embong Kaliasin, Genteng, Surabaya',
                'latitude' => -7.263580,
                'longitude' => 112.744150,
                'operating_hours' => '24 Jam',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 100, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 2],
                    ['connector_type' => 'CCS2', 'power_kw' => 50, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 2],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 22, 'charging_speed_category' => 'fast', 'current_type' => 'AC', 'available_connectors' => 2, 'total_connectors' => 2],
                ],
            ],
            [
                'name' => 'PLN SPKLU Malioboro Mall Yogyakarta',
                'operator_type' => 'commercial',
                'address' => 'Jl. Malioboro No. 52-58, Suryatmajan, Danurejan, Kota Yogyakarta',
                'latitude' => -7.792510,
                'longitude' => 110.366150,
                'operating_hours' => '10.00 - 22.00 WIB',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 50, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 2],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 22, 'charging_speed_category' => 'fast', 'current_type' => 'AC', 'available_connectors' => 2, 'total_connectors' => 2],
                ],
            ],
        ];

        foreach ($stations as $stationData) {
            $connectors = $stationData['connectors'];
            unset($stationData['connectors']);
            $stationData['source'] = 'manual';
            $station = Station::updateOrCreate(['name' => $stationData['name']], $stationData);
            $station->connectors()->delete();
            $station->connectors()->createMany($connectors);
        }

        // 4. Data Master Operator & Tipe Konektor
        $operators = [
            ['name' => 'PLN', 'official_app_name' => 'PLN Mobile', 'logo_url' => null],
            ['name' => 'ION Mobility', 'official_app_name' => 'ION Mobility', 'logo_url' => null],
            ['name' => 'Shell Recharge', 'official_app_name' => 'Shell Asia App', 'logo_url' => null],
            ['name' => 'Charge+', 'official_app_name' => 'Charge+ App', 'logo_url' => null],
            ['name' => 'Starvo', 'official_app_name' => 'Starvo App', 'logo_url' => null],
            ['name' => 'Voltron', 'official_app_name' => 'Voltron App', 'logo_url' => null],
            ['name' => 'Astra Otopower', 'official_app_name' => 'Astra Otoshop', 'logo_url' => null],
            ['name' => 'Hyundai EV', 'official_app_name' => 'MyHyundai ID', 'logo_url' => null],
        ];

        foreach ($operators as $op) {
            Operator::updateOrCreate(['name' => $op['name']], $op);
        }

        $connectorTypes = [
            ['name' => 'CCS2', 'current_type' => 'DC'],
            ['name' => 'AC Type 2', 'current_type' => 'AC'],
            ['name' => 'CHAdeMO', 'current_type' => 'DC'],
        ];

        foreach ($connectorTypes as $ct) {
            ConnectorType::updateOrCreate(['name' => $ct['name']], $ct);
        }

        // 5. Data Laporan Kondisi Stasiun
        $stationByName = Station::query()->pluck('id_stations', 'name');
        $reportsData = [
            [
                'station_name' => 'PLN SPKLU Substation Central',
                'connector_type' => 'CCS2',
                'power_kw' => 50,
                'charging_speed_category' => 'ultra',
                'status' => 'normal',
                'notes' => 'Dispenser CCS2 dan AC Type 2 berfungsi dengan baik, area parkir bersih.',
            ],
            [
                'station_name' => 'ION Mobility Sudirman',
                'connector_type' => 'CCS2',
                'power_kw' => 60,
                'charging_speed_category' => 'ultra',
                'status' => 'antre',
                'notes' => 'Antrean 3 kendaraan listrik pada jam pulang kerja.',
            ],
            [
                'station_name' => 'Shell Recharge Kemang',
                'connector_type' => 'CCS2',
                'power_kw' => 60,
                'charging_speed_category' => 'ultra',
                'status' => 'rusak',
                'notes' => 'Layar dispenser mati sementara, sedang dalam pengecekan teknisi.',
            ],
            [
                'station_name' => 'PLN SPKLU Pondok Indah Mall 2',
                'connector_type' => 'CCS2',
                'power_kw' => 50,
                'charging_speed_category' => 'ultra',
                'status' => 'antre',
                'notes' => 'Kedua konektor CCS2 sedang dipakai pengunjung mal.',
            ],
            [
                'station_name' => 'PLN SPKLU Rest Area KM 62B Tol Jakarta-Cikampek',
                'connector_type' => 'CCS2',
                'power_kw' => 200,
                'charging_speed_category' => 'ultra',
                'status' => 'antre',
                'notes' => 'Arus balik padat, antrean 2 mobil menunggu giliran pengisian cepat.',
            ],
            [
                'station_name' => 'PLN SPKLU Gambir Utama',
                'connector_type' => 'CCS2',
                'power_kw' => 50,
                'charging_speed_category' => 'ultra',
                'status' => 'normal',
                'notes' => 'Konektor berjalan normal, 3 dispenser siap digunakan.',
            ],
        ];

        foreach ($reportsData as $reportItem) {
            $stationId = $stationByName[$reportItem['station_name']] ?? null;
            if ($stationId) {
                StationReport::firstOrCreate(
                    [
                        'id_stations' => $stationId,
                        'notes' => $reportItem['notes'],
                    ],
                    [
                        'connector_type' => $reportItem['connector_type'],
                        'power_kw' => $reportItem['power_kw'],
                        'charging_speed_category' => $reportItem['charging_speed_category'],
                        'status' => $reportItem['status'],
                        'reported_at' => now()->subHours(rand(1, 48)),
                    ]
                );
            }
        }

        // 6. Data Ulasan Pengguna Stasiun
        $testUser = User::where('email', 'test@example.com')->first();
        $naufalUser = User::where('email', 'naufalrabbani146@gmail.com')->first();
        $adminUser = User::where('email', 'admin@casin.id')->first();

        $reviewsData = [
            [
                'station_name' => 'PLN SPKLU Substation Central',
                'user' => $naufalUser,
                'rating' => 5,
                'comment' => 'Area parkir luas, atap peneduh aman saat hujan, dan konektor CCS2 langsung berfungsi lancar lewat aplikasi PLN Mobile.',
            ],
            [
                'station_name' => 'PLN SPKLU Gambir Utama',
                'user' => $testUser,
                'rating' => 5,
                'comment' => 'Mesin sangat terawat, pengisian dari 20% ke 80% hanya butuh sekitar 35 menit.',
            ],
            [
                'station_name' => 'ION Mobility Sudirman',
                'user' => $naufalUser,
                'rating' => 4,
                'comment' => 'Lokasi sangat strategis di pusat bisnis SCBD, hanya cukup ramai di jam makan siang.',
            ],
            [
                'station_name' => 'PLN SPKLU Rest Area KM 57A Tol Jakarta-Cikampek',
                'user' => $testUser,
                'rating' => 5,
                'comment' => 'Ultra fast charging 200kW sangat membantu perjalanan jarak jauh Jakarta ke Bandung.',
            ],
            [
                'station_name' => 'Charge+ Plaza Indonesia',
                'user' => $adminUser,
                'rating' => 5,
                'comment' => 'Dispenser modern, bersih, dan sangat praktis sembari beraktivitas di pusat kota.',
            ],
            [
                'station_name' => 'Shell Recharge Kemang',
                'user' => $testUser,
                'rating' => 3,
                'comment' => 'Lokasi nyaman dengan kafe 24 jam, namun salah satu unit sedang perawatan teknisi.',
            ],
        ];

        foreach ($reviewsData as $revItem) {
            $stationId = $stationByName[$revItem['station_name']] ?? null;
            $userId = $revItem['user']?->id;
            if ($stationId && $userId) {
                StationReview::firstOrCreate(
                    [
                        'id_stations' => $stationId,
                        'id_user' => $userId,
                    ],
                    [
                        'rating' => $revItem['rating'],
                        'comment' => $revItem['comment'],
                    ]
                );
            }
        }

        // 7. Data Sebaran SPKLU Terbuka Nasional (petaspklu.id / PLN + OpenStreetMap)
        $this->call(IndonesiaStationsSeeder::class);
    }
}
