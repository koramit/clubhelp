<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Traits\CSVReadable;
use Illuminate\Database\Seeder;

class DivisionTablesSeeder extends Seeder
{
    use CSVReadable;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Division::truncate();

        $now = now()->format('Y-m-d H:i:s');

        $items = $this->loadCSV(storage_path('app/seeders/divisions.csv'));
        $items = array_map(function ($item) use ($now) {
            unset($item['id']);
            $item['created_at'] = $now;
            $item['updated_at'] = $now;

            return $item;
        }, $items);

        Division::insert($items);
    }
}
