<?php

namespace Marktic\Newsletter\Newsletters\Actions\Preview;

use Marktic\Newsletter\Newsletters\Exceptions\InvalidPreviewEmail;
use Marktic\Newsletter\Utility\PackageConfig;

/**
 * Sends the saved newsletter content to one or more test addresses.
 *
 * SendNewsletterPreviewEmail::for($newsletter)->to('a@example.com, b@example.com')->execute();
 */
class SendNewsletterPreviewEmail
{
    public const MAX_RECIPIENTS = 10;
    public const SUBJECT_PREFIX = '[Preview] ';

    protected $newsletter;
    protected mixed $recipients = null;
    protected ?PreviewEmailSenderInterface $sender = null;

    public static function for($newsletter): self
    {
        $action = new static();
        $action->newsletter = $newsletter;
        return $action;
    }

    public function to(mixed $recipients): self
    {
        $this->recipients = $recipients;
        return $this;
    }

    public function withSender(PreviewEmailSenderInterface $sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    /**
     * @return string[] the addresses the preview was sent to
     * @throws InvalidPreviewEmail
     */
    public function execute(): array
    {
        $content = (string)$this->newsletter?->getContent();
        if (trim($content) === '') {
            throw new InvalidPreviewEmail('No saved newsletter content to send. Save your content in the editor first.');
        }

        $recipients = static::parseRecipients($this->recipients);
        $subject = static::SUBJECT_PREFIX . ($this->newsletter->getSubject() ?: $this->newsletter->getName() ?: 'Newsletter');

        $sender = $this->getSender();
        foreach ($recipients as $recipient) {
            $sender->send($recipient, $subject, $content);
        }
        return $recipients;
    }

    /**
     * @return string[]
     * @throws InvalidPreviewEmail
     */
    public static function parseRecipients(mixed $recipients): array
    {
        if (is_string($recipients)) {
            $recipients = preg_split('/[\s,;]+/', $recipients);
        }
        $recipients = array_values(array_unique(array_filter(
            array_map(static fn($email) => is_scalar($email) ? strtolower(trim((string)$email)) : '', (array)$recipients),
            static fn($email) => $email !== ''
        )));

        if (empty($recipients)) {
            throw new InvalidPreviewEmail('Please provide at least one email address.');
        }
        if (count($recipients) > static::MAX_RECIPIENTS) {
            throw new InvalidPreviewEmail(sprintf('A preview can be sent to at most %d addresses.', static::MAX_RECIPIENTS));
        }
        foreach ($recipients as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidPreviewEmail(sprintf('Invalid email address [%s]', $email));
            }
        }
        return $recipients;
    }

    protected function getSender(): PreviewEmailSenderInterface
    {
        return $this->sender ??= static::resolveSender();
    }

    protected static function resolveSender(): PreviewEmailSenderInterface
    {
        $configured = PackageConfig::previewEmailSender();
        if ($configured === null) {
            return new NativeMailPreviewSender();
        }

        $sender = class_exists($configured) ? new $configured() : app($configured);
        if (!$sender instanceof PreviewEmailSenderInterface) {
            throw new InvalidPreviewEmail(sprintf(
                'Preview email sender [%s] must implement %s',
                $configured,
                PreviewEmailSenderInterface::class
            ));
        }
        return $sender;
    }
}
