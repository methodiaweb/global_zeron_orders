<?php
declare(strict_types=1);

namespace GZO\Data;

final class DummyProvider
{
    public static function load(): array
    {
        $file = GZO_PLUGIN_DIR . 'assets/dummy-obligations.json';
        if (!is_readable($file)) {
            return [
                'unpaid' => [],
                'paid_by_year' => [],
            ];
        }

        $raw = file_get_contents($file);
        if ($raw === false) {
            return ['unpaid' => [], 'paid_by_year' => []];
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            return ['unpaid' => [], 'paid_by_year' => []];
        }

        return $data;
    }

    public static function get_years(array $data): array
    {
        $years = array_keys($data['paid_by_year'] ?? []);
        rsort($years, SORT_NUMERIC);
        return array_values($years);
    }
}
