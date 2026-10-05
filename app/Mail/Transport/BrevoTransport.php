<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;

// Brevo has no official Laravel/Symfony Mailer integration, so this calls
// their HTTP API (api.brevo.com) directly — over HTTPS/443, not SMTP/587,
// which is what Railway's free tier blocks. Modeled on Laravel's own
// ResendTransport (vendor/laravel/framework/.../Transport/ResendTransport.php).
class BrevoTransport extends AbstractTransport
{
    public function __construct(protected string $apiKey)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        $envelope = $message->getEnvelope();

        $payload = [
            'sender' => $this->formatAddress($envelope->getSender()),
            'to' => $this->formatAddresses($this->getRecipients($email, $envelope)),
            'subject' => $email->getSubject(),
        ];

        if ($email->getCc()) {
            $payload['cc'] = $this->formatAddresses($email->getCc());
        }
        if ($email->getBcc()) {
            $payload['bcc'] = $this->formatAddresses($email->getBcc());
        }
        if ($email->getReplyTo()) {
            $payload['replyTo'] = $this->formatAddress($email->getReplyTo()[0]);
        }
        if ($email->getHtmlBody()) {
            $payload['htmlContent'] = $email->getHtmlBody();
        }
        if ($email->getTextBody()) {
            $payload['textContent'] = $email->getTextBody();
        }

        $attachments = [];
        foreach ($email->getAttachments() as $attachment) {
            $headers = $attachment->getPreparedHeaders();
            $filename = $headers->getHeaderParameter('Content-Disposition', 'filename');
            $attachments[] = [
                'content' => base64_encode($attachment->getBody()),
                'name' => $filename ?? 'attachment',
            ];
        }
        if ($attachments) {
            $payload['attachment'] = $attachments;
        }

        $response = Http::withHeaders([
            'api-key' => $this->apiKey,
            'Accept' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', $payload);

        if ($response->failed()) {
            throw new TransportException(
                sprintf('Request to Brevo API failed. Reason: %s.', $response->json('message') ?? $response->body()),
                $response->status()
            );
        }

        if ($messageId = $response->json('messageId')) {
            $message->setMessageId($messageId);
        }
    }

    protected function getRecipients(Email $email, Envelope $envelope): array
    {
        return array_filter($envelope->getRecipients(), function (Address $address) use ($email) {
            return ! in_array($address, array_merge($email->getCc(), $email->getBcc()), true);
        });
    }

    protected function formatAddress(Address $address): array
    {
        return array_filter([
            'email' => $address->getAddress(),
            'name' => $address->getName() ?: null,
        ]);
    }

    protected function formatAddresses(array $addresses): array
    {
        return array_map(fn (Address $address) => $this->formatAddress($address), array_values($addresses));
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}
