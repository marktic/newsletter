<?php

declare(strict_types=1);

namespace Marktic\Newsletter\Bundle\Controllers\Admin;

use Nip\Controllers\Response\ResponsePayload;
use Marktic\Newsletter\Bundle\Library\View\ViewUtility;
use Nip\View\View;

/**
 * @method ResponsePayload payload()
 */
trait AbstractControllerTrait
{
    use \Marktic\Newsletter\Bundle\Controllers\Base\AbstractControllerTrait;

    protected function registerViewPathsNewsletter(View $view): void
    {
        ViewUtility::registerViewPaths($view, 'admin');
    }
}
