<?php
declare(strict_types=1);

namespace GZO\Front;

use GZO\Admin\Settings;
use GZO\Data\DummyProvider;

final class Account
{
    public const ENDPOINT = 'obligations';

    public function init(): void
    {
        // Endpoint + rewrite.
        add_action('init', [$this, 'add_endpoint']);
        add_filter('query_vars', [$this, 'add_query_vars']);

        // My Account menu.
        add_filter('woocommerce_account_menu_items', [$this, 'menu_items'], 50);

        // Content.
        add_action('woocommerce_account_' . self::ENDPOINT . '_endpoint', [$this, 'render_endpoint']);

        // Assets.
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function add_endpoint(): void
    {
        add_rewrite_endpoint(self::ENDPOINT, EP_ROOT | EP_PAGES);
    }

    public function add_query_vars(array $vars): array
    {
        $vars[] = self::ENDPOINT;
        $vars[] = 'gzo_paid_year';
        return $vars;
    }

    public function menu_items(array $items): array
    {
        // Remove Orders (MVP decision).
        if (isset($items['orders'])) {
            unset($items['orders']);
        }

        // Insert obligations after dashboard.
        $new = [];
        foreach ($items as $key => $label) {
            $new[$key] = $label;
            if ($key === 'dashboard') {
                $new[self::ENDPOINT] = __('Задължения', 'global-zeron-orders');
            }
        }

        // If dashboard wasn't there for some reason, append.
        if (!isset($new[self::ENDPOINT])) {
            $new[self::ENDPOINT] = __('Задължения', 'global-zeron-orders');
        }

        return $new;
    }

    public function enqueue_assets(): void
    {
        if (!function_exists('is_account_page') || !is_account_page()) {
            return;
        }

        wp_enqueue_style(
            'gzo-account',
            GZO_PLUGIN_URL . 'assets/css/account-obligations.css',
            [],
            GZO_VERSION
        );
    }

    public function render_endpoint(): void
    {
        $settings = Settings::get_settings();
        $mode = $settings['mode'] ?? 'dummy';

        $data = [
            'unpaid' => [],
            'paid_by_year' => [],
            'years' => [],
            'selected_year' => '',
            'mode' => $mode,
        ];

        if ($mode === 'dummy') {
            $dummy = DummyProvider::load();
            $years = DummyProvider::get_years($dummy);
            $selected_year = $this->get_selected_year($years);

            $data['unpaid'] = $this->sort_by_date_desc($dummy['unpaid'] ?? []);
            $paid = $dummy['paid_by_year'][$selected_year] ?? [];
            $data['paid_by_year'] = $this->sort_by_date_desc($paid);
            $data['years'] = $years;
            $data['selected_year'] = $selected_year;
        } else {
            // Live mode: MVP placeholder (no API calls).
            $data['years'] = [];
            $data['selected_year'] = '';
        }

        wc_get_template(
            'account/obligations.php',
            $data,
            '',
            GZO_PLUGIN_DIR . 'templates/'
        );
    }

    private function get_selected_year(array $years): string
    {
        if (empty($years)) {
            return (string)date('Y');
        }

        $requested = isset($_GET['gzo_paid_year']) ? sanitize_text_field((string)$_GET['gzo_paid_year']) : '';
        if ($requested !== '' && in_array($requested, $years, true)) {
            return $requested;
        }

        // Default = most recent year (first in sorted list).
        return (string)$years[0];
    }

    private function sort_by_date_desc(array $rows): array
    {
        usort($rows, static function (array $a, array $b): int {
            $da = strtotime((string)($a['date'] ?? ''));
            $db = strtotime((string)($b['date'] ?? ''));
            return $db <=> $da;
        });
        return $rows;
    }
}
