<?php

declare(strict_types=1);

namespace App\Services\GoogleAds;

use App\DataTransferObjects\GoogleAdsReportData;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class GoogleAdsPdfGenerator
{
    public function generate(GoogleAdsReportData $data): string
    {
        $html = view('pdf.google-ads-report.report', [
            'data' => $data,
        ])->render();

        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
            ]);

        $filename = $this->generateFilename($data);
        $path = 'pdfs/' . $filename;

        Storage::disk('public')->put($path, $pdf->output());

        return $filename;
    }

    private function generateFilename(GoogleAdsReportData $data): string
    {
        $teamSlug = Str::slug($data->team->name);
        $monthStr = $data->month->format('Y-m');

        return "google-ads-report-{$teamSlug}-{$monthStr}.pdf";
    }
}
