<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LargeOrderDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating large dataset for performance testing...');

        DB::disableQueryLog();

        $this->command->info('Creating 500 users...');
        $users = User::factory(500)->create();

        $this->command->info('Creating 10,000 orders...');

        $progressBar = $this->command->getOutput()->createProgressBar(10000);

        $orderChunks = [];
        $chunkSize = 500;

        for ($i = 0; $i < 10000; $i++) {
            $orderChunks[] = [
                'user_id' => $users->random()->id,
                'total' => fake()->randomFloat(2, 50, 2000),
                'status' => fake()->randomElement(['completed', 'completed', 'completed', 'pending', 'cancelled']),
                'created_at' => fake()->dateTimeBetween('-3 months', 'now'),
                'updated_at' => now(),
            ];

            if (count($orderChunks) === $chunkSize) {
                Order::insert($orderChunks);
                $orderChunks = [];
                $progressBar->advance($chunkSize);
            }
        }

        if (!empty($orderChunks)) {
            Order::insert($orderChunks);
            $progressBar->advance(count($orderChunks));
        }

        $progressBar->finish();
        $this->command->newLine();
        $this->command->info('Dataset created successfully!');
        $this->command->info('Users: ' . User::count());
        $this->command->info('Orders: ' . Order::count());
    }
}
