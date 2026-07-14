<?php

namespace App\Http\Controllers\SchoolAdmin;

use App\Http\Controllers\Controller;
use App\Models\DocumentImport;
use App\Services\DocumentExtractionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ResultImportController extends Controller
{
    /**
     * Show import dashboard.
     */
    public function index(Request $request): Response
    {
        $imports = DocumentImport::with('user:id,name')
            ->where('school_id', $this->getSchoolId())
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->document_type, fn ($q) => $q->where('document_type', $request->document_type))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('SchoolAdmin/Imports/Index', [
            'imports' => [
                'data' => $imports->items(),
                'meta' => [
                    'total' => $imports->total(), 'per_page' => $imports->perPage(),
                    'current_page' => $imports->currentPage(), 'last_page' => $imports->lastPage(),
                    'from' => $imports->firstItem(), 'to' => $imports->lastItem(),
                ],
                'links' => ['prev' => $imports->previousPageUrl(), 'next' => $imports->nextPageUrl()],
            ],
            'filters' => $request->only(['status', 'document_type']),
            'stats' => [
                'total'     => DocumentImport::where('school_id', $this->getSchoolId())->count(),
                'uploaded'  => DocumentImport::where('school_id', $this->getSchoolId())->where('status', 'uploaded')->count(),
                'processing'=> DocumentImport::where('school_id', $this->getSchoolId())->where('status', 'processing')->count(),
                'imported'  => DocumentImport::where('school_id', $this->getSchoolId())->where('status', 'imported')->count(),
                'failed'    => DocumentImport::where('school_id', $this->getSchoolId())->where('status', 'failed')->count(),
            ],
        ]);
    }

    /**
     * Upload a document for AI extraction.
     */
    public function upload(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'file'          => 'required|file|max:20480|mimes:jpg,jpeg,png,pdf,webp',
            'document_type' => 'required|string|in:report_card,admission_form,attendance_register,fee_receipt,certificate,staff_record',
        ]);

        try {
            $service = new DocumentExtractionService($this->getSchoolId(), auth()->id());
            $import = $service->uploadAndExtract($request->file('file'), $data['document_type']);

            return redirect()->route('school.imports.show', $import->id)
                ->with('success', 'Document uploaded. Click "Extract" to begin AI analysis.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Show a single import with extracted data.
     */
    public function show(DocumentImport $import): Response
    {
        abort_unless($import->school_id === $this->getSchoolId(), 403);

        return Inertia::render('SchoolAdmin/Imports/Show', [
            'import' => $import->load('user:id,name'),
        ]);
    }

    /**
     * Trigger AI extraction on an uploaded document.
     */
    public function extract(DocumentImport $import): RedirectResponse
    {
        abort_unless($import->school_id === $this->getSchoolId(), 403);

        if (!$import->isUploaded()) {
            return back()->with('error', 'This document has already been processed.');
        }

        try {
            $service = new DocumentExtractionService($this->getSchoolId(), auth()->id());
            $service->extract($import);

            return back()->with('success', 'AI extraction complete. Review the extracted data below.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Extraction failed: ' . $e->getMessage());
        }
    }

    /**
     * Update reviewed/corrected extracted data.
     */
    public function updateData(Request $request, DocumentImport $import): RedirectResponse
    {
        abort_unless($import->school_id === $this->getSchoolId(), 403);

        $data = $request->validate([
            'extracted_data'   => 'required|array',
            'admin_notes'      => 'nullable|string|max:1000',
        ]);

        try {
            $import->update([
                'extracted_data' => $data['extracted_data'],
                'admin_notes'    => $data['admin_notes'] ?? null,
                'status'         => 'reviewed',
            ]);

            return back()->with('success', 'Extracted data updated. Ready for import.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Failed to update: ' . $e->getMessage());
        }
    }

    /**
     * Import the reviewed data into the database.
     */
    public function import(DocumentImport $import): RedirectResponse
    {
        abort_unless($import->school_id === $this->getSchoolId(), 403);

        if (!$import->isReviewed() && !$import->isExtracted()) {
            return back()->with('error', 'Document must be reviewed before importing.');
        }

        try {
            $service = new DocumentExtractionService($this->getSchoolId(), auth()->id());
            $results = $service->importResults($import, $import->extracted_data, true);

            return back()->with('success', "Import complete: {$results['imported']} records imported, {$results['skipped']} skipped.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Delete an import record.
     */
    public function destroy(DocumentImport $import): RedirectResponse
    {
        abort_unless($import->school_id === $this->getSchoolId(), 403);

        try {
            $import->delete();
            return back()->with('success', 'Import record deleted.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to delete: ' . $e->getMessage());
        }
    }
}
