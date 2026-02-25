<?php
declare(strict_types=1);

namespace GZO\Admin;

final class Settings
{
    public const OPTION_KEY = 'gzo_settings';

    public function init(): void
    {
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public static function get_settings(): array
    {
        $defaults = [
            'mode' => 'dummy', // dummy|live
            'api_base_url' => '',
            'api_user' => '',
            'api_password' => '',
            'api_token' => '',
        ];
        $saved = get_option(self::OPTION_KEY, []);
        if (!is_array($saved)) { $saved = []; }
        return array_merge($defaults, $saved);
    }

    public function register_menu(): void
    {
        add_menu_page(
            __('Global Zeron orders', 'global-zeron-orders'),
            __('Global Zeron orders', 'global-zeron-orders'),
            'manage_options',
            'global-zeron-orders',
            [$this, 'render_page'],
            'dashicons-clipboard',
            58
        );
    }

    public function register_settings(): void
    {
        register_setting('gzo_settings_group', self::OPTION_KEY, [
            'type' => 'array',
            'sanitize_callback' => [$this, 'sanitize'],
            'default' => self::get_settings(),
        ]);

        add_settings_section(
            'gzo_main',
            __('Settings', 'global-zeron-orders'),
            static function () {
                echo '<p>' . esc_html__('MVP settings. Live mode fields are placeholders for later API wiring.', 'global-zeron-orders') . '</p>';
            },
            'global-zeron-orders'
        );

        add_settings_field(
            'gzo_mode',
            __('Mode', 'global-zeron-orders'),
            [$this, 'field_mode'],
            'global-zeron-orders',
            'gzo_main'
        );

        add_settings_field(
            'gzo_api_base_url',
            __('API Base URL (Live)', 'global-zeron-orders'),
            [$this, 'field_text'],
            'global-zeron-orders',
            'gzo_main',
            ['key' => 'api_base_url', 'placeholder' => 'https://example.com/zeron/connector']
        );

        add_settings_field(
            'gzo_api_user',
            __('API User (Live)', 'global-zeron-orders'),
            [$this, 'field_text'],
            'global-zeron-orders',
            'gzo_main',
            ['key' => 'api_user', 'placeholder' => 'data']
        );

        add_settings_field(
            'gzo_api_password',
            __('API Password (Live)', 'global-zeron-orders'),
            [$this, 'field_password'],
            'global-zeron-orders',
            'gzo_main',
            ['key' => 'api_password']
        );

        add_settings_field(
            'gzo_api_token',
            __('API Token (Live)', 'global-zeron-orders'),
            [$this, 'field_text'],
            'global-zeron-orders',
            'gzo_main',
            ['key' => 'api_token', 'placeholder' => 'Bearer …']
        );
    }

    public function sanitize($input): array
    {
        $out = self::get_settings();

        if (is_array($input)) {
            $mode = isset($input['mode']) ? sanitize_text_field((string)$input['mode']) : 'dummy';
            $out['mode'] = in_array($mode, ['dummy', 'live'], true) ? $mode : 'dummy';

            $out['api_base_url'] = isset($input['api_base_url']) ? esc_url_raw((string)$input['api_base_url']) : '';
            $out['api_user'] = isset($input['api_user']) ? sanitize_text_field((string)$input['api_user']) : '';
            $out['api_password'] = isset($input['api_password']) ? sanitize_text_field((string)$input['api_password']) : '';
            $out['api_token'] = isset($input['api_token']) ? sanitize_text_field((string)$input['api_token']) : '';
        }

        return $out;
    }

    public function render_page(): void
    {
        if (!current_user_can('manage_options')) { return; }
        $settings = self::get_settings();
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Global Zeron orders', 'global-zeron-orders'); ?></h1>
            <?php if ($settings['mode'] === 'live'): ?>
                <div class="notice notice-warning">
                    <p><?php echo esc_html__('Live mode is not wired to the ERP yet (MVP). The fields below are saved only.', 'global-zeron-orders'); ?></p>
                </div>
            <?php endif; ?>

            <form method="post" action="options.php">
                <?php
                settings_fields('gzo_settings_group');
                do_settings_sections('global-zeron-orders');
                submit_button();
                ?>
            </form>

            <hr />
            <p>
                <?php echo esc_html__('Front-end view:', 'global-zeron-orders'); ?>
                <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" target="_blank" rel="noopener">
                    <?php echo esc_html__('My Account', 'global-zeron-orders'); ?>
                </a>
            </p>
        </div>
        <?php
    }

    public function field_mode(): void
    {
        $settings = self::get_settings();
        ?>
        <select name="<?php echo esc_attr(self::OPTION_KEY); ?>[mode]">
            <option value="dummy" <?php selected($settings['mode'], 'dummy'); ?>>
                <?php echo esc_html__('Dummy (uses bundled JSON)', 'global-zeron-orders'); ?>
            </option>
            <option value="live" <?php selected($settings['mode'], 'live'); ?>>
                <?php echo esc_html__('Live (ERP API - later)', 'global-zeron-orders'); ?>
            </option>
        </select>
        <?php
    }

    public function field_text(array $args): void
    {
        $settings = self::get_settings();
        $key = (string)($args['key'] ?? '');
        $placeholder = (string)($args['placeholder'] ?? '');
        ?>
        <input type="text"
               class="regular-text"
               name="<?php echo esc_attr(self::OPTION_KEY); ?>[<?php echo esc_attr($key); ?>]"
               value="<?php echo esc_attr((string)($settings[$key] ?? '')); ?>"
               placeholder="<?php echo esc_attr($placeholder); ?>" />
        <?php
    }

    public function field_password(array $args): void
    {
        $settings = self::get_settings();
        $key = (string)($args['key'] ?? '');
        ?>
        <input type="password"
               class="regular-text"
               name="<?php echo esc_attr(self::OPTION_KEY); ?>[<?php echo esc_attr($key); ?>]"
               value="<?php echo esc_attr((string)($settings[$key] ?? '')); ?>" />
        <?php
    }
}
