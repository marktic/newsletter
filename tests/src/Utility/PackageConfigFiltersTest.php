<?php

namespace Marktic\Newsletter\Tests\Utility;

use Marktic\Newsletter\NewsletterFilters\Actions\CreateNewsletterFilter;
use Marktic\Newsletter\NewsletterFilters\Exceptions\InvalidFilter;
use Marktic\Newsletter\NewsletterFilters\FilterTypes\ChannelFilterType;
use Marktic\Newsletter\NewsletterFilters\FilterTypes\FilterTypes;
use Marktic\Newsletter\NewsletterFilters\Models\NewsletterFilter;
use Marktic\Newsletter\NewsletterFilters\Models\NewsletterFilters;
use Marktic\Newsletter\Newsletters\Models\NewsletterNewsletter;
use Marktic\Newsletter\Tests\AbstractTest;
use Marktic\Newsletter\Tests\Fixtures\NewsletterDefinitions\TestDefinition;
use Marktic\Newsletter\Utility\PackageConfig;
use Mockery;
use Nip\Config\Config;
use Nip\Container\Utility\Container;

class PackageConfigFiltersTest extends AbstractTest
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
        PackageConfig::resetInstance();
        FilterTypes::reset();
        parent::tearDown();
    }

    public function test_no_definition_defaults_to_empty_list(): void
    {
        self::assertSame([], PackageConfig::availableFilterTypes('organizations', 'weekly'));
        self::assertSame([], FilterTypes::forNewsletter($this->newsletter('organizations', 'weekly')));

        $this->expectException(InvalidFilter::class);
        CreateNewsletterFilter::for($this->newsletter('organizations', 'weekly'))
            ->withType('list')->withValues('1')->execute();
    }

    public function test_explicit_empty_types_configuration_preserves_builtin_registry(): void
    {
        $this->configure(['types' => []]);

        self::assertSame([], PackageConfig::filterTypes());
        self::assertSame(
            ['list', 'channel', 'subscribed_after', 'subscribed_before'],
            array_keys(FilterTypes::all())
        );
    }

    public function test_configured_type_classes_are_unwrapped(): void
    {
        $this->configure(['types' => [ChannelFilterType::class]]);

        self::assertSame([ChannelFilterType::class], PackageConfig::filterTypes());
        self::assertInstanceOf(ChannelFilterType::class, FilterTypes::get('channel'));
    }

    public function test_configured_type_instances_are_preserved(): void
    {
        $type = new ChannelFilterType();
        $this->configure(['types' => [$type]]);

        self::assertSame([$type], PackageConfig::filterTypes());
        self::assertSame($type, FilterTypes::get('channel'));
    }

    public function test_definitions_are_scoped_by_both_owner_and_newsletter_type(): void
    {
        $this->configure([
            'available' => [
                'organizations' => ['weekly' => ['list'], 'daily' => ['channel']],
                'teams' => ['weekly' => ['subscribed_after']],
            ],
        ]);

        self::assertSame(['list'], array_keys(FilterTypes::forNewsletter($this->newsletter('organizations', 'weekly'))));
        self::assertSame(['channel'], PackageConfig::availableFilterTypes('organizations', 'daily'));
        self::assertSame(['subscribed_after'], PackageConfig::availableFilterTypes('teams', 'weekly'));
        self::assertSame([], PackageConfig::availableFilterTypes('teams', 'daily'));
    }

    public function test_missing_match_uses_default_but_explicit_empty_match_does_not(): void
    {
        $this->configure([
            'default' => ['channel'],
            'available' => ['organizations' => ['weekly' => []]],
        ]);

        self::assertSame([], PackageConfig::availableFilterTypes('organizations', 'weekly'));
        self::assertSame(['channel'], PackageConfig::availableFilterTypes('organizations', 'daily'));
        self::assertSame(['channel'], PackageConfig::availableFilterTypes(null, 'weekly'));
        self::assertSame(['channel'], PackageConfig::availableFilterTypes('organizations', null));
    }

    public function test_unknown_configured_filter_is_reported(): void
    {
        $this->configure(['default' => ['unknown']]);

        $this->expectException(InvalidFilter::class);
        FilterTypes::forNewsletter($this->newsletter(null, null));
    }

    public function test_direct_creation_uses_owner_and_type_configuration(): void
    {
        $this->configure(['available' => ['organizations' => ['weekly' => ['list']]]]);
        $filter = Mockery::mock(NewsletterFilter::class)->makePartial();
        $filter->shouldReceive('save')->once();
        $repository = Mockery::mock(NewsletterFilters::class);
        $repository->shouldReceive('getNew')->once()->andReturn($filter);

        $result = CreateNewsletterFilter::for($this->newsletter('organizations', 'weekly'))
            ->withType('list')->withValues('3,4')->withRepository($repository)->execute();

        self::assertSame([3, 4], $result->getValues());
        self::assertSame(12, $result->getIdNewsletter());
    }

    public function test_direct_creation_uses_the_selected_definition_instead_of_legacy_filters(): void
    {
        Container::container()->set('config', new Config([
            'mkt_newsletter' => [
                'definitions' => ['available' => ['organizations' => ['weekly' => TestDefinition::class]]],
                'filters' => ['default' => ['channel']],
            ],
        ], true));
        PackageConfig::resetInstance();
        $filter = Mockery::mock(NewsletterFilter::class)->makePartial();
        $filter->shouldReceive('save')->once();
        $repository = Mockery::mock(NewsletterFilters::class);
        $repository->shouldReceive('getNew')->once()->andReturn($filter);
        $newsletter = $this->newsletter('organizations', 'weekly');

        $result = CreateNewsletterFilter::for($newsletter)
            ->withType('list')->withValues('3')->withRepository($repository)->execute();

        self::assertSame([3], $result->getValues());
        $this->expectException(InvalidFilter::class);
        CreateNewsletterFilter::for($newsletter)->withType('channel')->withValues('email')->execute();
    }

    private function configure(array $filters): void
    {
        Container::container()->set('config', new Config(['mkt_newsletter' => ['filters' => $filters]], true));
        PackageConfig::resetInstance();
        FilterTypes::reset();
    }

    private function newsletter(?string $owner, ?string $type): NewsletterNewsletter
    {
        $newsletter = new NewsletterNewsletter();
        $newsletter->setId(12);
        $newsletter->setOwner($owner);
        $newsletter->setType($type);
        return $newsletter;
    }
}
