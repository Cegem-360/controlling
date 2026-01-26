<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Team;
use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class GoogleAdsReportMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly Team $team,
        public readonly string $pdfPath,
        public readonly CarbonInterface $reportMonth,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Google Ads Havi Riport - {$this->team->name} - {$this->reportMonth->format('Y. F')}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.google-ads-report',
            with: [
                'team' => $this->team,
                'reportMonth' => $this->reportMonth,
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromStorage($this->pdfPath)
                ->as("google-ads-report-{$this->reportMonth->format('Y-m')}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
