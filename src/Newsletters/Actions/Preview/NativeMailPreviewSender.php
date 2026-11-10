<?php

namespace Marktic\Newsletter\Newsletters\Actions\Preview;

use Marktic\Newsletter\Newsletters\Exceptions\InvalidPreviewEmail;
use Marktic\Newsletter\Utility\PackageConfig;

/**
 * Fallback sender using PHP's mail(). Configure `preview.sender` to use your application mailer.
 */
class NativeMailPreviewSender implements PreviewEmailSenderInterface
{
    public function send(string $to, string $subject, string $html): void
    {
        $headers = [
            'MIME-Version' => '1.0',
            'Content-Type' => 'text/html; charset=UTF-8',
        ];
        $from = PackageConfig::previewEmailFrom();
        if ($from && filter_var($from, FILTER_VALIDATE_EMAIL)) {
            $headers['From'] = $from;
        }

        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        if (!mail($to, $encodedSubject, $html, $headers)) {
            throw new InvalidPreviewEmail(sprintf('Could not send preview email to [%s]', $to));
        }
    }
}
