<?php

namespace App\Traits;

trait CSVReadable
{
    protected function loadCSV($path)
    {
        if (! file_exists($path)) {
            return [];
        }
        $items = array_map('str_getcsv', file($path));
        array_walk($items, function (&$item) use ($items) {
            $item = array_combine($items[0], $item);
        });
        array_shift($items);

        return $items;
    }
}
