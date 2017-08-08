<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        // check if table users is empty
        if (DB::table('quotes')->get()->count() == 0) {

            DB::table('quotes')->insert([

                [
                    'quote' => 'Napoleon Hill',
                    'author' => 'The world has the habit of making room for the man whose words and actions show that he knows where he is going',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'quote' => 'James Allen',
                    'author' => 'Circumstance does not make the man; it reveals him to himself',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'quote' => 'Michelangelo',
                    'author' => 'The greater danger for most of us is not that our aim is too high and we miss it, but that it is too low and we reach it',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]

            ]);

        } else {
            echo "\e[Table is not empty, therefore NOT seeded";
        }
    }
}
