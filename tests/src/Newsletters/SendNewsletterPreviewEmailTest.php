<?php

namespace Marktic\Newsletter\Tests\Newsletters;

use Marktic\Newsletter\Newsletters\Actions\Preview\PreviewEmailSenderInterface;
use Marktic\Newsletter\Newsletters\Actions\Preview\SendNewsletterPreviewEmail;
use Marktic\Newsletter\Newsletters\Exceptions\InvalidPreviewEmail;
use Marktic\Newsletter\Tests\AbstractTest;
use PHPUnit\Framework\Attributes\DataProvider;

class SendNewsletterPreviewEmailTest extends AbstractTest
{
    public function test_sends_content_to_each_recipient(): void
    {
        $sender = $this->sender();

        $sent = SendNewsletterPreviewEmail::for($this->newsletter('<p>Hi</p>'))
            ->to('A@example.com, b@example.com; a@example.com')
            ->withSender($sender)
            ->execute();

        self::assertSame(['a@example.com', 'b@example.com'], $sent);
        self::assertSame([
            ['a@example.com', '[Preview] Weekly subject', '<p>Hi</p>'],
            ['b@example.com', '[Preview] Weekly subject', '<p>Hi</p>'],
        ], $sender->sent);
    }

    #[DataProvider('invalidRecipients')]
    public function test_rejects_invalid_recipients($recipients): void
    {
        $sender = $this->sender();
        try {
            SendNewsletterPreviewEmail::for($this->newsletter('<p>Hi</p>'))
                ->to($recipients)->withSender($sender)->execute();
            self::fail('Expected exception');
        } catch (InvalidPreviewEmail) {
            self::assertSame([], $sender->sent);
        }
    }

    public static function invalidRecipients(): array
    {
        $tooMany = implode(',', array_map(static fn($i) => "u$i@example.com", range(1, 11)));
        return [[''], [null], ['not-an-email'], ['ok@example.com, bad'], [$tooMany]];
    }

    public function test_requires_saved_content(): void
    {
        $this->expectException(InvalidPreviewEmail::class);
        SendNewsletterPreviewEmail::for($this->newsletter('  '))
            ->to('a@example.com')->withSender($this->sender())->execute();
    }

    private function sender(): PreviewEmailSenderInterface
    {
        return new class implements PreviewEmailSenderInterface {
            public array $sent = [];

            public function send(string $to, string $subject, string $html): void
            {
                $this->sent[] = [$to, $subject, $html];
            }
        };
    }

    private function newsletter(?string $content): object
    {
        return new class($content) {
            public function __construct(private ?string $content)
            {
            }

            public function getContent(): ?string
            {
                return $this->content;
            }

            public function getSubject(): string
            {
                return 'Weekly subject';
            }

            public function getName(): string
            {
                return 'Weekly';
            }
        };
    }
}
