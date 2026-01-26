<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PdfViewerController extends Controller
{
    public function show(string $filename): View|Factory
    {
        $path = 'pdfs/' . $filename;

        if (! Storage::disk('public')->exists($path)) {
            throw new NotFoundHttpException(__('PDF not found'));
        }

        return view('pdf.viewer', [
            'filename' => $filename,
            'title' => pathinfo($filename, PATHINFO_FILENAME),
        ]);
    }

    public function stream(string $filename): StreamedResponse
    {
        $path = 'pdfs/' . $filename;

        if (! Storage::disk('public')->exists($path)) {
            throw new NotFoundHttpException(__('PDF not found'));
        }

        return Storage::disk('public')->response($path, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
