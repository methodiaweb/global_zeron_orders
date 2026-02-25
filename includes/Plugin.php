<?php
declare(strict_types=1);

namespace GZO;

use GZO\Admin\Settings;
use GZO\Front\Account;
use GZO\Front\DummyPdf;

final class Plugin
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init(): void
    {
        (new Settings())->init();
        (new Account())->init();
        (new DummyPdf())->init();
    }
}
