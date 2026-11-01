<?php

namespace Marktic\Newsletter\Tests\NewsletterFilters;

use Marktic\Newsletter\NewsletterFilters\Exceptions\InvalidFilter;
use Marktic\Newsletter\NewsletterFilters\FilterTypes\ChannelFilterType;
use Marktic\Newsletter\NewsletterFilters\FilterTypes\FilterTypes;
use Marktic\Newsletter\NewsletterFilters\FilterTypes\ListFilterType;
use Marktic\Newsletter\NewsletterFilters\FilterTypes\SubscribedAfterFilterType;
use Marktic\Newsletter\NewsletterFilters\FilterTypes\SubscribedBeforeFilterType;
use Marktic\Newsletter\Tests\AbstractTest;
use PHPUnit\Framework\Attributes\DataProvider;

class FilterTypesTest extends AbstractTest
{
    protected function tearDown(): void
    {
        FilterTypes::reset();
        parent::tearDown();
    }

    public function test_registry_contains_default_types(): void
    {
        self::assertSame(
            ['list', 'channel', 'subscribed_after', 'subscribed_before'],
            array_keys(FilterTypes::all())
        );
        self::assertInstanceOf(ListFilterType::class, FilterTypes::get('list'));
    }

    public function test_registry_rejects_unknown_type(): void
    {
        $this->expectException(InvalidFilter::class);
        FilterTypes::get('unknown');
    }

    public function test_list_type_normalizes_ids(): void
    {
        self::assertSame([3, 5], (new ListFilterType())->normalizeValues('3, 5 ,3'));
        self::assertSame([7], (new ListFilterType())->normalizeValues(['7', '']));
    }

    #[DataProvider('invalidListValues')]
    public function test_list_type_rejects_invalid_values($values): void
    {
        $this->expectException(InvalidFilter::class);
        (new ListFilterType())->normalizeValues($values);
    }

    public static function invalidListValues(): array
    {
        return [[''], ['abc'], ['0'], ['-1'], [null], [['1 OR 1=1']]];
    }

    public function test_list_type_applies_in_condition(): void
    {
        $query = new FakeQuery();
        (new ListFilterType())->applyToQuery($query, [1, 2], 'subs');

        self::assertSame([['`subs`.`list_id` IN ?', [1, 2]]], $query->wheres);
    }

    public function test_channel_type_combines_channels_with_or(): void
    {
        $type = new ChannelFilterType();
        self::assertSame(['email', 'sms'], $type->normalizeValues('Email, sms'));

        $query = new FakeQuery();
        $type->applyToQuery($query, ['email', 'sms'], 'subs');

        self::assertCount(1, $query->wheres);
        self::assertSame(
            '(FIND_IN_SET(?, `subs`.`channels`) > 0 [email]) OR (FIND_IN_SET(?, `subs`.`channels`) > 0 [sms])',
            (string)$query->wheres[0][0]
        );
    }

    public function test_channel_type_rejects_unsafe_values(): void
    {
        $this->expectException(InvalidFilter::class);
        (new ChannelFilterType())->normalizeValues("email'--");
    }

    public function test_date_types(): void
    {
        $after = new SubscribedAfterFilterType();
        $before = new SubscribedBeforeFilterType();
        self::assertSame(['date' => '2026-01-31'], $after->normalizeValues('2026-01-31'));
        self::assertSame(['date' => '2026-01-31'], $before->normalizeValues(['date' => '2026-01-31']));

        $query = new FakeQuery();
        $after->applyToQuery($query, ['date' => '2026-01-01'], 'subs');
        $before->applyToQuery($query, ['date' => '2026-02-01'], 'subs');
        self::assertSame([
            ['DATE(`subs`.`created_at`) >= ?', '2026-01-01'],
            ['DATE(`subs`.`created_at`) <= ?', '2026-02-01'],
        ], $query->wheres);
    }

    public function test_date_type_rejects_invalid_dates(): void
    {
        $this->expectException(InvalidFilter::class);
        (new SubscribedAfterFilterType())->normalizeValues('2026-02-30');
    }
}

class FakeQuery
{
    public array $wheres = [];

    public function where($string, $values = [])
    {
        $this->wheres[] = [$string, $values];
        return $this;
    }

    public function getCondition($string, $values = [])
    {
        return new FakeCondition($string . ' [' . $values . ']');
    }
}

class FakeCondition
{
    public function __construct(public string $string)
    {
    }

    public function or_($condition)
    {
        return new FakeCondition('(' . $this->string . ') OR (' . $condition->string . ')');
    }

    public function __toString(): string
    {
        return $this->string;
    }
}
