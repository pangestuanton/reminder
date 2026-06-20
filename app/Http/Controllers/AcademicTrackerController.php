<?php

namespace App\Http\Controllers;

use App\Models\CourseGrade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicTrackerController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $grades = CourseGrade::where('user_id', $user->id)
            ->orderBy('semester')
            ->orderBy('mata_kuliah')
            ->get();

        // Group by semester
        $groupedGrades = $grades->groupBy('semester');

        $semesterStats = [];
        $cumulativeSks = 0;
        $cumulativePoints = 0;

        foreach ($groupedGrades as $semesterNum => $semesterGrades) {
            $totalSks = $semesterGrades->sum('sks');
            $totalPoints = $semesterGrades->sum(function ($grade) {
                return $grade->sks * $grade->grade_point;
            });
            $ips = $totalSks > 0 ? round($totalPoints / $totalSks, 2) : 0.0;

            $semesterStats[$semesterNum] = [
                'grades' => $semesterGrades,
                'total_sks' => $totalSks,
                'ips' => $ips,
            ];

            $cumulativeSks += $totalSks;
            $cumulativePoints += $totalPoints;
        }

        $ipk = $cumulativeSks > 0 ? round($cumulativePoints / $cumulativeSks, 2) : 0.0;

        // Build chart coordinates
        $chartPoints = [];
        $linePath = '';
        $fillPath = '';
        $width = 500;
        $height = 150;
        $padding = 30;

        ksort($semesterStats);
        $semesters = array_keys($semesterStats);
        $totalSemesters = count($semesters);

        if ($totalSemesters > 0) {
            $xCoords = [];
            $yCoords = [];

            foreach ($semesters as $index => $sem) {
                // X coordinate
                if ($totalSemesters === 1) {
                    $x = $width / 2;
                } else {
                    $x = $padding + $index * (($width - 2 * $padding) / ($totalSemesters - 1));
                }

                // Y coordinate (mapping 0.0 - 4.0 GPA to SVG height)
                $ipsValue = $semesterStats[$sem]['ips'];
                $y = ($height - $padding) - ($ipsValue * (($height - 2 * $padding) / 4));

                $xCoords[] = $x;
                $yCoords[] = $y;

                $chartPoints[] = [
                    'semester' => $sem,
                    'ips' => $ipsValue,
                    'x' => $x,
                    'y' => $y,
                ];
            }

            // Create SVG line and fill path strings
            $lineSegments = [];
            for ($i = 0; $i < count($xCoords); $i++) {
                $lineSegments[] = "{$xCoords[$i]},{$yCoords[$i]}";
            }

            if (count($lineSegments) > 0) {
                $linePath = 'M ' . implode(' L ', $lineSegments);
                
                // For fill, close the shape down to the bottom axis
                $bottomY = $height - $padding;
                $fillPath = "M {$xCoords[0]},{$bottomY} L " . implode(' L ', $lineSegments) . " L " . end($xCoords) . ",{$bottomY} Z";
            }
        }

        return view('academic-tracker.index', [
            'semesterStats' => $semesterStats,
            'cumulativeSks' => $cumulativeSks,
            'ipk' => $ipk,
            'chartPoints' => $chartPoints,
            'linePath' => $linePath,
            'fillPath' => $fillPath,
            'chartWidth' => $width,
            'chartHeight' => $height,
            'chartPadding' => $padding,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'semester' => 'required|integer|min:1|max:20',
            'mata_kuliah' => 'required|string|max:255',
            'sks' => 'required|integer|min:1|max:6',
            'nilai' => 'required|string|in:A,AB,B,BC,C,D,E',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['nilai'] = strtoupper($validated['nilai']);

        CourseGrade::create($validated);

        return redirect()->route('academic-tracker.index')->with('success', 'Nilai mata kuliah berhasil disimpan.');
    }

    public function destroy(CourseGrade $courseGrade, Request $request): RedirectResponse
    {
        if ($courseGrade->user_id !== $request->user()->id) {
            abort(403);
        }

        $courseGrade->delete();

        return redirect()->route('academic-tracker.index')->with('success', 'Mata kuliah berhasil dihapus.');
    }
}
