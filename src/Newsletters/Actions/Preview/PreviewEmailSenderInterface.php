<?php

namespace Marktic\Newsletter\Newsletters\Actions\Preview;

interface PreviewEmailSenderInterface
{
    public function send(string $to, string $subject, string $html): void;
}
