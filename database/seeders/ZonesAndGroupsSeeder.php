<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZonesAndGroupsSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            [
                'name' => 'SA Zone 2',
                'order' => 1,
                'groups' => [
                    'Angola Group',
                    'Free State Group',
                    'Germiston Group',
                    'Kempton Park Group',
                    'Kensington Group',
                    'South Group',
                    'Namibia Group',
                    'Northern Cape Group',
                    'Pleroma group',
                    'Pretoria group',
                    'West Rand Group',
                    'Witbank group',
                    'Yeoville Group',
                ]
            ],
            [
                'name' => 'SA Zone 3',
                'order' => 2,
                'groups' => [
                    'Sunninghill Church',
                    'Eswatini Group',
                    'Lesotho group',
                    'Malawi North Group',
                    'Malawi South Group',
                    'Mozambique Group',
                    'Zambia Group',
                ]
            ],
            [
                'name' => 'SA Zone 5',
                'order' => 3,
                'groups' => [
                    'Belvedere Group',
                    'Bulawayo Group',
                    'Chitungwiza Group',
                    'Hatfield Group',
                    'Mutare Group',
                    'Norton Group',
                ]
            ],
            [
                'name' => 'Cape Town Zone 1',
                'order' => 4,
                'groups' => [
                    'Waterfront Group',
                ]
            ],
            [
                'name' => 'Cape Town Zone 2',
                'order' => 5,
                'groups' => [
                    'Cape Town North Group',
                    'Stellenbosch Group',
                ]
            ],
            [
                'name' => 'Durban Zone',
                'order' => 6,
                'groups' => [
                    'Botswana Group',
                    'Durban Group',
                ]
            ],
        ];

        foreach ($zones as $zoneData) {
            $zone = DB::table('zones')->insertGetId([
                'name' => $zoneData['name'],
                'order' => $zoneData['order'],
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($zoneData['groups'] as $index => $groupName) {
                DB::table('groups')->insert([
                    'zone_id' => $zone,
                    'name' => $groupName,
                    'order' => $index + 1,
                    'active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}