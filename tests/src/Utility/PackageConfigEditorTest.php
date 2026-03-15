<?php

namespace Marktic\Newsletter\Tests\Utility;

use Marktic\Newsletter\Tests\AbstractTest;
use Marktic\Newsletter\Utility\PackageConfig;
use Nip\Config\Config;
use Nip\Container\Utility\Container;

class PackageConfigEditorTest extends AbstractTest
{
    protected function setUp(): void
    {
        parent::setUp();
        PackageConfig::resetInstance();
    }

    public function test_editor_driver_defaults_to_grapesjs(): void
    {
        $this->loadNewsletterConfig([]);

        self::assertSame('grapesjs', PackageConfig::editorDriver());
    }

    public function test_editor_driver_reads_configured_value(): void
    {
        $this->loadNewsletterConfig([
            'editor' => ['driver' => 'unlayer'],
        ]);

        self::assertSame('unlayer', PackageConfig::editorDriver());
    }

    public function test_editor_options_defaults_to_empty_array(): void
    {
        $this->loadNewsletterConfig([]);

        self::assertSame([], PackageConfig::editorOptions());
    }

    public function test_editor_options_reads_configured_values(): void
    {
        $this->loadNewsletterConfig([
            'editor' => [
                'driver'  => 'unlayer',
                'options' => ['project_id' => 42],
            ],
        ]);

        self::assertSame(['project_id' => 42], PackageConfig::editorOptions());
    }

    public function test_beefree_driver_with_credentials(): void
    {
        $this->loadNewsletterConfig([
            'editor' => [
                'driver'  => 'beefree',
                'options' => [
                    'client_id'     => 'my-id',
                    'client_secret' => 'my-secret',
                ],
            ],
        ]);

        self::assertSame('beefree', PackageConfig::editorDriver());
        self::assertSame('my-id', PackageConfig::editorOptions()['client_id']);
        self::assertSame('my-secret', PackageConfig::editorOptions()['client_secret']);
    }

    // ── helpers ──────────────────────────────────────────────────────────────

    private function loadNewsletterConfig(array $data): void
    {
        $existing = config();
        $merged   = $existing->merge(new Config(['mkt_newsletter' => $data], true));
        Container::container()->set('config', $merged);
    }
}
