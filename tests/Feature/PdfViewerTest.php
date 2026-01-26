<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('public');
});

it('shows pdf viewer page when pdf exists', function (): void {
    Storage::disk('public')->put('pdfs/test-report.pdf', 'dummy pdf content');

    $response = $this->get('/pdf/test-report.pdf');

    $response->assertOk();
    $response->assertViewIs('pdf.viewer');
    $response->assertViewHas('filename', 'test-report.pdf');
    $response->assertViewHas('title', 'test-report');
});

it('returns 404 when pdf does not exist', function (): void {
    $response = $this->get('/pdf/non-existent.pdf');

    $response->assertNotFound();
});

it('streams pdf file when it exists', function (): void {
    Storage::disk('public')->put('pdfs/test-report.pdf', 'dummy pdf content');

    $response = $this->get('/pdf/test-report.pdf/stream');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/pdf');
});

it('returns 404 when streaming non-existent pdf', function (): void {
    $response = $this->get('/pdf/non-existent.pdf/stream');

    $response->assertNotFound();
});

it('only accepts pdf files in route', function (): void {
    $response = $this->get('/pdf/malicious.exe');

    $response->assertNotFound();
});
