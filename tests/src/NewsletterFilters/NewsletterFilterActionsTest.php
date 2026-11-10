<?php

namespace Marktic\Newsletter\Tests\NewsletterFilters;

use Marktic\Newsletter\NewsletterFilters\Actions\ApplyNewsletterFiltersToQuery;
use Marktic\Newsletter\NewsletterFilters\Actions\CreateNewsletterFilter;
use Marktic\Newsletter\NewsletterFilters\Actions\DeleteNewsletterFilter;
use Marktic\Newsletter\NewsletterFilters\Exceptions\InvalidFilter;
use Marktic\Newsletter\NewsletterFilters\Models\NewsletterFilter;
use Marktic\Newsletter\NewsletterFilters\Models\NewsletterFilters;
use Marktic\Newsletter\Tests\AbstractTest;
use Mockery;

class NewsletterFilterActionsTest extends AbstractTest
{
    public function test_create_validates_and_saves_filter(): void
    {
        $filter = Mockery::mock(NewsletterFilter::class)->makePartial();
        $filter->shouldReceive('save')->once();
        $repository = Mockery::mock(NewsletterFilters::class);
        $repository->shouldReceive('getNew')->once()->andReturn($filter);

        $result = CreateNewsletterFilter::for($this->newsletter(12))
            ->withType('list')
            ->withValues('4,9')
            ->withRepository($repository)
            ->execute();

        self::assertSame($filter, $result);
        self::assertSame(12, $result->getIdNewsletter());
        self::assertSame('list', $result->getType());
        self::assertSame([4, 9], $result->getValues());
        self::assertSame('Lists: 4, 9', $result->getDescription());
    }

    public function test_create_rejects_unknown_type_without_saving(): void
    {
        $repository = Mockery::mock(NewsletterFilters::class);
        $repository->shouldNotReceive('getNew');

        $this->expectException(InvalidFilter::class);
        CreateNewsletterFilter::for($this->newsletter(12))
            ->withType('nope')
            ->withValues('1')
            ->withRepository($repository)
            ->execute();
    }

    public function test_create_requires_saved_newsletter(): void
    {
        $this->expectException(InvalidFilter::class);
        CreateNewsletterFilter::for($this->newsletter(null))
            ->withType('list')
            ->withValues('1')
            ->withRepository(Mockery::mock(NewsletterFilters::class))
            ->execute();
    }

    public function test_delete_only_removes_filters_of_the_newsletter(): void
    {
        $filter = Mockery::mock(NewsletterFilter::class)->makePartial();
        $filter->setIdNewsletter(12);
        $filter->shouldReceive('delete')->once();
        $repository = Mockery::mock(NewsletterFilters::class);
        $repository->shouldReceive('findOne')->with(5)->andReturn($filter);

        DeleteNewsletterFilter::for($this->newsletter(12), '5')->withRepository($repository)->execute();

        $this->expectException(InvalidFilter::class);
        DeleteNewsletterFilter::for($this->newsletter(13), '5')->withRepository($repository)->execute();
    }

    public function test_apply_filters_to_query(): void
    {
        $list = new NewsletterFilter();
        $list->setType('list');
        $list->setValues([1]);
        $after = new NewsletterFilter();
        $after->setType('subscribed_after');
        $after->setValues(['date' => '2026-01-01']);

        $query = new FakeQuery();
        ApplyNewsletterFiltersToQuery::for([$list, $after])->withTable('subs')->apply($query);

        self::assertSame([
            ['`subs`.`list_id` IN ?', [1]],
            ['DATE(`subs`.`created_at`) >= ?', '2026-01-01'],
        ], $query->wheres);
    }

    private function newsletter(?int $id): object
    {
        return new class($id) {
            public function __construct(private ?int $id)
            {
            }

            public function getId(): ?int
            {
                return $this->id;
            }
        };
    }
}
