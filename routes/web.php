<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\SchoolAdmin\AttendanceController;
use App\Http\Controllers\SchoolAdmin\ExamController;
use App\Http\Controllers\SchoolAdmin\FeeCategoryController;
use App\Http\Controllers\SchoolAdmin\FeePaymentController;
use App\Http\Controllers\SchoolAdmin\FeeStructureController;
use App\Http\Controllers\SchoolAdmin\CommunicationController;
use App\Http\Controllers\SchoolAdmin\IntegrationController;
use App\Http\Controllers\SchoolAdmin\ReportController;
use App\Http\Controllers\SchoolAdmin\HomeworkController;
use App\Http\Controllers\SchoolAdmin\LeaveController;
use App\Http\Controllers\SchoolAdmin\AssetController;
use App\Http\Controllers\SchoolAdmin\HostelController;
use App\Http\Controllers\SchoolAdmin\TransportController;
use App\Http\Controllers\SchoolAdmin\InventoryController;
use App\Http\Controllers\SchoolAdmin\LibraryController;
use App\Http\Controllers\SchoolAdmin\PayrollController;
use App\Http\Controllers\SchoolAdmin\TimetableController;
use App\Http\Controllers\SchoolAdmin\ClassController;
use App\Http\Controllers\SchoolAdmin\DepartmentController;
use App\Http\Controllers\SchoolAdmin\DesignationController;
use App\Http\Controllers\SchoolAdmin\HolidayController;
use App\Http\Controllers\SchoolAdmin\SectionController;
use App\Http\Controllers\SchoolAdmin\ShiftController;
use App\Http\Controllers\SchoolAdmin\StaffController;
use App\Http\Controllers\SchoolAdmin\StudentController;
use App\Http\Controllers\SchoolAdmin\SubjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SchoolAdmin\SchoolUserController;
use App\Http\Controllers\SchoolAdmin\SettingsController as SchoolSettingsController;
use App\Http\Controllers\SuperAdmin\SettingsController as SuperAdminSettingsController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SchoolAdmin\AdmissionInquiryController;
use App\Http\Controllers\SchoolAdmin\VisitorLogController;
use App\Http\Controllers\PublicAdmissionController;
use App\Http\Controllers\StudentPortalController;
use App\Http\Controllers\ParentPortalController;
use App\Http\Controllers\TeacherPortalController;
use App\Http\Controllers\AccountantPortalController;
use App\Http\Controllers\PrincipalPortalController;
use App\Http\Controllers\ProprietorPortalController;
use App\Http\Controllers\LibrarianPortalController;
use App\Http\Controllers\DriverPortalController;
use App\Http\Controllers\WardenPortalController;
use App\Http\Controllers\StoreManagerPortalController;
use App\Http\Controllers\MinistryPortalController;
use App\Http\Controllers\SuperAdmin\CouponController;
use App\Http\Controllers\SuperAdmin\ModuleManagerController;
use App\Http\Controllers\SuperAdmin\PackageController;
use App\Http\Controllers\SuperAdmin\SchoolController;
use App\Http\Controllers\SuperAdmin\DemoManagementController;
use App\Http\Controllers\DemoRequestController;
use App\Http\Controllers\SuperAdmin\SubscriptionController;
use App\Http\Controllers\SuperAdmin\UserManagementController;
use App\Http\Controllers\SuperAdmin\TwoFactorController;
use App\Http\Controllers\SchoolAdmin\AcademicTermController;
use App\Http\Controllers\SchoolAdmin\ReportCardController;
use App\Http\Controllers\SchoolAdmin\NationalExaminationController;
use App\Http\Controllers\SchoolAdmin\SierraLeoneSettingsController;
use App\Http\Controllers\SchoolAdmin\SchoolIdentityController;
use App\Http\Controllers\ResultChangeRequestController;
use App\Http\Controllers\AttendanceApprovalController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\PrincipalPerformanceController;
use App\Http\Controllers\ProprietorPerformanceController;
use App\Http\Controllers\StudentPerformanceController;
use App\Http\Controllers\ParentPerformanceController;
use App\Http\Controllers\TeacherPerformanceController;
use App\Http\Controllers\SchoolAdminPerformanceController;
use App\Http\Controllers\SuperAdminPerformanceController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Guest routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/', fn () => Inertia::render('Public/Homepage'))->name('home');
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:login');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:register');
});

/*
|--------------------------------------------------------------------------
| Public pages (no auth required)
|--------------------------------------------------------------------------
*/
Route::get('/about',            fn () => Inertia::render('Public/About'))->name('about');
Route::get('/contact',          fn () => Inertia::render('Public/Contact'))->name('contact');
Route::get('/privacy',          fn () => Inertia::render('Public/Privacy'))->name('privacy');
Route::get('/terms',            fn () => Inertia::render('Public/Terms'))->name('terms');
Route::get('/support',          fn () => Inertia::render('Public/Support'))->name('support');
Route::get('/training',         fn () => Inertia::render('Public/Training'))->name('training');
Route::get('/vision-mission',   fn () => Inertia::render('Public/VisionMission'))->name('vision-mission');

// Platform feature pages
Route::get('/platform/students',     fn () => Inertia::render('Public/PlatformStudents'))->name('platform.students');
Route::get('/platform/fees',         fn () => Inertia::render('Public/PlatformFees'))->name('platform.fees');
Route::get('/platform/exams',        fn () => Inertia::render('Public/PlatformExams'))->name('platform.exams');
Route::get('/platform/analytics',    fn () => Inertia::render('Public/PlatformAnalytics'))->name('platform.analytics');
Route::get('/platform/communication', fn () => Inertia::render('Public/PlatformCommunication'))->name('platform.communication');

// Solution pages
Route::get('/solutions/nursery',     fn () => Inertia::render('Public/SolutionsNursery'))->name('solutions.nursery');
Route::get('/solutions/primary',     fn () => Inertia::render('Public/SolutionsPrimary'))->name('solutions.primary');
Route::get('/solutions/secondary',   fn () => Inertia::render('Public/SolutionsSecondary'))->name('solutions.secondary');
Route::get('/solutions/combined',    fn () => Inertia::render('Public/SolutionsCombined'))->name('solutions.combined');
Route::get('/solutions/multi-school', fn () => Inertia::render('Public/SolutionsMultiSchool'))->name('solutions.multi-school');

// Security page
Route::get('/security', fn () => Inertia::render('Public/Security'))->name('security');

/*
|--------------------------------------------------------------------------
| Demo Request (public)
|--------------------------------------------------------------------------
*/
Route::get('/request-demo',       [DemoRequestController::class, 'show'])->name('demo.request');
Route::post('/request-demo',      [DemoRequestController::class, 'submit'])->middleware('throttle:demo')->name('demo.submit');
Route::get('/request-demo/thank-you', [DemoRequestController::class, 'thankYou'])->name('demo.thank-you');

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Profile
    Route::get('/profile',                   [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile',                   [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/password/change',           [ProfileController::class, 'changePasswordPage'])->name('password.change');
    Route::put('/profile/password',          [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/dashboard', function () {
        $user = auth()->user();

        // Redirect to role-specific dashboard
        return match (true) {
            $user->hasRole('super-admin')                                   => redirect()->route('super-admin.dashboard'),
            $user->hasRole('school-admin')                                  => redirect()->route('school.reports.dashboard'),
            $user->hasRole('principal')                                      => redirect()->route('principal.dashboard'),
            $user->hasRole('teacher')                                        => redirect()->route('teacher.dashboard'),
            $user->hasRole('student')                                        => redirect()->route('student.dashboard'),
            $user->hasRole('parent')                                         => redirect()->route('parent.dashboard'),
            $user->hasRole('accountant')                                     => redirect()->route('accountant.dashboard'),
            $user->hasRole('proprietor')                                     => redirect()->route('proprietor.dashboard'),
            $user->hasRole('librarian')                                      => redirect()->route('librarian.dashboard'),
            $user->hasRole('driver')                                         => redirect()->route('driver.dashboard'),
            $user->hasRole('warden')                                         => redirect()->route('warden.dashboard'),
            $user->hasRole('store-manager')                                  => redirect()->route('store-manager.dashboard'),
            $user->hasRole('ministry-admin')                                  => redirect()->route('ministry.dashboard'),
            $user->hasRole('district-officer')                                => redirect()->route('ministry.dashboard'),
            default                                                          => redirect()->route('school.reports.dashboard'),
        };
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | School Admin routes (school-admin, principal)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:super-admin|school-admin|principal|teacher|accountant|librarian')
        ->prefix('school')
        ->name('school.')
        ->group(function () {
            Route::resource('classes',  ClassController::class)->except(['create', 'edit', 'show']);
            Route::resource('sections', SectionController::class)->except(['create', 'edit', 'show']);
            Route::resource('subjects', SubjectController::class)->except(['create', 'edit', 'show']);
            Route::resource('shifts',   ShiftController::class)->except(['create', 'edit', 'show']);
            Route::resource('holidays', HolidayController::class)->except(['create', 'edit', 'show']);
            Route::resource('students', StudentController::class);
            Route::post('students/bulk-import',                 [StudentController::class, 'bulkImport'])->name('students.bulk-import');
            Route::post('students/{student}/documents',        [StudentController::class, 'uploadDocument'])->name('students.documents.upload');
            Route::delete('students/documents/{document}',     [StudentController::class, 'deleteDocument'])->name('students.documents.delete');

            // Exams
            Route::get('exams',                              [ExamController::class, 'index'])->name('exams.index');
            Route::post('exams',                             [ExamController::class, 'store'])->name('exams.store');
            Route::put('exams/{exam}',                       [ExamController::class, 'update'])->name('exams.update');
            Route::delete('exams/{exam}',                    [ExamController::class, 'destroy'])->name('exams.destroy');
            Route::get('exams/{exam}/marks',                 [ExamController::class, 'marks'])->name('exams.marks');
            Route::post('exams/{exam}/marks',                [ExamController::class, 'saveMarks'])->name('exams.marks.save');
            Route::post('exams/{exam}/marks/import',         [ExamController::class, 'bulkImportMarks'])->name('exams.marks.import');
            Route::get('exams/{exam}/results',               [ExamController::class, 'results'])->name('exams.results');
            Route::get('grade-scales',                       [ExamController::class, 'gradeScales'])->name('grade-scales.index');
            Route::post('grade-scales',                      [ExamController::class, 'saveGradeScale'])->name('grade-scales.store');
            Route::put('grade-scales/{gradeScale}',          [ExamController::class, 'updateGradeScale'])->name('grade-scales.update');
            Route::delete('grade-scales/{gradeScale}',       [ExamController::class, 'deleteGradeScale'])->name('grade-scales.destroy');

            // Timetable
            Route::get('timetable',                      [TimetableController::class, 'index'])->name('timetable.index');
            Route::post('timetable',                     [TimetableController::class, 'store'])->name('timetable.store');
            Route::delete('timetable/{timetable}',       [TimetableController::class, 'destroy'])->name('timetable.destroy');
            Route::get('timetable/teacher',              [TimetableController::class, 'teacherSchedule'])->name('timetable.teacher');

            // Attendance — student
            Route::get('attendance',                          [AttendanceController::class, 'index'])->name('attendance.index');
            Route::post('attendance',                         [AttendanceController::class, 'store'])->name('attendance.store');
            Route::get('attendance/students/{student}/calendar', [AttendanceController::class, 'studentCalendar'])->name('attendance.student.calendar');
            // Attendance — staff
            Route::get('attendance/staff',                    [AttendanceController::class, 'staffIndex'])->name('attendance.staff.index');
            Route::post('attendance/staff',                   [AttendanceController::class, 'staffStore'])->name('attendance.staff.store');

            // Attendance Approval
            Route::post('attendance/approve/{date}/{classId}', [AttendanceApprovalController::class, 'approve'])->name('attendance.approve');
            Route::post('attendance/bulk-approve', [AttendanceApprovalController::class, 'bulkApprove'])->name('attendance.bulk-approve');

            // HR — Leave Management
            Route::get('hr/leave-types',                       [LeaveController::class, 'types'])->name('hr.leave-types.index');
            Route::post('hr/leave-types',                      [LeaveController::class, 'storeType'])->name('hr.leave-types.store');
            Route::put('hr/leave-types/{leaveType}',           [LeaveController::class, 'updateType'])->name('hr.leave-types.update');
            Route::delete('hr/leave-types/{leaveType}',        [LeaveController::class, 'destroyType'])->name('hr.leave-types.destroy');
            Route::get('hr/leaves',                            [LeaveController::class, 'index'])->name('hr.leaves.index');
            Route::post('hr/leaves',                           [LeaveController::class, 'store'])->name('hr.leaves.store');
            Route::put('hr/leaves/{leaveRequest}/approve',     [LeaveController::class, 'approve'])->middleware('permission:edit-staff')->name('hr.leaves.approve');

            // HR — Payroll
            Route::get('hr/salary-structure',                  [PayrollController::class, 'structure'])->name('hr.salary-structure.index');
            Route::put('hr/salary-structure/{staff}',          [PayrollController::class, 'saveStructure'])->name('hr.salary-structure.save');
            Route::get('hr/payroll',                           [PayrollController::class, 'index'])->name('hr.payroll.index');
            Route::middleware('permission:manage-payroll')->group(function () {
                Route::post('hr/payroll/generate',                 [PayrollController::class, 'generate'])->name('hr.payroll.generate');
                Route::put('hr/payroll/{payroll}/paid',            [PayrollController::class, 'markPaid'])->name('hr.payroll.paid');
            });
            Route::get('hr/payroll/{payroll}/slip',            [PayrollController::class, 'slip'])->name('hr.payroll.slip');

            // Library Management
            Route::get('library/books',                        [LibraryController::class, 'index'])->name('library.books.index');
            Route::post('library/books',                       [LibraryController::class, 'store'])->name('library.books.store');
            Route::put('library/books/{book}',                 [LibraryController::class, 'update'])->name('library.books.update');
            Route::delete('library/books/{book}',              [LibraryController::class, 'destroy'])->name('library.books.destroy');
            Route::get('library/issues',                       [LibraryController::class, 'issues'])->name('library.issues.index');
            Route::post('library/issues',                      [LibraryController::class, 'issueBook'])->name('library.issues.store');
            Route::put('library/issues/{bookIssue}/return',    [LibraryController::class, 'returnBook'])->name('library.issues.return');
            Route::get('library/overdue',                      [LibraryController::class, 'overdue'])->name('library.overdue');

            // Inventory Management
            Route::get('inventory/categories',                         [InventoryController::class, 'categories'])->name('inventory.categories');
            Route::post('inventory/categories',                        [InventoryController::class, 'storeCategory'])->name('inventory.categories.store');
            Route::put('inventory/categories/{inventoryCategory}',     [InventoryController::class, 'updateCategory'])->name('inventory.categories.update');
            Route::delete('inventory/categories/{inventoryCategory}',  [InventoryController::class, 'destroyCategory'])->name('inventory.categories.destroy');

            Route::get('inventory/items',                              [InventoryController::class, 'items'])->name('inventory.items');
            Route::post('inventory/items',                             [InventoryController::class, 'storeItem'])->name('inventory.items.store');
            Route::put('inventory/items/{inventoryItem}',              [InventoryController::class, 'updateItem'])->name('inventory.items.update');
            Route::delete('inventory/items/{inventoryItem}',           [InventoryController::class, 'destroyItem'])->name('inventory.items.destroy');

            Route::get('inventory/purchases',                          [InventoryController::class, 'purchases'])->name('inventory.purchases');
            Route::post('inventory/purchases',                         [InventoryController::class, 'storePurchase'])->name('inventory.purchases.store');

            Route::get('inventory/issues',                             [InventoryController::class, 'issues'])->name('inventory.issues');
            Route::post('inventory/issues',                            [InventoryController::class, 'storeIssue'])->name('inventory.issues.store');
            Route::put('inventory/issues/{inventoryIssue}/return',     [InventoryController::class, 'returnIssue'])->name('inventory.issues.return');

            // Asset Management
            Route::get('inventory/assets',                             [AssetController::class, 'index'])->name('inventory.assets');
            Route::post('inventory/assets',                            [AssetController::class, 'store'])->name('inventory.assets.store');
            Route::get('inventory/assets/{asset}',                     [AssetController::class, 'show'])->name('inventory.assets.show');
            Route::put('inventory/assets/{asset}',                     [AssetController::class, 'update'])->name('inventory.assets.update');
            Route::delete('inventory/assets/{asset}',                  [AssetController::class, 'destroy'])->name('inventory.assets.destroy');
            Route::post('inventory/assets/{asset}/maintenance',        [AssetController::class, 'storeMaintenance'])->name('inventory.assets.maintenance');

            // Hostel Management
            Route::get('hostel',                                          [HostelController::class, 'index'])->name('hostel.index');
            Route::post('hostel',                                         [HostelController::class, 'store'])->name('hostel.store');
            Route::put('hostel/{hostel}',                                 [HostelController::class, 'update'])->name('hostel.update');
            Route::delete('hostel/{hostel}',                              [HostelController::class, 'destroy'])->name('hostel.destroy');

            Route::get('hostel/{hostel}/rooms',                           [HostelController::class, 'rooms'])->name('hostel.rooms');
            Route::post('hostel/{hostel}/rooms',                          [HostelController::class, 'storeRoom'])->name('hostel.rooms.store');
            Route::put('hostel/{hostel}/rooms/{room}',                    [HostelController::class, 'updateRoom'])->name('hostel.rooms.update');
            Route::delete('hostel/{hostel}/rooms/{room}',                 [HostelController::class, 'destroyRoom'])->name('hostel.rooms.destroy');
            Route::get('hostel/{hostel}/available-rooms',                 [HostelController::class, 'hostelRooms'])->name('hostel.available-rooms');

            Route::get('hostel/allocations',                              [HostelController::class, 'allocations'])->name('hostel.allocations');
            Route::post('hostel/allocations',                             [HostelController::class, 'storeAllocation'])->name('hostel.allocations.store');
            Route::put('hostel/allocations/{allocation}/vacate',          [HostelController::class, 'vacate'])->name('hostel.vacate');

            // Transport Management
            Route::get('transport/vehicles',                          [TransportController::class, 'vehicles'])->name('transport.vehicles');
            Route::post('transport/vehicles',                         [TransportController::class, 'storeVehicle'])->name('transport.vehicles.store');
            Route::put('transport/vehicles/{vehicle}',                [TransportController::class, 'updateVehicle'])->name('transport.vehicles.update');
            Route::delete('transport/vehicles/{vehicle}',             [TransportController::class, 'destroyVehicle'])->name('transport.vehicles.destroy');

            Route::get('transport/routes',                            [TransportController::class, 'routes'])->name('transport.routes');
            Route::post('transport/routes',                           [TransportController::class, 'storeRoute'])->name('transport.routes.store');
            Route::put('transport/routes/{route}',                    [TransportController::class, 'updateRoute'])->name('transport.routes.update');
            Route::delete('transport/routes/{route}',                 [TransportController::class, 'destroyRoute'])->name('transport.routes.destroy');

            Route::get('transport/routes/{route}/assignments',        [TransportController::class, 'assignments'])->name('transport.assignments');
            Route::post('transport/routes/{route}/assign',            [TransportController::class, 'assignStudent'])->name('transport.assign');
            Route::delete('transport/routes/{route}/students/{student}', [TransportController::class, 'removeStudent'])->name('transport.unassign');

            // Homework & Lesson Planning
            Route::get('homework',                                    [HomeworkController::class, 'index'])->name('homework.index');
            Route::post('homework',                                   [HomeworkController::class, 'store'])->name('homework.store');
            Route::put('homework/{homework}',                         [HomeworkController::class, 'update'])->name('homework.update');
            Route::delete('homework/{homework}',                      [HomeworkController::class, 'destroy'])->name('homework.destroy');
            Route::get('homework/{homework}/submissions',             [HomeworkController::class, 'submissions'])->name('homework.submissions');
            Route::put('homework/submissions/{submission}/review',    [HomeworkController::class, 'reviewSubmission'])->name('homework.submissions.review');

            Route::get('homework/lesson-plans',                       [HomeworkController::class, 'lessonPlans'])->name('homework.lesson-plans.index');
            Route::post('homework/lesson-plans',                      [HomeworkController::class, 'storeLessonPlan'])->name('homework.lesson-plans.store');
            Route::put('homework/lesson-plans/{lessonPlan}/review',   [HomeworkController::class, 'reviewLessonPlan'])->name('homework.lesson-plans.review');
            Route::delete('homework/lesson-plans/{lessonPlan}',       [HomeworkController::class, 'destroyLessonPlan'])->name('homework.lesson-plans.destroy');

            Route::get('homework/syllabi',                            [HomeworkController::class, 'syllabi'])->name('homework.syllabi.index');
            Route::post('homework/syllabi',                           [HomeworkController::class, 'storeSyllabus'])->name('homework.syllabi.store');
            Route::put('homework/syllabi/{syllabus}',                 [HomeworkController::class, 'updateSyllabus'])->name('homework.syllabi.update');

            Route::get('homework/online-classes',                     [HomeworkController::class, 'onlineClasses'])->name('homework.online-classes.index');
            Route::post('homework/online-classes',                    [HomeworkController::class, 'storeOnlineClass'])->name('homework.online-classes.store');
            Route::put('homework/online-classes/{onlineClass}/status',[HomeworkController::class, 'updateOnlineClassStatus'])->name('homework.online-classes.status');
            Route::delete('homework/online-classes/{onlineClass}',    [HomeworkController::class, 'destroyOnlineClass'])->name('homework.online-classes.destroy');

            // Fee Management
            Route::get('fees/categories',                    [FeeCategoryController::class, 'index'])->name('fees.categories.index');
            Route::get('fees/structures',                    [FeeStructureController::class, 'index'])->name('fees.structures.index');
            Route::get('fees/payments',                      [FeePaymentController::class, 'index'])->name('fees.payments.index');
            Route::get('fees/payments/collect',              [FeePaymentController::class, 'create'])->name('fees.payments.create');
            Route::get('fees/payments/{feePayment}',         [FeePaymentController::class, 'show'])->name('fees.payments.show');
            Route::get('fees/outstanding',                   [FeePaymentController::class, 'outstanding'])->name('fees.outstanding');

            Route::middleware('permission:create-fees|edit-fees|delete-fees')->group(function () {
                Route::post('fees/categories',                   [FeeCategoryController::class, 'store'])->name('fees.categories.store');
                Route::put('fees/categories/{feeCategory}',      [FeeCategoryController::class, 'update'])->name('fees.categories.update');
                Route::delete('fees/categories/{feeCategory}',   [FeeCategoryController::class, 'destroy'])->name('fees.categories.destroy');
                Route::post('fees/structures',                   [FeeStructureController::class, 'store'])->name('fees.structures.store');
                Route::put('fees/structures/{feeStructure}',     [FeeStructureController::class, 'update'])->name('fees.structures.update');
                Route::delete('fees/structures/{feeStructure}',  [FeeStructureController::class, 'destroy'])->name('fees.structures.destroy');
            });

            Route::middleware('permission:collect-fees')->group(function () {
                Route::post('fees/payments',                     [FeePaymentController::class, 'store'])->name('fees.payments.store');
            });

            // Communication
            Route::get('communication/announcements',                              [CommunicationController::class, 'announcements'])->name('communication.announcements');
            Route::post('communication/announcements',                             [CommunicationController::class, 'storeAnnouncement'])->name('communication.announcements.store');
            Route::put('communication/announcements/{announcement}',               [CommunicationController::class, 'updateAnnouncement'])->name('communication.announcements.update');
            Route::delete('communication/announcements/{announcement}',            [CommunicationController::class, 'destroyAnnouncement'])->name('communication.announcements.destroy');

            Route::get('communication/messages',                                   [CommunicationController::class, 'messages'])->name('communication.messages');
            Route::post('communication/messages',                                  [CommunicationController::class, 'sendMessage'])->name('communication.messages.send');
            Route::put('communication/messages/{message}/read',                    [CommunicationController::class, 'readMessage'])->name('communication.messages.read');

            Route::get('communication/blast',                                      [CommunicationController::class, 'blast'])->name('communication.blast');
            Route::post('communication/blast',                                     [CommunicationController::class, 'sendBlast'])->name('communication.blast.send');

            Route::get('communication/email-templates',                            [CommunicationController::class, 'emailTemplates'])->name('communication.email-templates');
            Route::post('communication/email-templates',                           [CommunicationController::class, 'storeEmailTemplate'])->name('communication.email-templates.store');
            Route::put('communication/email-templates/{emailTemplate}',            [CommunicationController::class, 'updateEmailTemplate'])->name('communication.email-templates.update');

            Route::get('communication/notifications',                              [CommunicationController::class, 'notifications'])->name('communication.notifications');
            Route::put('communication/notifications/{notification}/read',          [CommunicationController::class, 'markNotificationRead'])->name('communication.notifications.read');
            Route::put('communication/notifications/read-all',                     [CommunicationController::class, 'markAllNotificationsRead'])->name('communication.notifications.read-all');

            // Reports & Analytics
            Route::get('reports/dashboard',                     [ReportController::class, 'dashboard'])->name('reports.dashboard');
            Route::get('reports/attendance',                    [ReportController::class, 'attendance'])->name('reports.attendance');
            Route::get('reports/academic',                      [ReportController::class, 'academic'])->name('reports.academic');
            Route::get('reports/finance',                       [ReportController::class, 'finance'])->name('reports.finance');
            Route::get('reports/custom',                        [ReportController::class, 'customBuilder'])->name('reports.custom');
            Route::post('reports/custom/run',                   [ReportController::class, 'runCustomReport'])->name('reports.custom.run');
            Route::get('reports/custom/export-csv',             [ReportController::class, 'exportCsv'])->name('reports.custom.csv');
            Route::get('reports/attendance/export-pdf',         [ReportController::class, 'exportAttendancePdf'])->name('reports.attendance.pdf');
            Route::get('reports/finance/export-pdf',            [ReportController::class, 'exportFinancePdf'])->name('reports.finance.pdf');
            Route::get('reports/audit-log',                     [ReportController::class, 'auditLog'])->name('reports.audit-log');

            // General / Branding / Academic / Notification Settings
            Route::get('settings',                              [SchoolSettingsController::class, 'index'])->name('settings.index');
            Route::middleware('permission:manage-settings')->group(function () {
                Route::post('settings/general',                     [SchoolSettingsController::class, 'saveGeneral'])->name('settings.general');
                Route::post('settings/branding',                    [SchoolSettingsController::class, 'saveBranding'])->name('settings.branding');
                Route::post('settings/academic',                    [SchoolSettingsController::class, 'saveAcademic'])->name('settings.academic');
                Route::post('settings/notifications',               [SchoolSettingsController::class, 'saveNotifications'])->name('settings.notifications');
            });

            // Integrations / Gateway Settings
            Route::get('settings/integrations',                 [IntegrationController::class, 'index'])->name('settings.integrations');
            Route::middleware('permission:manage-settings')->group(function () {
                Route::post('settings/integrations/smtp',           [IntegrationController::class, 'saveSmtp'])->name('settings.integrations.smtp');
                Route::post('settings/integrations/smtp/test',      [IntegrationController::class, 'testSmtp'])->name('settings.integrations.smtp.test');
                Route::post('settings/integrations/sms',            [IntegrationController::class, 'saveSms'])->name('settings.integrations.sms');
                Route::post('settings/integrations/sms/test',       [IntegrationController::class, 'testSms'])->name('settings.integrations.sms.test');
            });

            // School User / Admin Management
            Route::get('settings/admins',                       [SchoolUserController::class, 'index'])->name('settings.admins');
            Route::middleware('permission:manage-users')->group(function () {
                Route::post('settings/admins',                      [SchoolUserController::class, 'store'])->name('settings.admins.store');
                Route::put('settings/admins/{user}',                [SchoolUserController::class, 'update'])->name('settings.admins.update');
                Route::delete('settings/admins/{user}',             [SchoolUserController::class, 'destroy'])->name('settings.admins.destroy');
                Route::patch('settings/admins/{user}/suspend',      [SchoolUserController::class, 'suspend'])->name('settings.admins.suspend');
                Route::patch('settings/admins/{user}/activate',     [SchoolUserController::class, 'activate'])->name('settings.admins.activate');
                Route::post('settings/admins/{user}/reset-password', [SchoolUserController::class, 'resetPassword'])->name('settings.admins.reset-password');
                Route::post('settings/admins/bulk/activate',         [SchoolUserController::class, 'bulkActivate'])->name('settings.admins.bulk-activate');
                Route::post('settings/admins/bulk/suspend',          [SchoolUserController::class, 'bulkSuspend'])->name('settings.admins.bulk-suspend');
                Route::post('settings/admins/bulk/delete',           [SchoolUserController::class, 'bulkDelete'])->name('settings.admins.bulk-delete');
                Route::post('settings/admins/bulk/import',           [SchoolUserController::class, 'bulkImport'])->name('settings.admins.bulk-import');
                Route::get('settings/admins/export',                 [SchoolUserController::class, 'exportCsv'])->name('settings.admins.export');
            });

            // Admission Inquiries
            Route::get('admissions/inquiries',                          [AdmissionInquiryController::class, 'index'])->name('admissions.inquiries');
            Route::post('admissions/inquiries',                         [AdmissionInquiryController::class, 'store'])->name('admissions.inquiries.store');
            Route::put('admissions/inquiries/{admissionInquiry}',       [AdmissionInquiryController::class, 'update'])->name('admissions.inquiries.update');
            Route::delete('admissions/inquiries/{admissionInquiry}',    [AdmissionInquiryController::class, 'destroy'])->name('admissions.inquiries.destroy');
            Route::post('admissions/inquiries/{admissionInquiry}/followup', [AdmissionInquiryController::class, 'addFollowup'])->name('admissions.inquiries.followup');

            // Visitor Logs
            Route::get('admissions/visitors',                [VisitorLogController::class, 'index'])->name('admissions.visitors');
            Route::post('admissions/visitors',               [VisitorLogController::class, 'store'])->name('admissions.visitors.store');
            Route::patch('admissions/visitors/{visitorLog}/checkout', [VisitorLogController::class, 'checkout'])->name('admissions.visitors.checkout');
            Route::delete('admissions/visitors/{visitorLog}', [VisitorLogController::class, 'destroy'])->name('admissions.visitors.destroy');

            Route::resource('departments',  DepartmentController::class)->except(['create', 'edit', 'show']);
            Route::resource('designations', DesignationController::class)->except(['create', 'edit', 'show']);
            Route::resource('staff',        StaffController::class);
            Route::post('staff/{staff}/documents',         [StaffController::class, 'uploadDocument'])->name('staff.documents.upload');
            Route::delete('staff/documents/{document}',    [StaffController::class, 'deleteDocument'])->name('staff.documents.delete');

            // ── Sierra Leone: Academic Terms ──
            Route::get('academic-terms',                   [AcademicTermController::class, 'index'])->name('academic-terms.index');
            Route::post('academic-terms',                  [AcademicTermController::class, 'store'])->name('academic-terms.store');
            Route::put('academic-terms/{academicTerm}',    [AcademicTermController::class, 'update'])->name('academic-terms.update');
            Route::delete('academic-terms/{academicTerm}', [AcademicTermController::class, 'destroy'])->name('academic-terms.destroy');

            // ── Sierra Leone: Report Cards ──
            Route::get('report-cards',                             [ReportCardController::class, 'index'])->name('report-cards.index');
            Route::middleware('permission:publish-report-cards')->group(function () {
                Route::post('report-cards/generate',                   [ReportCardController::class, 'generate'])->name('report-cards.generate');
                Route::patch('report-cards/{reportCard}/approve',      [ReportCardController::class, 'approve'])->name('report-cards.approve');
                Route::patch('report-cards/{reportCard}/publish',      [ReportCardController::class, 'publish'])->name('report-cards.publish');
                Route::post('report-cards/bulk-publish',               [ReportCardController::class, 'bulkPublish'])->name('report-cards.bulk-publish');
                Route::post('report-cards/promote-students',           [ReportCardController::class, 'promoteStudents'])->name('report-cards.promote-students');
            });
            Route::get('report-cards/{reportCard}',                [ReportCardController::class, 'show'])->name('report-cards.show');
            Route::get('report-cards/{reportCard}/print',         [ReportCardController::class, 'printPdf'])->name('report-cards.print');
            Route::put('report-cards/{reportCard}/comments',       [ReportCardController::class, 'updateComments'])->name('report-cards.comments');

            // ── Sierra Leone: National Examinations ──
            Route::get('national-exams',                                    [NationalExaminationController::class, 'index'])->name('national-exams.index');
            Route::middleware('permission:manage-national-exams')->group(function () {
                Route::post('national-exams',                                   [NationalExaminationController::class, 'store'])->name('national-exams.store');
                Route::put('national-exams/{nationalExam}',                     [NationalExaminationController::class, 'update'])->name('national-exams.update');
                Route::delete('national-exams/{nationalExam}',                  [NationalExaminationController::class, 'destroy'])->name('national-exams.destroy');
                Route::post('national-exams/bulk-register',                     [NationalExaminationController::class, 'bulkRegister'])->name('national-exams.bulk-register');
                Route::post('national-exams/import-results',                    [NationalExaminationController::class, 'importResults'])->name('national-exams.import-results');
                Route::post('national-exams/publish-results',                   [NationalExaminationController::class, 'publishResults'])->name('national-exams.publish-results');
            });

            // ── Sierra Leone: Settings ──
            Route::get('settings/sierra-leone',                             [SierraLeoneSettingsController::class, 'index'])->name('settings.sierra-leone');
            Route::post('settings/sierra-leone/education',                  [SierraLeoneSettingsController::class, 'saveEducationSystem'])->name('settings.sierra-leone.education');
            Route::post('settings/sierra-leone/levels',                     [SierraLeoneSettingsController::class, 'saveSchoolLevels'])->name('settings.sierra-leone.levels');

            // School Identity & Branding
            Route::get('school-identity',                              [SchoolIdentityController::class, 'index'])->name('school-identity.index');
            Route::post('school-identity/basic',                       [SchoolIdentityController::class, 'saveBasic'])->name('school-identity.basic');
            Route::post('school-identity/registration',                [SchoolIdentityController::class, 'saveRegistration'])->name('school-identity.registration');
            Route::post('school-identity/contact',                     [SchoolIdentityController::class, 'saveContact'])->name('school-identity.contact');
            Route::post('school-identity/leadership',                  [SchoolIdentityController::class, 'saveLeadership'])->name('school-identity.leadership');
            Route::post('school-identity/academic',                    [SchoolIdentityController::class, 'saveAcademic'])->name('school-identity.academic');
            Route::post('school-identity/branding',                    [SchoolIdentityController::class, 'saveBranding'])->name('school-identity.branding');
            Route::post('school-identity/public-profile',              [SchoolIdentityController::class, 'savePublicProfile'])->name('school-identity.public-profile');
            Route::delete('school-identity/asset',                     [SchoolIdentityController::class, 'removeAsset'])->name('school-identity.asset.remove');
            Route::get('school-identity/branding-data',                [SchoolIdentityController::class, 'branding'])->name('school-identity.branding-data');

            // Performance Intelligence (shared by all school roles)
            Route::get('performance/overview',            [PerformanceController::class, 'overview'])->name('performance.overview');
            Route::get('performance/recalculate',         [PerformanceController::class, 'recalculate'])->name('performance.recalculate');
            Route::get('performance/student/{studentId}', [PerformanceController::class, 'studentProfile'])->name('performance.student');
            Route::get('performance/top-students',        [PerformanceController::class, 'topStudents'])->name('performance.top-students');
            Route::get('performance/at-risk',             [PerformanceController::class, 'atRiskStudents'])->name('performance.at-risk');
            Route::get('performance/most-improved',       [PerformanceController::class, 'mostImproved'])->name('performance.most-improved');
            Route::get('performance/subjects',            [PerformanceController::class, 'subjectPerformance'])->name('performance.subjects');
            Route::get('performance/departments',         [PerformanceController::class, 'departmentPerformance'])->name('performance.departments');
            Route::get('performance/alerts',              [PerformanceController::class, 'alerts'])->name('performance.alerts');
            Route::post('performance/alerts/{id}/read',   [PerformanceController::class, 'markAlertRead'])->name('performance.alerts.read');
            Route::get('performance/interventions',       [PerformanceController::class, 'interventions'])->name('performance.interventions');
            Route::post('performance/interventions',      [PerformanceController::class, 'storeIntervention'])->name('performance.interventions.store');
            Route::get('performance/interventions/{id}',  [PerformanceController::class, 'showIntervention'])->name('performance.interventions.show');
            Route::put('performance/interventions/{id}',  [PerformanceController::class, 'updateIntervention'])->name('performance.interventions.update');
            Route::post('performance/interventions/{id}/notes', [PerformanceController::class, 'addInterventionNote'])->name('performance.interventions.notes');
            Route::get('performance/behaviours',          [PerformanceController::class, 'behaviours'])->name('performance.behaviours');
            Route::post('performance/behaviours',         [PerformanceController::class, 'storeBehaviour'])->name('performance.behaviours.store');
            Route::get('performance/goals',               [PerformanceController::class, 'goals'])->name('performance.goals');
            Route::post('performance/goals',              [PerformanceController::class, 'storeGoal'])->name('performance.goals.store');
            Route::get('performance/achievements',        [PerformanceController::class, 'achievements'])->name('performance.achievements');

            // Result Change Requests
            Route::get('result-change-requests', [ResultChangeRequestController::class, 'index'])->name('result-change-requests.index');
            Route::post('result-change-requests', [ResultChangeRequestController::class, 'store'])->name('result-change-requests.store');
            Route::middleware('permission:approve-result-changes')->group(function () {
                Route::post('result-change-requests/{resultChangeRequest}/approve', [ResultChangeRequestController::class, 'approve'])->name('result-change-requests.approve');
                Route::post('result-change-requests/{resultChangeRequest}/reject', [ResultChangeRequestController::class, 'reject'])->name('result-change-requests.reject');
            });
        });

    /*
    |--------------------------------------------------------------------------
    | Student & Parent portal routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:student')->prefix('school/student')->name('student.')->group(function () {
        Route::get('dashboard',           [StudentPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('timetable',           [StudentPortalController::class, 'timetable'])->name('timetable');
        Route::get('attendance',          [StudentPortalController::class, 'attendance'])->name('attendance');
        Route::get('results',             [StudentPortalController::class, 'results'])->name('results');
        Route::get('homework',            [StudentPortalController::class, 'homework'])->name('homework');
        Route::get('fees',                [StudentPortalController::class, 'fees'])->name('fees');
        Route::get('announcements',       [StudentPortalController::class, 'announcements'])->name('announcements');
        Route::get('report-cards',        [StudentPortalController::class, 'reportCards'])->name('report-cards');
        Route::get('profile',             [StudentPortalController::class, 'profile'])->name('profile');
        Route::get('downloads',           [StudentPortalController::class, 'downloads'])->name('downloads');
        Route::get('examination-centre',  [StudentPortalController::class, 'examinationCentre'])->name('examination-centre');
        Route::get('academic-progress',   [StudentPortalController::class, 'academicProgress'])->name('academic-progress');
        Route::get('school-info',         [StudentPortalController::class, 'schoolInfo'])->name('school-info');
        // Performance Intelligence
        Route::get('performance',         [StudentPerformanceController::class, 'dashboard'])->name('performance');
        Route::get('goals',               [StudentPerformanceController::class, 'goals'])->name('goals');
        Route::get('achievements',        [StudentPerformanceController::class, 'achievements'])->name('achievements');
    });

    Route::middleware('role:parent')->prefix('school/parent')->name('parent.')->group(function () {
        Route::get('dashboard',       [ParentPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('attendance',      [ParentPortalController::class, 'attendance'])->name('attendance');
        Route::get('results',         [ParentPortalController::class, 'results'])->name('results');
        Route::get('fees',            [ParentPortalController::class, 'fees'])->name('fees');
        Route::get('announcements',   [ParentPortalController::class, 'announcements'])->name('announcements');
        Route::get('report-cards',    [ParentPortalController::class, 'reportCards'])->name('report-cards');
        Route::get('timetable',       [ParentPortalController::class, 'timetable'])->name('timetable');
        Route::get('profile',         [ParentPortalController::class, 'profile'])->name('profile');
        Route::get('school-info',     [ParentPortalController::class, 'schoolInfo'])->name('school-info');
        Route::get('downloads',       [ParentPortalController::class, 'downloads'])->name('downloads');
        Route::get('communication',   [ParentPortalController::class, 'communication'])->name('communication');
        // Performance Intelligence
        Route::get('performance',     [ParentPerformanceController::class, 'dashboard'])->name('performance');
        Route::get('child-performance/{childId}', [ParentPerformanceController::class, 'childDetail'])->name('child-performance');
    });

    Route::middleware('role:teacher')->prefix('school/teacher')->name('teacher.')->group(function () {
        Route::get('dashboard',            [TeacherPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('academic',             [TeacherPortalController::class, 'academic'])->name('academic');
        Route::get('students',             [TeacherPortalController::class, 'students'])->name('students');
        Route::get('students/{id}',        [TeacherPortalController::class, 'studentProfile'])->name('students.profile');
        Route::get('timetable',            [TeacherPortalController::class, 'timetable'])->name('timetable');
        Route::get('attendance',           [TeacherPortalController::class, 'attendance'])->name('attendance');
        Route::get('attendance/take',      [TeacherPortalController::class, 'attendanceTake'])->name('attendance.take');
        Route::post('attendance/store',    [TeacherPortalController::class, 'attendanceStore'])->name('attendance.store');
        Route::get('exams',                [TeacherPortalController::class, 'exams'])->name('exams');
        Route::get('exams/{id}/grades',    [TeacherPortalController::class, 'gradeEntry'])->name('exams.grades');
        Route::post('exams/{id}/grades',   [TeacherPortalController::class, 'gradeStore'])->name('exams.grades.store');
        Route::get('homework',             [TeacherPortalController::class, 'homework'])->name('homework');
        Route::get('homework/create',      [TeacherPortalController::class, 'homeworkCreate'])->name('homework.create');
        Route::post('homework/store',      [TeacherPortalController::class, 'homeworkStore'])->name('homework.store');
        Route::delete('homework/{id}',     [TeacherPortalController::class, 'homeworkDestroy'])->name('homework.destroy');
        Route::get('lesson-plans',         [TeacherPortalController::class, 'lessonPlans'])->name('lesson-plans');
        Route::post('lesson-plans/store',  [TeacherPortalController::class, 'lessonPlanStore'])->name('lesson-plans.store');
        Route::get('syllabus',             [TeacherPortalController::class, 'syllabus'])->name('syllabus');
        Route::get('online-classes',       [TeacherPortalController::class, 'onlineClasses'])->name('online-classes');
        Route::get('announcements',        [TeacherPortalController::class, 'announcements'])->name('announcements');
        Route::get('messages',             [TeacherPortalController::class, 'messages'])->name('messages');
        Route::post('messages/send',       [TeacherPortalController::class, 'messageSend'])->name('messages.send');
        Route::get('notifications',        [TeacherPortalController::class, 'notifications'])->name('notifications');
        Route::get('reports',              [TeacherPortalController::class, 'reports'])->name('reports');
        Route::get('downloads',            [TeacherPortalController::class, 'downloads'])->name('downloads');
        Route::get('profile',              [TeacherPortalController::class, 'profile'])->name('profile');
        // Performance Intelligence
        Route::get('performance',          [TeacherPerformanceController::class, 'dashboard'])->name('performance');
        Route::get('performance/student/{studentId}', [TeacherPerformanceController::class, 'studentDetail'])->name('performance.student');
        Route::get('performance/class',    [TeacherPerformanceController::class, 'classPerformance'])->name('performance.class');
    });

    /*
    |--------------------------------------------------------------------------
    | Accountant Portal routes
    |--------------------------------------------------------------------------*/
    Route::middleware('role:accountant')->prefix('school/accountant')->name('accountant.')->group(function () {
        Route::get('dashboard',           [AccountantPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('fees',                [AccountantPortalController::class, 'fees'])->name('fees');
        Route::get('fee-structure',       [AccountantPortalController::class, 'feeStructure'])->name('fee-structure');
        Route::get('fee-collections',     [AccountantPortalController::class, 'feeCollections'])->name('fee-collections');
        Route::get('outstanding',         [AccountantPortalController::class, 'outstanding'])->name('outstanding');
        Route::get('payroll',             [AccountantPortalController::class, 'payroll'])->name('payroll');
        Route::get('expenses',            [AccountantPortalController::class, 'expenses'])->name('expenses');
        Route::get('reports',             [AccountantPortalController::class, 'reports'])->name('reports');
        Route::get('announcements',       [AccountantPortalController::class, 'announcements'])->name('announcements');
        Route::get('messages',            [AccountantPortalController::class, 'messages'])->name('messages');
        Route::post('messages/send',      [AccountantPortalController::class, 'messageSend'])->name('messages.send');
        Route::get('notifications',       [AccountantPortalController::class, 'notifications'])->name('notifications');
        Route::get('downloads',           [AccountantPortalController::class, 'downloads'])->name('downloads');
        Route::get('profile',             [AccountantPortalController::class, 'profile'])->name('profile');
        // Performance Intelligence
        Route::get('performance',         [PerformanceController::class, 'overview'])->name('performance');
        Route::get('performance/recalculate', [PerformanceController::class, 'recalculate'])->name('performance.recalculate');
    });

    /*
    |--------------------------------------------------------------------------
    | Principal Portal routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:principal')->prefix('school/principal')->name('principal.')->group(function () {
        Route::get('dashboard',           [PrincipalPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('academic',            [PrincipalPortalController::class, 'academic'])->name('academic');
        Route::get('students',            [PrincipalPortalController::class, 'students'])->name('students');
        Route::get('students/{id}',       [PrincipalPortalController::class, 'studentProfile'])->name('students.profile');
        Route::get('staff',               [PrincipalPortalController::class, 'staff'])->name('staff');
        Route::get('attendance',          [PrincipalPortalController::class, 'attendance'])->name('attendance');
        Route::get('exams',               [PrincipalPortalController::class, 'exams'])->name('exams');
        Route::get('results',             [PrincipalPortalController::class, 'results'])->name('results');
        Route::get('timetable',           [PrincipalPortalController::class, 'timetable'])->name('timetable');
        Route::get('homework',            [PrincipalPortalController::class, 'homework'])->name('homework');
        Route::get('fees',                [PrincipalPortalController::class, 'fees'])->name('fees');
        Route::get('announcements',       [PrincipalPortalController::class, 'announcements'])->name('announcements');
        Route::get('messages',            [PrincipalPortalController::class, 'messages'])->name('messages');
        Route::post('messages/send',      [PrincipalPortalController::class, 'messageSend'])->name('messages.send');
        Route::get('reports',             [PrincipalPortalController::class, 'reports'])->name('reports');
        Route::get('downloads',           [PrincipalPortalController::class, 'downloads'])->name('downloads');
        Route::get('profile',             [PrincipalPortalController::class, 'profile'])->name('profile');
        // Performance Intelligence
        Route::get('performance',         [PrincipalPerformanceController::class, 'dashboard'])->name('performance');
        Route::get('performance/student/{studentId}', [PrincipalPerformanceController::class, 'studentDetail'])->name('performance.student');
    });

    /*
    |--------------------------------------------------------------------------
    | Proprietor Portal routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:proprietor')->prefix('school/proprietor')->name('proprietor.')->group(function () {
        Route::get('dashboard',           [ProprietorPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('financial',           [ProprietorPortalController::class, 'financial'])->name('financial');
        Route::get('academic',            [ProprietorPortalController::class, 'academic'])->name('academic');
        Route::get('staff',               [ProprietorPortalController::class, 'staff'])->name('staff');
        Route::get('students',            [ProprietorPortalController::class, 'students'])->name('students');
        Route::get('reports',             [ProprietorPortalController::class, 'reports'])->name('reports');
        Route::get('announcements',       [ProprietorPortalController::class, 'announcements'])->name('announcements');
        Route::get('messages',            [ProprietorPortalController::class, 'messages'])->name('messages');
        Route::post('messages/send',      [ProprietorPortalController::class, 'messageSend'])->name('messages.send');
        Route::get('school-info',         [ProprietorPortalController::class, 'schoolInfo'])->name('school-info');
        Route::get('downloads',           [ProprietorPortalController::class, 'downloads'])->name('downloads');
        Route::get('profile',             [ProprietorPortalController::class, 'profile'])->name('profile');
        // Performance Intelligence
        Route::get('performance',         [ProprietorPerformanceController::class, 'dashboard'])->name('performance');
    });

    /*
    |--------------------------------------------------------------------------
    | Librarian Portal routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:librarian')->prefix('school/librarian')->name('librarian.')->group(function () {
        Route::get('dashboard',       [LibrarianPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('books',           [LibrarianPortalController::class, 'books'])->name('books');
        Route::get('issues',          [LibrarianPortalController::class, 'issues'])->name('issues');
        Route::get('overdue',         [LibrarianPortalController::class, 'overdue'])->name('overdue');
        Route::get('announcements',   [LibrarianPortalController::class, 'announcements'])->name('announcements');
        Route::get('profile',         [LibrarianPortalController::class, 'profile'])->name('profile');
    });

    /*
    |--------------------------------------------------------------------------
    | Driver Portal routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:driver')->prefix('school/driver')->name('driver.')->group(function () {
        Route::get('dashboard',       [DriverPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('route',           [DriverPortalController::class, 'route'])->name('route');
        Route::get('students',        [DriverPortalController::class, 'students'])->name('students');
        Route::get('schedule',        [DriverPortalController::class, 'schedule'])->name('schedule');
        Route::get('announcements',   [DriverPortalController::class, 'announcements'])->name('announcements');
        Route::get('profile',         [DriverPortalController::class, 'profile'])->name('profile');
    });

    /*
    |--------------------------------------------------------------------------
    | Warden Portal routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:warden')->prefix('school/warden')->name('warden.')->group(function () {
        Route::get('dashboard',       [WardenPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('hostel',          [WardenPortalController::class, 'hostel'])->name('hostel');
        Route::get('rooms',           [WardenPortalController::class, 'rooms'])->name('rooms');
        Route::get('allocations',     [WardenPortalController::class, 'allocations'])->name('allocations');
        Route::get('students',        [WardenPortalController::class, 'students'])->name('students');
        Route::get('announcements',   [WardenPortalController::class, 'announcements'])->name('announcements');
        Route::get('profile',         [WardenPortalController::class, 'profile'])->name('profile');
    });

    /*
    |--------------------------------------------------------------------------
    | Store Manager Portal routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:store-manager')->prefix('school/store-manager')->name('store-manager.')->group(function () {
        Route::get('dashboard',       [StoreManagerPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('items',           [StoreManagerPortalController::class, 'items'])->name('items');
        Route::get('purchases',       [StoreManagerPortalController::class, 'purchases'])->name('purchases');
        Route::get('issues',          [StoreManagerPortalController::class, 'issues'])->name('issues');
        Route::get('announcements',   [StoreManagerPortalController::class, 'announcements'])->name('announcements');
        Route::get('profile',         [StoreManagerPortalController::class, 'profile'])->name('profile');
    });

    /*
    |--------------------------------------------------------------------------
    | Super Admin routes
    |--------------------------------------------------------------------------*/
    Route::middleware('role:super-admin')
        ->prefix('super-admin')
        ->name('super-admin.')
        ->group(function () {
            Route::resource('schools', SchoolController::class);
            Route::patch('schools/{school}/suspend', [SchoolController::class, 'suspend'])->name('schools.suspend');
            Route::patch('schools/{school}/activate', [SchoolController::class, 'activate'])->name('schools.activate');

            // User Management
            // Packages
            // Dashboard
            Route::get('dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');

            // Platform Settings
            Route::get('settings',                          [SuperAdminSettingsController::class, 'index'])->name('settings.index');
            Route::post('settings/general',                 [SuperAdminSettingsController::class, 'saveGeneral'])->name('settings.general');
            Route::post('settings/payment',                 [SuperAdminSettingsController::class, 'savePayment'])->name('settings.payment');
            Route::post('settings/smtp',                    [SuperAdminSettingsController::class, 'saveSmtp'])->name('settings.smtp');
            Route::post('settings/localization',            [SuperAdminSettingsController::class, 'saveLocalization'])->name('settings.localization');
            Route::post('settings/maintenance',             [SuperAdminSettingsController::class, 'saveMaintenance'])->name('settings.maintenance');
            Route::post('settings/storage',                 [SuperAdminSettingsController::class, 'saveStorage'])->name('settings.storage');
            Route::post('settings/templates',               [SuperAdminSettingsController::class, 'saveTemplate'])->name('settings.templates');
            Route::post('settings/audit',                   [SuperAdminSettingsController::class, 'saveAudit'])->name('settings.audit');

            Route::get('packages',              [PackageController::class, 'index'])->name('packages.index');
            Route::post('packages',             [PackageController::class, 'store'])->name('packages.store');
            Route::put('packages/{package}',    [PackageController::class, 'update'])->name('packages.update');
            Route::delete('packages/{package}', [PackageController::class, 'destroy'])->name('packages.destroy');

            // Subscriptions
            Route::get('subscriptions',                        [SubscriptionController::class, 'index'])->name('subscriptions.index');
            Route::post('subscriptions',                       [SubscriptionController::class, 'store'])->name('subscriptions.store');
            Route::put('subscriptions/{subscription}',         [SubscriptionController::class, 'update'])->name('subscriptions.update');
            Route::delete('subscriptions/{subscription}',      [SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');

            // Coupons
            Route::get('coupons',              [CouponController::class, 'index'])->name('coupons.index');
            Route::post('coupons',             [CouponController::class, 'store'])->name('coupons.store');
            Route::put('coupons/{coupon}',     [CouponController::class, 'update'])->name('coupons.update');
            Route::delete('coupons/{coupon}',  [CouponController::class, 'destroy'])->name('coupons.destroy');

            // Module Manager
            Route::get('module-manager',        [ModuleManagerController::class, 'index'])->name('module-manager.index');
            Route::post('module-manager/toggle', [ModuleManagerController::class, 'toggle'])->name('module-manager.toggle');
            Route::post('module-manager/bulk',   [ModuleManagerController::class, 'bulkSave'])->name('module-manager.bulk');

            // Demo Request Management
            Route::get('demo-requests',                                      [DemoManagementController::class, 'index'])->name('demo-requests.index');
            Route::get('demo-requests/{demoRequest}',                        [DemoManagementController::class, 'show'])->name('demo-requests.show');
            Route::put('demo-requests/{demoRequest}/status',                 [DemoManagementController::class, 'updateStatus'])->name('demo-requests.status');
            Route::post('demo-requests/{demoRequest}/assign',                [DemoManagementController::class, 'assign'])->name('demo-requests.assign');
            Route::post('demo-requests/{demoRequest}/notes',                 [DemoManagementController::class, 'addNote'])->name('demo-requests.notes');

            // User Management
            Route::get('users',                          [UserManagementController::class, 'index'])->name('users.index');
            Route::post('users',                         [UserManagementController::class, 'store'])->name('users.store');
            Route::put('users/{user}',                   [UserManagementController::class, 'update'])->name('users.update');
            Route::delete('users/{user}',                [UserManagementController::class, 'destroy'])->name('users.destroy');
            Route::patch('users/{user}/suspend',         [UserManagementController::class, 'suspend'])->name('users.suspend');
            Route::patch('users/{user}/activate',        [UserManagementController::class, 'activate'])->name('users.activate');
            Route::patch('users/{user}/reset-password',  [UserManagementController::class, 'resetPassword'])->name('users.reset-password');

            // 2FA
            Route::get('2fa/verify', [TwoFactorController::class, 'showVerifyForm'])->name('2fa.verify-form');
            Route::post('2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify');
            Route::get('2fa/enable', [TwoFactorController::class, 'enable'])->name('2fa.enable');
            Route::post('2fa/enable', [TwoFactorController::class, 'confirmEnable'])->name('2fa.confirm-enable');

            // Performance Intelligence
            Route::get('performance',                    [SuperAdminPerformanceController::class, 'dashboard'])->name('performance');
        });

    /*
    |--------------------------------------------------------------------------
    | Ministry of Education Portal routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:ministry-admin|district-officer')
        ->prefix('ministry')
        ->name('ministry.')
        ->group(function () {
            Route::get('dashboard', [MinistryPortalController::class, 'dashboard'])->name('dashboard');

            // Schools Management
            Route::get('schools',                           [MinistryPortalController::class, 'schools'])->name('schools');
            Route::get('schools/approvals',                 [MinistryPortalController::class, 'schoolApprovals'])->name('schools.approvals');
            Route::post('schools/{school}/approve',         [MinistryPortalController::class, 'approveSchool'])->name('schools.approve');
            Route::patch('schools/{school}/suspend',        [MinistryPortalController::class, 'suspendSchool'])->name('schools.suspend');

            // District Management
            Route::get('districts',                         [MinistryPortalController::class, 'districts'])->name('districts');
            Route::get('districts/officers',                [MinistryPortalController::class, 'districtOfficers'])->name('districts.officers');

            // Student Registry
            Route::get('students',                          [MinistryPortalController::class, 'students'])->name('students');
            Route::get('students/analytics',                [MinistryPortalController::class, 'studentAnalytics'])->name('students.analytics');

            // Teacher Registry
            Route::get('teachers',                          [MinistryPortalController::class, 'teachers'])->name('teachers');
            Route::get('teachers/licensing',                [MinistryPortalController::class, 'teacherLicensing'])->name('teachers.licensing');

            // National Examinations
            Route::get('exams/npse',                        [MinistryPortalController::class, 'examNpse'])->name('exams.npse');
            Route::get('exams/bece',                        [MinistryPortalController::class, 'examBece'])->name('exams.bece');
            Route::get('exams/wasse',                       [MinistryPortalController::class, 'examWasse'])->name('exams.wasse');
            Route::get('exams/analytics',                   [MinistryPortalController::class, 'examAnalytics'])->name('exams.analytics');

            // Quality Assurance
            Route::get('inspections',                       [MinistryPortalController::class, 'inspections'])->name('inspections');
            Route::get('inspections/compliance',            [MinistryPortalController::class, 'inspectionCompliance'])->name('inspections.compliance');
            Route::get('inspections/ratings',               [MinistryPortalController::class, 'inspectionRatings'])->name('inspections.ratings');

            // Reports & Analytics
            Route::get('reports',                           [MinistryPortalController::class, 'reports'])->name('reports');
            Route::get('reports/districts',                 [MinistryPortalController::class, 'reportDistricts'])->name('reports.districts');
            Route::get('reports/enrollment',                [MinistryPortalController::class, 'reportEnrollment'])->name('reports.enrollment');
            Route::get('reports/gender',                    [MinistryPortalController::class, 'reportGender'])->name('reports.gender');

            // Communication
            Route::get('communication/announcements',       [MinistryPortalController::class, 'announcements'])->name('communication.announcements');
            Route::get('communication/circulars',           [MinistryPortalController::class, 'circulars'])->name('communication.circulars');
            Route::get('communication/alerts',              [MinistryPortalController::class, 'alerts'])->name('communication.alerts');

            // Downloads
            Route::get('downloads',                         [MinistryPortalController::class, 'downloads'])->name('downloads');

            // Administration
            Route::get('admin/users',                       [MinistryPortalController::class, 'adminUsers'])->name('admin.users');
            Route::get('admin/roles',                       [MinistryPortalController::class, 'adminRoles'])->name('admin.roles');
            Route::get('admin/audit',                       [MinistryPortalController::class, 'adminAudit'])->name('admin.audit');
            Route::get('admin/settings',                    [MinistryPortalController::class, 'adminSettings'])->name('admin.settings');
        });
});

// Public admission form (no auth)
Route::get('/apply/{school}',  [PublicAdmissionController::class, 'show'])->name('public.admission.show');
Route::post('/apply/{school}', [PublicAdmissionController::class, 'submit'])->middleware('throttle:demo')->name('public.admission.submit');
