<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Holidays;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $attributesArray = [
            [
                'name' => "New Year's Day",
                'category' => 'Regular Holidays',
                'month' => 1,
                'day' => 1,
            ],
            [
                'name' => 'Araw ng Kagitingan',
                'category' => 'Regular Holidays',
                'month' => 4,
                'day' => 10,
            ],
            [
                'name' => 'Maundy Thursday',
                'category' => 'Regular Holidays',
                'month' => 4,
                'day' => 6,
            ],
            [
                'name' => 'Good Friday',
                'category' => 'Regular Holidays',
                'month' => 4,
                'day' => 7,
            ],
            [
                'name' => "Eid'l Fitr",
                'category' => 'Regular Holidays',
                'month' => 4,
                'day' => 21,
            ],
            [
                'name' => 'Labor Day',
                'category' => 'Regular Holidays',
                'month' => 5,
                'day' => 1,
            ],
            [
                'name' => 'Independence Day',
                'category' => 'Regular Holidays',
                'month' => 6,
                'day' => 12,
            ],
            [
                'name' => "Eid'l Adha",
                'category' => 'Regular Holidays',
                'month' => 6,
                'day' => 28,
            ],
            [
                'name' => 'National Heroes Day',
                'category' => 'Regular Holidays',
                'month' => 8,
                'day' => 28,
            ],
            [
                'name' => 'Bonifacio Day',
                'category' => 'Regular Holidays',
                'month' => 11,
                'day' => 27,
            ],
            [
                'name' => 'Christmas Day',
                'category' => 'Regular Holidays',
                'month' => 12,
                'day' => 25,
            ],
            [
                'name' => 'Rizal Day',
                'category' => 'Regular Holidays',
                'month' => 12,
                'day' => 30,
            ],
            [
                'name' => 'Ninoy Aquino Day',
                'category' => 'Special (Non-Working) Holidays',
                'month' => 8,
                'day' => 21,
            ],
            [
                'name' => 'EDSA People Power Revolution Anniversary',
                'category' => 'Special (Non-Working) Holidays',
                'month' => 2,
                'day' => 24,
            ],
            [
                'name' => 'Black Saturday',
                'category' => 'Special (Non-Working) Holidays',
                'month' => 4,
                'day' => 8,
            ],
            [
                'name' => "All Saints' Day",
                'category' => 'Special (Non-Working) Holidays',
                'month' => 11,
                'day' => 1,
            ],
            [
                'name' => 'Feast of the Immaculate Conception of Mary',
                'category' => 'Special (Non-Working) Holidays',
                'month' => 12,
                'day' => 8,
            ],
            [
                'name' => 'Last Day of the Year',
                'category' => 'Special (Non-Working) Holidays',
                'month' => 12,
                'day' => 31,
            ],
            [
                'name' => 'Additional Special (Non-Working) Day',
                'category' => 'Special (Non-Working) Holidays',
                'month' => 1,
                'day' => 2,
            ],
            [
                'name' => 'Additional Special (Non-Working) Day',
                'category' => 'Special (Non-Working) Holidays',
                'month' => 11,
                'day' => 2,
            ],
        ];
        foreach ($attributesArray as $attributes) {
            Holidays::create($attributes);
        }
        




        // \App\Models\User::factory(10)->create();
        // \App\Models\Department::factory(20)->create();
        // \App\Models\Position::factory(5)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        // \App\Models\User::factory()->create([
        //     'name' => 'Marlon123',
        //     'email' => 'marlonpadilla1593@gmail.com',
        // ]);



        // \App\Models\Employee::factory(100)->create();

        

        

   
    }
}
