<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\FeePayment;
use App\Models\Guardian;
use App\Models\Mark;
use App\Models\Message;
use App\Models\ReportCard;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ParentPortalController extends Controller
{
    public function dashboard()
    {
        $user     = auth()->user();
        $guardian = Guardian::with('students.schoolClass:id,name', 'students.section:id,name')
            ->where('school_id', $user->school_id)
            ->where('user_id', $user->id)
            ->first();

        if (! $guardian) {
            return Inertia::render('Parent/Dashboard', [
                'guardian'  => null,
                'linked'    => false,
                'children'  => [],
                'announcements' => [],
            ]);
        }

        $now   = Carbon::now();
        $today = $now->toDateString();

        $children = $guardian->students->map(function (Student $student) use ($now, $today) {

            /* Attendance this month */
            $attRows = Attendance::where('school_id', $student->school_id)
                ->where('attendable_type', Student::class)
                ->where('attendable_id', $student->id)
                ->whereMonth('date', $now->month)
                ->whereYear('date', $now->year)
                ->select('status')
                ->get();

            $total   = $attRows->count();
            $present = $attRows->where('status', 'present')->count();

            /* Fee summary */
            $fee = FeePayment::where('school_id', $student->school_id)
                ->where('student_id', $student->id)
                ->select(DB::raw('SUM(amount_due) as due, SUM(amount_paid) as paid'))
                ->first();

            $balance = (float) ($fee->due ?? 0) - (float) ($fee->paid ?? 0);

            /* Recent marks */
            $marks = Mark::where('school_id', $student->school_id)
                ->where('student_id', $student->id)
                ->with(['exam:id,name', 'subject:id,name'])
                ->orderByDesc('created_at')
                ->limit(5)
                ->get()
                ->map(fn ($m) => [
                    'exam'    => $m->exam?->name,
                    'subject' => $m->subject?->name,
                    'marks'   => $m->marks_obtained,
                    'grade'   => $m->grade,
                    'absent'  => $m->is_absent,
                ]);

            /* Recent fee payments */
            $recentFees = FeePayment::where('school_id', $student->school_id)
                ->where('student_id', $student->id)
                ->orderByDesc('payment_date')
                ->limit(3)
                ->get()
                ->map(fn ($f) => [
                    'month'   => $f->month_year,
                    'paid'    => (float) $f->amount_paid,
                    'balance' => (float) ($f->amount_due - $f->amount_paid),
                    'status'  => $f->status,
                ]);

            return [
                'id'           => $student->id,
                'full_name'    => $student->full_name,
                'admission_no' => $student->admission_no,
                'class'        => $student->schoolClass?->name,
                'section'      => $student->section?->name,
                'photo_url'    => $student->photo_url,
                'attendance'   => [
                    'total'      => $total,
                    'present'    => $present,
                    'absent'     => $attRows->where('status', 'absent')->count(),
                    'percentage' => $total ? round(($present / $total) * 100) : 0,
                ],
                'fees' => [
                    'total_due'  => (float) ($fee->due ?? 0),
                    'total_paid' => (float) ($fee->paid ?? 0),
                    'balance'    => $balance,
                    'recent'     => $recentFees,
                ],
                'marks'      => $marks,
            ];
        });

        /* Announcements for parents */
        $announcements = Announcement::where('school_id', $user->school_id)
            ->where(fn ($q) => $q->where('audience', 'all')->orWhere('audience', 'parents'))
            ->orderByDesc('published_at')
            ->limit(5)
            ->get()
            ->map(fn ($a) => [
                'id'     => $a->id,
                'title'  => $a->title,
                'body'   => $a->body,
                'pinned' => $a->is_pinned,
                'date'   => $a->published_at ? Carbon::parse($a->published_at)->diffForHumans() : null,
            ]);

        return Inertia::render('Parent/Dashboard', [
            'linked'       => true,
            'guardian'     => [
                'id'    => $guardian->id,
                'name'  => $guardian->name,
                'phone' => $guardian->phone,
                'email' => $guardian->email,
            ],
            'children'     => $children,
            'announcements'=> $announcements,
        ]);
    }

    private function resolveGuardian()
    {
        $user = auth()->user();
        return Guardian::with('students.schoolClass:id,name', 'students.section:id,name')
            ->where('school_id', $user->school_id)
            ->where('user_id', $user->id)
            ->first();
    }

    private function notLinked(string $page)
    {
        return Inertia::render($page, ['linked' => false, 'guardian' => null, 'children' => []]);
    }

    public function attendance()
    {
        $guardian = $this->resolveGuardian();
        if (! $guardian) return $this->notLinked('Parent/Attendance');

        $now      = Carbon::now();
        $children = $guardian->students->map(function (Student $student) use ($now) {
            $months = [];
            for ($m = 0; $m < 3; $m++) {
                $month = $now->copy()->subMonths($m);
                $rows  = Attendance::where('school_id', $student->school_id)
                    ->where('attendable_type', Student::class)
                    ->where('attendable_id', $student->id)
                    ->whereMonth('date', $month->month)
                    ->whereYear('date', $month->year)
                    ->orderBy('date')
                    ->get(['date', 'status']);

                $total   = $rows->count();
                $present = $rows->where('status', 'present')->count();
                $months[] = [
                    'label'      => $month->format('M Y'),
                    'total'      => $total,
                    'present'    => $present,
                    'absent'     => $rows->where('status', 'absent')->count(),
                    'late'       => $rows->where('status', 'late')->count(),
                    'percentage' => $total ? round($present / $total * 100) : 0,
                    'calendar'   => $rows->map(fn ($r) => ['date' => $r->date, 'status' => $r->status]),
                ];
            }
            return [
                'id'        => $student->id,
                'full_name' => $student->full_name,
                'class'     => $student->schoolClass?->name,
                'section'   => $student->section?->name,
                'months'    => $months,
            ];
        });

        return Inertia::render('Parent/Attendance', [
            'linked'   => true,
            'guardian' => ['name' => $guardian->name],
            'children' => $children,
        ]);
    }

    public function results()
    {
        $guardian = $this->resolveGuardian();
        if (! $guardian) return $this->notLinked('Parent/Results');

        $children = $guardian->students->map(function (Student $student) {
            $marks = \App\Models\Mark::where('school_id', $student->school_id)
                ->where('student_id', $student->id)
                ->with(['exam:id,name,type', 'subject:id,name'])
                ->orderByDesc('created_at')
                ->get()
                ->map(fn ($m) => [
                    'exam'    => $m->exam?->name,
                    'type'    => $m->exam?->type,
                    'subject' => $m->subject?->name,
                    'marks'   => $m->marks_obtained,
                    'total'   => $m->total_marks,
                    'grade'   => $m->grade,
                    'absent'  => $m->is_absent,
                ]);
            return [
                'id'        => $student->id,
                'full_name' => $student->full_name,
                'class'     => $student->schoolClass?->name,
                'marks'     => $marks,
            ];
        });

        return Inertia::render('Parent/Results', [
            'linked'   => true,
            'guardian' => ['name' => $guardian->name],
            'children' => $children,
        ]);
    }

    public function fees()
    {
        $guardian = $this->resolveGuardian();
        if (! $guardian) return $this->notLinked('Parent/Fees');

        $children = $guardian->students->map(function (Student $student) {
            $summary = FeePayment::where('school_id', $student->school_id)
                ->where('student_id', $student->id)
                ->selectRaw('SUM(amount_due) as total_due, SUM(amount_paid) as total_paid')
                ->first();

            $payments = FeePayment::where('school_id', $student->school_id)
                ->where('student_id', $student->id)
                ->orderByDesc('payment_date')
                ->get(['id', 'month_year', 'amount_due', 'amount_paid', 'status', 'payment_date'])
                ->map(fn ($f) => [
                    'month'        => $f->month_year ?? '',
                    'due'          => (float) $f->amount_due,
                    'paid'         => (float) $f->amount_paid,
                    'balance'      => (float) ($f->amount_due - $f->amount_paid),
                    'status'       => $f->status,
                    'payment_date' => $f->payment_date ? Carbon::parse($f->payment_date)->format('d M Y') : null,
                ]);

            return [
                'id'         => $student->id,
                'full_name'  => $student->full_name,
                'class'      => $student->schoolClass?->name,
                'total_due'  => (float) ($summary->total_due ?? 0),
                'total_paid' => (float) ($summary->total_paid ?? 0),
                'balance'    => (float) ($summary->total_due ?? 0) - (float) ($summary->total_paid ?? 0),
                'payments'   => $payments,
            ];
        });

        return Inertia::render('Parent/Fees', [
            'linked'   => true,
            'guardian' => ['name' => $guardian->name],
            'children' => $children,
        ]);
    }

    public function announcements()
    {
        $user     = auth()->user();
        $guardian = Guardian::where('school_id', $user->school_id)->where('user_id', $user->id)->first();
        if (! $guardian) return $this->notLinked('Parent/Announcements');

        $announcements = Announcement::where('school_id', $user->school_id)
            ->where(fn ($q) => $q->where('audience', 'all')->orWhere('audience', 'parents'))
            ->orderByDesc('published_at')
            ->get(['id', 'title', 'body', 'is_pinned', 'published_at'])
            ->map(fn ($a) => [
                'id'     => $a->id,
                'title'  => $a->title,
                'body'   => $a->body,
                'pinned' => $a->is_pinned,
                'date'   => $a->published_at ? Carbon::parse($a->published_at)->format('d M Y') : null,
            ]);

        return Inertia::render('Parent/Announcements', [
            'linked'        => true,
            'guardian'      => ['name' => $guardian->name],
            'announcements' => $announcements,
        ]);
    }

    public function reportCards()
    {
        $user    = auth()->user();
        $guardian = $this->resolveGuardian();
        if (! $guardian) return $this->notLinked('Parent/ReportCards');

        $studentIds = $guardian->students()->where('status', 'active')->pluck('id');

        $reportCards = ReportCard::where('school_id', $user->school_id)
            ->whereIn('student_id', $studentIds)
            ->with(['student:id,first_name,last_name,admission_no', 'academicYear:id,name', 'term:id,name', 'schoolClass:id,name'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($rc) => [
                'id'              => $rc->id,
                'student'         => $rc->student?->full_name,
                'class'           => $rc->schoolClass?->name,
                'academic_year'   => $rc->academicYear?->name,
                'term'            => $rc->term?->name,
                'percentage'      => $rc->percentage,
                'grade'           => $rc->grade,
                'rank'            => $rc->rank,
                'status'          => $rc->status,
                'promotion_status' => $rc->promotion_status,
                'attendance'      => [
                    'total'  => $rc->total_school_days,
                    'present'=> $rc->days_present,
                ],
            ]);

        return Inertia::render('Parent/ReportCards', [
            'linked'      => true,
            'guardian'    => ['name' => $guardian->name],
            'reportCards' => $reportCards,
        ]);
    }

    public function timetable()
    {
        $guardian = $this->resolveGuardian();
        if (! $guardian) return $this->notLinked('Parent/Timetable');

        $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];

        $children = $guardian->students->map(function (Student $student) use ($days) {
            $timetable = [];
            foreach ($days as $day) {
                $slots = \App\Models\Timetable::where('school_id', $student->school_id)
                    ->where('class_id', $student->class_id)
                    ->where('section_id', $student->section_id)
                    ->where('day_of_week', $day)
                    ->with('subject:id,name')
                    ->orderBy('start_time')
                    ->get()
                    ->map(fn ($t) => [
                        'subject'    => $t->subject?->name,
                        'start_time' => substr($t->start_time, 0, 5),
                        'end_time'   => substr($t->end_time, 0, 5),
                        'room'       => $t->room,
                    ]);
                if ($slots->isNotEmpty()) $timetable[$day] = $slots;
            }

            return [
                'id'        => $student->id,
                'full_name' => $student->full_name,
                'class'     => $student->schoolClass?->name,
                'section'   => $student->section?->name,
                'timetable' => $timetable,
            ];
        });

        return Inertia::render('Parent/Timetable', [
            'linked'   => true,
            'guardian' => ['name' => $guardian->name],
            'children' => $children,
            'today'    => strtolower(Carbon::now()->format('l')),
        ]);
    }

    public function profile()
    {
        $guardian = $this->resolveGuardian();
        if (! $guardian) return $this->notLinked('Parent/Profile');

        $children = $guardian->students->map(fn ($s) => [
            'id'           => $s->id,
            'full_name'    => $s->full_name,
            'admission_no' => $s->admission_no,
            'class'        => $s->schoolClass?->name,
            'section'      => $s->section?->name,
        ]);

        return Inertia::render('Parent/Profile', [
            'linked'   => true,
            'guardian' => [
                'id'        => $guardian->id,
                'name'      => $guardian->name,
                'phone'     => $guardian->phone,
                'email'     => $guardian->email,
                'relation'  => $guardian->relation ?? null,
                'address'   => $guardian->address ?? null,
                'occupation' => $guardian->occupation ?? null,
            ],
            'children' => $children,
        ]);
    }

    public function schoolInfo()
    {
        $guardian = $this->resolveGuardian();
        if (! $guardian) return $this->notLinked('Parent/SchoolInfo');

        $school = \App\Models\School::findOrFail(auth()->user()->school_id);

        return Inertia::render('Parent/SchoolInfo', [
            'linked' => true,
            'school' => [
                'name'            => $school->name,
                'email'           => $school->email,
                'phone'           => $school->phone,
                'address'         => $school->address,
                'city'            => $school->city,
                'state'           => $school->state,
                'country'         => $school->country,
                'logo_url'        => $school->logo_url,
                'currency'        => $school->currency,
                'currency_symbol' => $school->currency_symbol,
            ],
        ]);
    }

    public function downloads()
    {
        $guardian = $this->resolveGuardian();
        if (! $guardian) return $this->notLinked('Parent/Downloads');

        $studentIds = $guardian->students()->where('status', 'active')->pluck('id');

        $documents = \App\Models\StudentDocument::where('school_id', auth()->user()->school_id)
            ->whereIn('student_id', $studentIds)
            ->with('student:id,first_name,last_name')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($d) => [
                'id'        => $d->id,
                'title'     => $d->title,
                'student'   => $d->student?->full_name,
                'file_type' => $d->file_type,
                'file_size' => $d->file_size,
                'file_url'  => $d->file_url,
                'date'      => $d->created_at->format('d M Y'),
            ]);

        return Inertia::render('Parent/Downloads', [
            'linked'    => true,
            'guardian'  => ['name' => $guardian->name],
            'documents' => $documents,
        ]);
    }

    public function communication()
    {
        $guardian = $this->resolveGuardian();
        if (! $guardian) return $this->notLinked('Parent/Communication');

        $messages = Message::where('school_id', auth()->user()->school_id)
            ->where('recipient_id', auth()->id())
            ->with('sender:id,name')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(fn ($m) => [
                'id'         => $m->id,
                'subject'    => $m->subject ?? 'Message',
                'body'       => $m->body ?? '',
                'sender'     => $m->sender?->name ?? 'System',
                'is_read'    => ! is_null($m->read_at),
                'created_at' => $m->created_at->format('d M Y H:i'),
            ]);

        return Inertia::render('Parent/Communication', [
            'linked'    => true,
            'guardian'  => ['name' => $guardian->name],
            'messages'  => $messages,
        ]);
    }
}
