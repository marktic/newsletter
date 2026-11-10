<?php

namespace Marktic\Newsletter\Tests\NewsletterDefinitions;

use Marktic\Newsletter\Base\Exceptions\NewsletterException;
use Marktic\Newsletter\NewsletterDefinitions\BaseDefinition;
use Marktic\Newsletter\NewsletterDefinitions\DefinitionResolver;
use Marktic\Newsletter\NewsletterFilters\FilterTypes\FilterTypes;
use Marktic\Newsletter\Newsletters\Actions\Preview\PreviewEmailSenderInterface;
use Marktic\Newsletter\Newsletters\Models\NewsletterNewsletter;
use Marktic\Newsletter\Tests\AbstractTest;
use Marktic\Newsletter\Tests\Fixtures\NewsletterDefinitions\TestDefinition;
use Marktic\Newsletter\Utility\PackageConfig;
use Nip\Config\Config;
use Nip\Container\Utility\Container;

class DefinitionResolverTest extends AbstractTest
{
    private $originalConfig;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalConfig = config();
        $this->configure([]);
    }

    protected function tearDown(): void
    {
        Container::container()->set('config', $this->originalConfig);
        DefinitionPreviewSender::$sent = [];
        PackageConfig::resetInstance();
        FilterTypes::reset();
        parent::tearDown();
    }

    public function test_default_definition_preserves_filters_and_placeholders(): void
    {
        $this->configure(['filters' => ['default' => ['channel']]]);
        $newsletter = $this->newsletter();
        $definition = DefinitionResolver::forNewsletter($newsletter);

        self::assertSame(BaseDefinition::class, get_class($definition));
        self::assertSame($newsletter, $definition->getNewsletter());
        self::assertSame(['channel'], array_keys($definition->getFilterTypes()));
        self::assertSame([
            '{{ first_name }}' => 'First name',
            '{{ last_name }}' => 'Last name',
            '{{ email }}' => 'Email',
        ], $definition->getPlaceholders());
    }

    public function test_exact_owner_and_type_match_controls_filters_and_placeholders(): void
    {
        $this->configure([
            'definitions' => ['available' => ['organizations' => ['weekly' => TestDefinition::class]]],
            'filters' => ['default' => ['channel']],
        ]);
        $newsletter = $this->newsletter('organizations', 'weekly');
        $definition = DefinitionResolver::forNewsletter($newsletter);

        self::assertInstanceOf(TestDefinition::class, $definition);
        self::assertSame(['list'], array_keys(FilterTypes::forNewsletter($newsletter)));
        self::assertSame(['{{ organization }}' => 'Organization'], $definition->getPlaceholders());
        self::assertSame(BaseDefinition::class, get_class(DefinitionResolver::forNewsletter($this->newsletter('teams', 'weekly'))));
        self::assertSame(BaseDefinition::class, get_class(DefinitionResolver::forNewsletter($this->newsletter('organizations', 'daily'))));
    }

    public function test_missing_owner_or_type_and_missing_matches_use_configured_default(): void
    {
        $this->configure(['definitions' => ['default' => TestDefinition::class]]);
        foreach ([$this->newsletter(), $this->newsletter(null, 'weekly'), $this->newsletter('teams', null),
                     $this->newsletter('teams', 'daily')] as $newsletter) {
            self::assertInstanceOf(TestDefinition::class, DefinitionResolver::forNewsletter($newsletter));
        }
    }

    public function test_invalid_configured_classes_fail_explicitly(): void
    {
        $this->configure(['definitions' => ['default' => \stdClass::class]]);

        $this->expectException(NewsletterException::class);
        $this->expectExceptionMessage('must extend');
        DefinitionResolver::forNewsletter($this->newsletter());
    }

    public function test_invalid_matching_definition_does_not_fall_back_to_default(): void
    {
        $this->configure(['definitions' => ['available' => ['organizations' => ['weekly' => null]]]]);

        $this->expectException(NewsletterException::class);
        DefinitionResolver::forNewsletter($this->newsletter('organizations', 'weekly'));
    }

    public function test_abstract_definition_is_rejected(): void
    {
        $this->configure(['definitions' => ['default' => AbstractTestDefinition::class]]);

        $this->expectException(NewsletterException::class);
        DefinitionResolver::forNewsletter($this->newsletter());
    }

    public function test_invalid_owner_mapping_is_reported(): void
    {
        $this->configure(['definitions' => ['available' => ['organizations' => TestDefinition::class]]]);

        $this->expectException(NewsletterException::class);
        $this->expectExceptionMessage('keyed by owner and type');
        DefinitionResolver::forNewsletter($this->newsletter('organizations', 'weekly'));
    }

    public function test_base_preview_uses_existing_sender_and_validation(): void
    {
        $this->configure(['preview' => ['sender' => DefinitionPreviewSender::class]]);
        $newsletter = $this->newsletter();
        $newsletter->setName('Weekly');
        $newsletter->setContent('<p>Hello {{ first_name }}</p>');
        $definition = DefinitionResolver::forNewsletter($newsletter);

        self::assertSame(['test@example.com'], $definition->sendPreview('TEST@example.com'));
        self::assertSame([
            ['test@example.com', '[Preview] Weekly', '<p>Hello {{ first_name }}</p>'],
        ], DefinitionPreviewSender::$sent);
    }

    public function test_full_send_must_be_implemented_by_a_definition(): void
    {
        $this->expectException(NewsletterException::class);
        $this->expectExceptionMessage('Full newsletter sending is not configured');
        DefinitionResolver::forNewsletter($this->newsletter())->send();
    }

    private function configure(array $config): void
    {
        Container::container()->set('config', new Config(['mkt_newsletter' => $config], true));
        PackageConfig::resetInstance();
        FilterTypes::reset();
    }

    private function newsletter(?string $owner = null, ?string $type = null): NewsletterNewsletter
    {
        $newsletter = new NewsletterNewsletter();
        $newsletter->setId(12);
        $newsletter->setOwner($owner);
        $newsletter->setType($type);
        return $newsletter;
    }
}

class DefinitionPreviewSender implements PreviewEmailSenderInterface
{
    public static array $sent = [];

    public function send(string $to, string $subject, string $html): void
    {
        self::$sent[] = [$to, $subject, $html];
    }
}

abstract class AbstractTestDefinition extends BaseDefinition
{
}
