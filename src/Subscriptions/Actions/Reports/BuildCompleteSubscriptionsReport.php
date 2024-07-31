<?php

namespace Marktic\Newsletter\Subscriptions\Actions\Reports;

use Bytic\Actions\Action;
use Marktic\Newsletter\Subscriptions\Models\Reports\CompleteSubscriptions\CompleteSubscriptionsReport;

class BuildCompleteSubscriptionsReport extends Action
{
    protected $filterSession;

    protected $report = null;

    public static function fromRequestFilters($filterSession)
    {
        $action = new static();
        $action->filterSession = $filterSession;
        return $action;
    }

    public function render()
    {
        $report = $this->getReport();

        ini_set('memory_limit', '1024M');

        $type = 'Xlsx';
        $report->getWriter($type)->render();
    }

    public function getReport()
    {
       if ($this->report === null) {
           $this->report = $this->generateReport();
       }
       return $this->report;
    }

    protected function generateReport(): CompleteSubscriptionsReport
    {
        $report = new CompleteSubscriptionsReport();
        $report->setParam('filters', $this->filterSession->getFiltersArray());
        return $report;
    }
}
