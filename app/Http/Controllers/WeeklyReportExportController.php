<?php

namespace App\Http\Controllers;

use App\Models\WeeklyReport;
use App\Services\WeeklyReportAggregationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class WeeklyReportExportController extends Controller
{
    public function __invoke(WeeklyReport $weeklyReport, WeeklyReportAggregationService $service)
    {
        $user = auth()->user();

        abort_unless($user !== null, 403);

        if ($user->role !== 'admin') {
            abort_unless(
                $weeklyReport->project->leads()->whereKey($user->id)->exists(),
                403
            );
        }

        $payload = $service->buildExportPayload($weeklyReport);
        $pdf = Pdf::loadView('weekly-reports.pdf', $payload)->setPaper('a4');
        $path = sprintf(
            'weekly-reports/%s-week-%s.pdf',
            $weeklyReport->project->code,
            $weeklyReport->report_number
        );

        Storage::disk('public')->put($path, $pdf->output());

        $weeklyReport->update([
            'generated_by_user_id' => $user->id,
            'generated_pdf_path' => $path,
        ]);

        return $pdf->download(sprintf('%s-week-%s.pdf', $weeklyReport->project->code, $weeklyReport->report_number));
    }
}
