<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\AcademicTerm;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\BookIssue;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Exam;
use App\Models\FeeCategory;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\GradeScale;
use App\Models\Guardian;
use App\Models\Holiday;
use App\Models\Hostel;
use App\Models\HostelRoom;
use App\Models\HostelAllocation;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\Book;
use App\Models\Mark;
use App\Models\Message;
use App\Models\Package;
use App\Models\Payroll;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolSubscription;
use App\Models\SchoolTimeSetting;
use App\Models\Section;
use App\Models\Shift;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    private int $schoolId;
    private $school;
    private $academicYear;
    private $term1;
    private array $store = [];

    private array $maleFirstNames = ['Ibrahim', 'Mohamed', 'Samuel', 'John', 'Peter', 'Daniel', 'Emmanuel', 'Abdul', 'David', 'Ishmael', 'Kamara', 'Sesay', 'Koroma', 'Mansaray', 'Turay', 'Conteh', 'Bangura', 'Kargbo'];
    private array $femaleFirstNames = ['Fatima', 'Aminata', 'Hawa', 'Mariam', 'Grace', 'Ruth', 'Sarah', 'Christiana', 'Isata', 'Adama', 'Naomi', 'Esther', 'Kadiatu', 'Memunatu', 'Salamatu', 'Ramatu'];
    private array $lastNames = ['Bangura', 'Sesay', 'Koroma', 'Kamara', 'Conteh', 'Turay', 'Mansaray', 'Kargbo', 'Sulaiman', 'Jalloh', 'Fofanah', 'Dumbuya', 'Mara', 'Yillah', 'Fullah'];
    private array $religions = ['Islam', 'Christianity'];
    private array $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+'];
    private array $occupations = ['Teacher', 'Trader', 'Civil Servant', 'Doctor', 'Lawyer', 'Farmer', 'Engineer', 'Business Owner', 'Nurse', 'Driver'];
    private array $addresses = ['12 Lumley Road, Freetown', '45 Kissy Road, Freetown', '7 Siaka Stevens Street', '23 Wilberforce, Freetown', '8 Kissi Town, Freetown', '31 Congo Cross, Freetown', '15 Hill Station, Freetown', '50 Port Loko Road'];

    public function run(): void
    {
        $this->command->info('Seeding Freetown International Academy demo environment...');

        $this->createSuperAdmin();
        $this->createSchool();
        $this->createAcademicStructure();
        $this->createDepartmentsAndDesignations();
        $this->createGradeScale();
        $this->createFeeCategories();
        $this->createClassesSectionsAndSubjects();
        $this->createSchoolTimeSettings();
        $this->createManagementUsers();
        $this->createTeachers();
        $this->createStudentsAndParents();
        $this->createExams();
        $this->createMarks();
        $this->createAttendance();
        $this->createTimetable();
        $this->createFeesAndPayments();
        $this->createPayroll();
        $this->createLibrary();
        $this->createTransport();
        $this->createHostel();
        $this->createInventory();
        $this->createCommunication();
        $this->createHolidays();

        $this->command->info('Demo environment seeded successfully!');
    }

    private function createSuperAdmin(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'syscend@gmail.com'],
            [
                'name' => 'Super Administrator',
                'phone' => '+23279630777',
                'password' => Hash::make('Demo@123'),
                'status' => 'active',
            ]
        );
        $user->syncRoles(['super-admin']);
        $this->command->line('  ✓ Super Administrator');
    }

    private function createSchool(): void
    {
        $this->school = School::create([
            'name' => 'Freetown International Academy',
            'short_name' => 'FIA',
            'slug' => 'freetown-international-academy',
            'email' => 'info@freetownacademy.edu',
            'phone' => '+23278123456',
            'address' => '25 Lumley Beach Road',
            'city' => 'Freetown',
            'country' => 'SL',
            'timezone' => 'Africa/Freetown',
            'currency' => 'SLL',
            'currency_symbol' => 'Le',
            'language' => 'en',
            'status' => 'active',
            'is_configured' => true,
            'motto' => 'Excellence Through Knowledge',
            'year_established' => '2010',
            'school_level' => 'combined',
            'mbsse_registration_number' => 'MBSSE/FIA/2010/001',
            'emis_code' => 'EMIS-FIA-001',
            'primary_color' => '#1e40af',
            'secondary_color' => '#f59e0b',
            'about_school' => 'Freetown International Academy is a leading co-educational institution in Sierra Leone, providing quality education from nursery through senior secondary level.',
            'school_mission' => 'To provide quality education that prepares students for academic excellence and responsible citizenship.',
            'school_vision' => 'To be the premier educational institution in West Africa.',
            'working_days' => 'monday,tuesday,wednesday,thursday,friday',
            'school_opening_time' => '07:30',
            'school_closing_time' => '15:00',
            'ca_weight' => 40,
            'exam_weight' => 60,
            'public_profile_enabled' => true,
        ]);
        $this->schoolId = $this->school->id;

        $enterprisePackage = Package::create([
            'name' => 'Enterprise',
            'slug' => 'enterprise',
            'description' => 'Full access to all platform features with unlimited capacity.',
            'price_monthly' => 500000,
            'price_yearly' => 5000000,
            'max_students' => -1,
            'max_staff' => -1,
            'storage_gb' => 100,
            'is_active' => true,
            'features' => ['students', 'staff', 'exams', 'fees', 'library', 'transport', 'hostel', 'inventory', 'communication', 'reports', 'analytics', 'sms', 'all'],
        ]);

        SchoolSubscription::create([
            'school_id' => $this->schoolId,
            'package_id' => $enterprisePackage->id,
            'start_date' => now()->subMonth(),
            'end_date' => now()->addYear(),
            'status' => 'active',
            'is_trial' => false,
            'amount_paid' => 5000000,
            'payment_method' => 'bank_transfer',
            'notes' => 'Enterprise annual subscription',
        ]);

        $this->command->line('  ✓ Demo School + Enterprise Subscription');
    }

    private function createAcademicStructure(): void
    {
        $this->academicYear = AcademicYear::create([
            'school_id' => $this->schoolId,
            'name' => '2025-2026',
            'start_date' => '2025-09-01',
            'end_date' => '2026-07-15',
            'is_current' => true,
        ]);

        $this->term1 = AcademicTerm::create([
            'school_id' => $this->schoolId,
            'academic_year_id' => $this->academicYear->id,
            'name' => 'Term 1',
            'start_date' => '2025-09-01',
            'end_date' => '2025-12-15',
            'mid_term_start' => '2025-10-20',
            'mid_term_end' => '2025-10-24',
            'is_current' => true,
        ]);

        AcademicTerm::create([
            'school_id' => $this->schoolId,
            'academic_year_id' => $this->academicYear->id,
            'name' => 'Term 2',
            'start_date' => '2026-01-05',
            'end_date' => '2026-04-10',
            'is_current' => false,
        ]);

        AcademicTerm::create([
            'school_id' => $this->schoolId,
            'academic_year_id' => $this->academicYear->id,
            'name' => 'Term 3',
            'start_date' => '2026-04-27',
            'end_date' => '2026-07-15',
            'is_current' => false,
        ]);

        $this->command->line('  ✓ Academic Year + 3 Terms');
    }

    private function createDepartmentsAndDesignations(): void
    {
        $depts = ['Mathematics', 'English', 'Science', 'Humanities', 'Languages', 'ICT', 'Administration'];
        $deptModels = [];
        foreach ($depts as $name) {
            $code = strtoupper(substr($name, 0, 3));
            $deptModels[$name] = Department::create([
                'school_id' => $this->schoolId,
                'name' => $name,
                'code' => $code,
                'type' => $name === 'Administration' ? 'administrative' : 'academic',
                'is_active' => true,
            ]);
        }

        // Academic departments for SSS
        $sssDepts = ['Science' => $deptModels['Science'], 'Arts' => Department::create([
            'school_id' => $this->schoolId, 'name' => 'Arts', 'code' => 'ART', 'type' => 'academic', 'is_active' => true,
        ]), 'Commercial' => Department::create([
            'school_id' => $this->schoolId, 'name' => 'Commercial', 'code' => 'COM', 'type' => 'academic', 'is_active' => true,
        ])];

        // Designations
        $teacherDesig = Designation::create(['school_id' => $this->schoolId, 'department_id' => $deptModels['Administration']->id, 'name' => 'Teacher']);
        Designation::create(['school_id' => $this->schoolId, 'department_id' => $deptModels['Administration']->id, 'name' => 'Senior Teacher']);
        Designation::create(['school_id' => $this->schoolId, 'department_id' => $deptModels['Administration']->id, 'name' => 'Head of Department']);
        Designation::create(['school_id' => $this->schoolId, 'department_id' => $deptModels['Administration']->id, 'name' => 'Principal']);
        Designation::create(['school_id' => $this->schoolId, 'department_id' => $deptModels['Administration']->id, 'name' => 'Vice Principal']);
        $accountantDesig = Designation::create(['school_id' => $this->schoolId, 'department_id' => $deptModels['Administration']->id, 'name' => 'Accountant']);
        $librarianDesig = Designation::create(['school_id' => $this->schoolId, 'department_id' => $deptModels['Administration']->id, 'name' => 'Librarian']);
        $adminDesig = Designation::create(['school_id' => $this->schoolId, 'department_id' => $deptModels['Administration']->id, 'name' => 'School Administrator']);
        Designation::create(['school_id' => $this->schoolId, 'department_id' => $deptModels['Administration']->id, 'name' => 'Proprietor']);

        $this->store['deptModels'] = $deptModels;
        $this->store['sssDepts'] = $sssDepts;
        $this->store['teacherDesig'] = $teacherDesig;
        $this->store['accountantDesig'] = $accountantDesig;
        $this->store['librarianDesig'] = $librarianDesig;
        $this->store['adminDesig'] = $adminDesig;

        $this->command->line('  ✓ Departments + Designations');
    }

    private function createGradeScale(): void
    {
        $grades = [
            ['grade' => 'A1', 'gpa' => 4.0, 'min_marks' => 75, 'max_marks' => 100, 'remarks' => 'Excellent', 'sort_order' => 1],
            ['grade' => 'B2', 'gpa' => 3.5, 'min_marks' => 65, 'max_marks' => 74, 'remarks' => 'Very Good', 'sort_order' => 2],
            ['grade' => 'B3', 'gpa' => 3.0, 'min_marks' => 55, 'max_marks' => 64, 'remarks' => 'Good', 'sort_order' => 3],
            ['grade' => 'C4', 'gpa' => 2.5, 'min_marks' => 45, 'max_marks' => 54, 'remarks' => 'Average', 'sort_order' => 4],
            ['grade' => 'C5', 'gpa' => 2.0, 'min_marks' => 40, 'max_marks' => 44, 'remarks' => 'Above Average', 'sort_order' => 5],
            ['grade' => 'C6', 'gpa' => 1.5, 'min_marks' => 35, 'max_marks' => 39, 'remarks' => 'Average', 'sort_order' => 6],
            ['grade' => 'D7', 'gpa' => 1.0, 'min_marks' => 30, 'max_marks' => 34, 'remarks' => 'Pass', 'sort_order' => 7],
            ['grade' => 'E8', 'gpa' => 0.5, 'min_marks' => 20, 'max_marks' => 29, 'remarks' => 'Weak Pass', 'sort_order' => 8],
            ['grade' => 'F9', 'gpa' => 0.0, 'min_marks' => 0, 'max_marks' => 19, 'remarks' => 'Fail', 'sort_order' => 9],
        ];
        foreach ($grades as $g) {
            GradeScale::create(array_merge($g, ['school_id' => $this->schoolId]));
        }
        $this->command->line('  ✓ Grade Scale (A1-F9)');
    }

    private function createFeeCategories(): void
    {
        $cats = [
            ['name' => 'Tuition Fee', 'description' => 'Annual tuition fee', 'type' => 'tuition'],
            ['name' => 'Transport Fee', 'description' => 'School bus transportation', 'type' => 'transport'],
            ['name' => 'Examination Fee', 'description' => 'Examination charges per term', 'type' => 'exam'],
            ['name' => 'Library Fee', 'description' => 'Library access fee', 'type' => 'library'],
            ['name' => 'Activity Fee', 'description' => 'Sports and extracurricular activities', 'type' => 'sports'],
        ];
        foreach ($cats as $cat) {
            FeeCategory::create(array_merge($cat, ['school_id' => $this->schoolId, 'is_active' => true]));
        }
        $this->command->line('  ✓ Fee Categories');
    }

    private function createClassesSectionsAndSubjects(): void
    {
        $sectionNames = ['A', 'B'];
        $this->classes = [];
        $this->subjects = [];

        $levelConfig = [
            'early_childhood' => [
                'classes' => ['Nursery 1', 'Nursery 2', 'Nursery 3'],
                'subjects' => [
                    ['name' => 'Literacy', 'code' => 'LIT', 'type' => 'theory'],
                    ['name' => 'Numeracy', 'code' => 'NUM', 'type' => 'theory'],
                    ['name' => 'Creative Activities', 'code' => 'CRE', 'type' => 'practical'],
                    ['name' => 'Social Studies', 'code' => 'SST', 'type' => 'theory'],
                    ['name' => 'Physical Education', 'code' => 'PE', 'type' => 'practical'],
                ],
            ],
            'primary' => [
                'classes' => ['Primary 1', 'Primary 2', 'Primary 3', 'Primary 4', 'Primary 5', 'Primary 6'],
                'subjects' => [
                    ['name' => 'English Language', 'code' => 'ENG', 'type' => 'theory'],
                    ['name' => 'Mathematics', 'code' => 'MAT', 'type' => 'theory'],
                    ['name' => 'Science', 'code' => 'SCI', 'type' => 'theory'],
                    ['name' => 'Social Studies', 'code' => 'SST', 'type' => 'theory'],
                    ['name' => 'Agric Science', 'code' => 'AGR', 'type' => 'theory'],
                    ['name' => 'Religious Studies', 'code' => 'REL', 'type' => 'theory'],
                    ['name' => 'Home Economics', 'code' => 'HEC', 'type' => 'practical'],
                    ['name' => 'Physical Education', 'code' => 'PE', 'type' => 'practical'],
                ],
            ],
            'junior_secondary' => [
                'classes' => ['JSS 1', 'JSS 2', 'JSS 3'],
                'subjects' => [
                    ['name' => 'English Language', 'code' => 'ENG', 'type' => 'theory'],
                    ['name' => 'Mathematics', 'code' => 'MAT', 'type' => 'theory'],
                    ['name' => 'Science', 'code' => 'SCI', 'type' => 'theory'],
                    ['name' => 'Social Studies', 'code' => 'SST', 'type' => 'theory'],
                    ['name' => 'Agric Science', 'code' => 'AGR', 'type' => 'theory'],
                    ['name' => 'Religious Studies', 'code' => 'REL', 'type' => 'theory'],
                    ['name' => 'French', 'code' => 'FRE', 'type' => 'theory'],
                    ['name' => 'Computer Studies', 'code' => 'ICT', 'type' => 'practical'],
                    ['name' => 'Physical Education', 'code' => 'PE', 'type' => 'practical'],
                ],
            ],
            'senior_secondary' => [
                'classes' => ['SSS 1', 'SSS 2', 'SSS 3'],
                'subjects' => [
                    ['name' => 'English Language', 'code' => 'ENG', 'type' => 'theory'],
                    ['name' => 'Mathematics', 'code' => 'MAT', 'type' => 'theory'],
                    ['name' => 'Physics', 'code' => 'PHY', 'type' => 'theory'],
                    ['name' => 'Chemistry', 'code' => 'CHE', 'type' => 'theory'],
                    ['name' => 'Biology', 'code' => 'BIO', 'type' => 'theory'],
                    ['name' => 'Agric Science', 'code' => 'AGR', 'type' => 'theory'],
                    ['name' => 'Literature in English', 'code' => 'LIT', 'type' => 'theory'],
                    ['name' => 'Government', 'code' => 'GOV', 'type' => 'theory'],
                    ['name' => 'History', 'code' => 'HIS', 'type' => 'theory'],
                    ['name' => 'Geography', 'code' => 'GEO', 'type' => 'theory'],
                    ['name' => 'Economics', 'code' => 'ECO', 'type' => 'theory'],
                    ['name' => 'Principles of Accounting', 'code' => 'ACC', 'type' => 'theory'],
                    ['name' => 'Business Studies', 'code' => 'BUS', 'type' => 'theory'],
                ],
            ],
        ];

        $order = 0;
        foreach ($levelConfig as $level => $config) {
            foreach ($config['classes'] as $className) {
                $order++;
                $class = SchoolClass::create([
                    'school_id' => $this->schoolId,
                    'name' => $className,
                    'short_name' => $className,
                    'numeric_name' => $order,
                    'capacity' => in_array($level, ['early_childhood']) ? 35 : 45,
                    'school_level' => $level,
                    'level_order' => $order,
                ]);

                foreach ($sectionNames as $sName) {
                    Section::create([
                        'school_id' => $this->schoolId,
                        'class_id' => $class->id,
                        'name' => $sName,
                        'capacity' => in_array($level, ['early_childhood']) ? 18 : 23,
                    ]);
                }

                $this->classes[$className] = $class;

                foreach ($config['subjects'] as $sub) {
                    $subject = Subject::create([
                        'school_id' => $this->schoolId,
                        'class_id' => $class->id,
                        'name' => $sub['name'],
                        'code' => $sub['code'],
                        'type' => $sub['type'],
                        'full_marks' => 100,
                        'pass_marks' => 50,
                        'school_level' => $level,
                        'is_core' => in_array($sub['name'], ['English Language', 'Mathematics']),
                    ]);
                    $this->subjects[$className][] = $subject;
                }
            }
        }

        // Shifts
        Shift::create(['school_id' => $this->schoolId, 'name' => 'Morning Shift', 'start_time' => '07:30', 'end_time' => '12:30']);
        Shift::create(['school_id' => $this->schoolId, 'name' => 'Day Shift', 'start_time' => '08:00', 'end_time' => '15:00']);

        $this->command->line('  ✓ ' . count($this->classes) . ' Classes + Sections + Subjects');
    }

    private function createSchoolTimeSettings(): void
    {
        SchoolTimeSetting::create([
            'school_id' => $this->schoolId,
            'academic_year_id' => $this->academicYear->id,
            'opening_time' => '07:30',
            'closing_time' => '15:00',
            'working_days' => 'monday,tuesday,wednesday,thursday,friday',
            'timezone' => 'Africa/Freetown',
            'clock_format' => '12h',
            'day_start' => '07:30',
            'day_end' => '15:00',
        ]);
        $this->command->line('  ✓ School Time Settings');
    }

    private function createManagementUsers(): void
    {
        $empNo = 1;
        $mgmt = [
            ['first' => 'Abdulai', 'last' => 'Conteh', 'email' => 'proprietor@freetownacademy.edu', 'role' => 'proprietor', 'phone' => '+23278100001', 'gender' => 'male', 'dob' => '1965-05-20'],
            ['first' => 'Mohamed', 'last' => 'Sesay', 'email' => 'admin@freetownacademy.edu', 'role' => 'school-admin', 'phone' => '+23278100002', 'gender' => 'male', 'dob' => '1978-08-12'],
            ['first' => 'Fatima', 'last' => 'Bangura', 'email' => 'principal@freetownacademy.edu', 'role' => 'principal', 'phone' => '+23278100003', 'gender' => 'female', 'dob' => '1972-03-15'],
            ['first' => 'James', 'last' => 'Koroma', 'email' => 'vp@freetownacademy.edu', 'role' => 'principal', 'phone' => '+23278100004', 'gender' => 'male', 'dob' => '1975-11-28'],
            ['first' => 'Ishmael', 'last' => 'Turay', 'email' => 'accountant@freetownacademy.edu', 'role' => 'accountant', 'phone' => '+23278100005', 'gender' => 'male', 'dob' => '1980-06-10'],
            ['first' => 'Aminata', 'last' => 'Sesay', 'email' => 'librarian@freetownacademy.edu', 'role' => 'librarian', 'phone' => '+23278100006', 'gender' => 'female', 'dob' => '1983-09-22'],
            ['first' => 'Helen', 'last' => 'Moses', 'email' => 'guidance@freetownacademy.edu', 'role' => 'teacher', 'phone' => '+23278100007', 'gender' => 'female', 'dob' => '1981-04-18'],
            ['first' => 'Peter', 'last' => 'Kamara', 'email' => 'exam.officer@freetownacademy.edu', 'role' => 'teacher', 'phone' => '+23278100008', 'gender' => 'male', 'dob' => '1979-12-05'],
            ['first' => 'Grace', 'last' => 'Turay', 'email' => 'receptionist@freetownacademy.edu', 'role' => 'receptionist', 'phone' => '+23278100009', 'gender' => 'female', 'dob' => '1988-07-14'],
        ];

        foreach ($mgmt as $m) {
            $user = User::create([
                'school_id' => $this->schoolId,
                'name' => $m['first'] . ' ' . $m['last'],
                'email' => $m['email'],
                'username' => strtolower($m['first']) . '.' . strtolower($m['last']),
                'phone' => $m['phone'],
                'password' => Hash::make('Demo@123'),
                'status' => 'active',
                'is_temporary_password' => true,
                'must_change_password' => true,
            ]);
            $user->syncRoles([$m['role']]);

            Staff::create([
                'school_id' => $this->schoolId,
                'user_id' => $user->id,
                'department_id' => $this->store['deptModels']['Administration']->id,
                'designation_id' => $this->store['adminDesig']->id,
                'emp_id' => 'FIA-ADM-' . str_pad($empNo++, 3, '0', STR_PAD_LEFT),
                'first_name' => $m['first'],
                'last_name' => $m['last'],
                'gender' => $m['gender'],
                'date_of_birth' => $m['dob'],
                'nationality' => 'Sierra Leonean',
                'phone' => $m['phone'],
                'email' => $m['email'],
                'joining_date' => '2015-09-01',
                'salary_type' => 'fixed',
                'salary' => $m['role'] === 'proprietor' ? 8000000 : ($m['role'] === 'school-admin' ? 5000000 : ($m['role'] === 'principal' ? 6000000 : 3000000)),
                'status' => 'active',
                'teacher_type' => 'non_teaching',
            ]);
        }
        $this->command->line('  ✓ Management Staff (9 users)');
    }

    private function createTeachers(): void
    {
        $teachers = [
            ['first' => 'Amara', 'last' => 'Kamara', 'email' => 'amara.kamara@freetownacademy.edu', 'phone' => '+23278200001', 'gender' => 'female', 'dob' => '1985-03-15', 'dept' => 'Mathematics', 'salary' => 3500000],
            ['first' => 'Ibrahim', 'last' => 'Sesay', 'email' => 'ibrahim.sesay@freetownacademy.edu', 'phone' => '+23278200002', 'gender' => 'male', 'dob' => '1982-07-22', 'dept' => 'English', 'salary' => 3500000],
            ['first' => 'Fatmata', 'last' => 'Bangura', 'email' => 'fatmata.b@freetownacademy.edu', 'phone' => '+23278200003', 'gender' => 'female', 'dob' => '1987-01-10', 'dept' => 'Science', 'salary' => 3200000],
            ['first' => 'Samuel', 'last' => 'Koroma', 'email' => 'samuel.k@freetownacademy.edu', 'phone' => '+23278200004', 'gender' => 'male', 'dob' => '1980-11-05', 'dept' => 'Humanities', 'salary' => 3200000],
            ['first' => 'Mariam', 'last' => 'Conteh', 'email' => 'mariam.c@freetownacademy.edu', 'phone' => '+23278200005', 'gender' => 'female', 'dob' => '1986-05-18', 'dept' => 'Languages', 'salary' => 3000000],
            ['first' => 'David', 'last' => 'Kamara', 'email' => 'david.k@freetownacademy.edu', 'phone' => '+23278200006', 'gender' => 'male', 'dob' => '1984-09-30', 'dept' => 'ICT', 'salary' => 3500000],
            ['first' => 'Hawa', 'last' => 'Turay', 'email' => 'hawa.t@freetownacademy.edu', 'phone' => '+23278200007', 'gender' => 'female', 'dob' => '1983-02-25', 'dept' => 'Science', 'salary' => 3000000],
            ['first' => 'John', 'last' => 'Mansaray', 'email' => 'john.m@freetownacademy.edu', 'phone' => '+23278200008', 'gender' => 'male', 'dob' => '1979-08-12', 'dept' => 'Science', 'salary' => 3800000],
        ];

        $empNo = 1;
        $this->teachers = [];

        foreach ($teachers as $t) {
            $user = User::create([
                'school_id' => $this->schoolId,
                'name' => $t['first'] . ' ' . $t['last'],
                'email' => $t['email'],
                'username' => strtolower($t['first']) . '.' . strtolower($t['last']),
                'phone' => $t['phone'],
                'password' => Hash::make('Demo@123'),
                'status' => 'active',
                'is_temporary_password' => true,
                'must_change_password' => true,
            ]);
            $user->syncRoles(['teacher']);

            $deptId = $this->store['deptModels'][$t['dept']]->id ?? $this->store['deptModels']['Administration']->id;
            $staff = Staff::create([
                'school_id' => $this->schoolId,
                'user_id' => $user->id,
                'department_id' => $deptId,
                'designation_id' => $this->store['teacherDesig']->id,
                'emp_id' => 'FIA-TCH-' . str_pad($empNo++, 3, '0', STR_PAD_LEFT),
                'first_name' => $t['first'],
                'last_name' => $t['last'],
                'gender' => $t['gender'],
                'date_of_birth' => $t['dob'],
                'nationality' => 'Sierra Leonean',
                'phone' => $t['phone'],
                'email' => $t['email'],
                'joining_date' => '2018-09-01',
                'salary_type' => 'fixed',
                'salary' => $t['salary'],
                'status' => 'active',
                'teacher_type' => 'subject_teacher',
            ]);
            $this->teachers[] = $staff;
        }
        $this->command->line('  ✓ Teachers (8 staff)');
    }

    private function createStudentsAndParents(): void
    {
        $studentDefs = [
            ['first' => 'Ibrahim', 'last' => 'Bangura', 'gender' => 'male', 'dob' => '2008-04-12', 'class' => 'JSS 3', 'section' => 'A', 'religion' => 'Islam'],
            ['first' => 'Fatima', 'last' => 'Sesay', 'gender' => 'female', 'dob' => '2006-09-20', 'class' => 'SSS 3', 'section' => 'A', 'religion' => 'Islam'],
            ['first' => 'Mohamed', 'last' => 'Conteh', 'gender' => 'male', 'dob' => '2011-01-15', 'class' => 'Primary 6', 'section' => 'A', 'religion' => 'Islam'],
            ['first' => 'Aminata', 'last' => 'Kamara', 'gender' => 'female', 'dob' => '2020-06-08', 'class' => 'Nursery 1', 'section' => 'A', 'religion' => 'Christianity'],
            ['first' => 'Samuel', 'last' => 'Koroma', 'gender' => 'male', 'dob' => '2018-03-22', 'class' => 'Primary 1', 'section' => 'A', 'religion' => 'Christianity'],
            ['first' => 'Hawa', 'last' => 'Turay', 'gender' => 'female', 'dob' => '2017-11-05', 'class' => 'Primary 2', 'section' => 'A', 'religion' => 'Islam'],
            ['first' => 'John', 'last' => 'Mansaray', 'gender' => 'male', 'dob' => '2016-07-14', 'class' => 'Primary 3', 'section' => 'A', 'religion' => 'Christianity'],
            ['first' => 'Mariam', 'last' => 'Conteh', 'gender' => 'female', 'dob' => '2015-02-28', 'class' => 'Primary 4', 'section' => 'A', 'religion' => 'Islam'],
            ['first' => 'Ishmael', 'last' => 'Sesay', 'gender' => 'male', 'dob' => '2014-08-10', 'class' => 'Primary 5', 'section' => 'B', 'religion' => 'Islam'],
            ['first' => 'Grace', 'last' => 'Kamara', 'gender' => 'female', 'dob' => '2012-05-17', 'class' => 'JSS 1', 'section' => 'A', 'religion' => 'Christianity'],
            ['first' => 'Peter', 'last' => 'Bangura', 'gender' => 'male', 'dob' => '2011-10-03', 'class' => 'JSS 2', 'section' => 'A', 'religion' => 'Christianity'],
            ['first' => 'Ruth', 'last' => 'Koroma', 'gender' => 'female', 'dob' => '2009-12-25', 'class' => 'SSS 1', 'section' => 'A', 'religion' => 'Christianity'],
            ['first' => 'Daniel', 'last' => 'Turay', 'gender' => 'male', 'dob' => '2008-07-08', 'class' => 'SSS 2', 'section' => 'A', 'religion' => 'Islam'],
            ['first' => 'Sarah', 'last' => 'Conteh', 'gender' => 'female', 'dob' => '2019-04-15', 'class' => 'Nursery 2', 'section' => 'A', 'religion' => 'Islam'],
            ['first' => 'Emmanuel', 'last' => 'Sesay', 'gender' => 'male', 'dob' => '2018-09-30', 'class' => 'Nursery 3', 'section' => 'A', 'religion' => 'Christianity'],
            ['first' => 'Christiana', 'last' => 'Mansaray', 'gender' => 'female', 'dob' => '2018-01-20', 'class' => 'Primary 1', 'section' => 'B', 'religion' => 'Christianity'],
            ['first' => 'Abdul', 'last' => 'Rahman Kamara', 'gender' => 'male', 'dob' => '2008-02-14', 'class' => 'JSS 3', 'section' => 'B', 'religion' => 'Islam'],
            ['first' => 'Isata', 'last' => 'Bangura', 'gender' => 'female', 'dob' => '2006-06-21', 'class' => 'SSS 3', 'section' => 'B', 'religion' => 'Islam'],
        ];

        $this->students = [];
        $guardianNo = 1;

        foreach ($studentDefs as $i => $s) {
            $guardian = Guardian::create([
                'school_id' => $this->schoolId,
                'name' => 'Parent of ' . $s['first'] . ' ' . $s['last'],
                'relation' => $s['gender'] === 'male' ? 'Father' : 'Mother',
                'phone' => '+232783' . str_pad($i + 1, 7, '0', STR_PAD_LEFT),
                'email' => strtolower(str_replace(' ', '.', $s['first'])) . '.parent' . ($i + 1) . '@freetownacademy.edu',
                'occupation' => $this->occupations[array_rand($this->occupations)],
                'address' => $this->addresses[array_rand($this->addresses)],
            ]);

            $parentUser = User::create([
                'school_id' => $this->schoolId,
                'name' => $guardian->name,
                'email' => $guardian->email,
                'username' => strtolower(str_replace(' ', '.', $s['first'])) . '.parent' . ($i + 1),
                'phone' => $guardian->phone,
                'password' => Hash::make('Demo@123'),
                'status' => 'active',
                'is_temporary_password' => true,
                'must_change_password' => true,
            ]);
            $parentUser->syncRoles(['parent']);
            $guardian->update(['user_id' => $parentUser->id]);

            $class = $this->classes[$s['class']] ?? null;
            if (!$class) continue;

            $section = Section::where('school_id', $this->schoolId)
                ->where('class_id', $class->id)
                ->where('name', $s['section'])
                ->first();

            $admYear = date('Y', strtotime($s['dob'] . '+5 years'));
            $student = Student::create([
                'school_id' => $this->schoolId,
                'class_id' => $class->id,
                'section_id' => $section?->id,
                'guardian_id' => $guardian->id,
                'first_name' => $s['first'],
                'last_name' => $s['last'],
                'gender' => $s['gender'],
                'date_of_birth' => $s['dob'],
                'nationality' => 'Sierra Leonean',
                'religion' => $s['religion'],
                'blood_group' => $this->bloodGroups[array_rand($this->bloodGroups)],
                'category' => 'general',
                'status' => 'active',
                'admission_date' => $admYear . '-09-01',
                'admission_no' => 'FIA-' . $admYear . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'roll_no' => (string) ($i + 1),
            ]);

            $this->students[] = $student;
        }
        $this->command->line('  ✓ ' . count($this->students) . ' Students + Guardians + Parent Users');
    }

    private function createExams(): void
    {
        $this->exams = [];

        foreach ($this->classes as $className => $class) {
            $exam = Exam::create([
                'school_id' => $this->schoolId,
                'class_id' => $class->id,
                'name' => 'First Term Examination',
                'type' => 'final',
                'start_date' => '2025-11-24',
                'end_date' => '2025-12-05',
                'status' => 'published',
                'academic_year_id' => $this->academicYear->id,
                'term_id' => $this->term1->id,
                'assessment_category' => 'exam',
                'ca_weight' => 40,
                'exam_weight' => 60,
                'max_score' => 100,
            ]);
            $this->exams[$className] = $exam;
        }
        $this->command->line('  ✓ Exams for all classes');
    }

    private function createMarks(): void
    {
        $count = 0;
        foreach ($this->students as $student) {
            $classId = $student->class_id;
            $className = SchoolClass::withoutGlobalScopes()->find($classId)?->name;
            if (!$className || !isset($this->exams[$className]) || !isset($this->subjects[$className])) continue;

            $exam = $this->exams[$className];
            $subjects = $this->subjects[$className];

            foreach ($subjects as $subject) {
                $marks = rand(30, 92);
                $grade = $this->calculateGrade($marks);
                $gpa = $this->calculateGpa($marks);

                Mark::create([
                    'school_id' => $this->schoolId,
                    'exam_id' => $exam->id,
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'marks_obtained' => $marks,
                    'grade' => $grade,
                    'gpa' => $gpa,
                    'is_absent' => false,
                    'remarks' => $marks >= 75 ? 'Excellent' : ($marks >= 50 ? 'Good' : 'Needs improvement'),
                ]);
                $count++;
            }
        }
        $this->command->line("  ✓ {$count} Marks records");
    }

    private function calculateGrade(int $marks): string
    {
        return match(true) {
            $marks >= 75 => 'A1',
            $marks >= 65 => 'B2',
            $marks >= 55 => 'B3',
            $marks >= 45 => 'C4',
            $marks >= 40 => 'C5',
            $marks >= 35 => 'C6',
            $marks >= 30 => 'D7',
            $marks >= 20 => 'E8',
            default => 'F9',
        };
    }

    private function calculateGpa(int $marks): float
    {
        return match(true) {
            $marks >= 75 => 4.0,
            $marks >= 65 => 3.5,
            $marks >= 55 => 3.0,
            $marks >= 45 => 2.5,
            $marks >= 40 => 2.0,
            $marks >= 35 => 1.5,
            $marks >= 30 => 1.0,
            $marks >= 20 => 0.5,
            default => 0.0,
        };
    }

    private function createAttendance(): void
    {
        $count = 0;
        $startDate = new \Carbon\Carbon('2025-09-08');
        $schoolDays = 0;

        for ($day = 0; $schoolDays < 20; $day++) {
            $date = $startDate->copy()->addDays($day);
            if ($date->isWeekend()) continue;
            $schoolDays++;

            foreach ($this->students as $student) {
                $rand = rand(1, 100);
                $status = $rand <= 85 ? 'present' : ($rand <= 93 ? 'absent' : 'late');

                Attendance::create([
                    'school_id' => $this->schoolId,
                    'academic_year_id' => $this->academicYear->id,
                    'date' => $date->toDateString(),
                    'attendable_type' => 'App\\Models\\Student',
                    'attendable_id' => $student->id,
                    'status' => $status,
                    'remarks' => $status === 'absent' ? 'Absent without excuse' : null,
                ]);
                $count++;
            }
        }
        $this->command->line("  ✓ {$count} Attendance records (20 school days)");
    }

    private function createTimetable(): void
    {
        $timeSlots = [
            ['start' => '08:00', 'end' => '08:45'],
            ['start' => '08:50', 'end' => '09:35'],
            ['start' => '09:45', 'end' => '10:30'],
            ['start' => '10:40', 'end' => '11:25'],
            ['start' => '11:35', 'end' => '12:20'],
        ];
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $count = 0;

        $keyClasses = ['Primary 1', 'Primary 4', 'JSS 1', 'JSS 3', 'SSS 1'];
        $teacherIndex = 0;

        foreach ($keyClasses as $className) {
            if (!isset($this->classes[$className]) || !isset($this->subjects[$className])) continue;
            $class = $this->classes[$className];
            $subjects = $this->subjects[$className];
            $teacher = $this->teachers[$teacherIndex % count($this->teachers)];
            $teacherIndex++;

            foreach ($days as $day) {
                foreach ($timeSlots as $si => $slot) {
                    $subject = $subjects[$si % count($subjects)];
                    Timetable::create([
                        'school_id' => $this->schoolId,
                        'class_id' => $class->id,
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacher->id,
                        'day_of_week' => $day,
                        'start_time' => $slot['start'],
                        'end_time' => $slot['end'],
                        'room' => 'Room ' . ($class->numeric_name ?? rand(1, 16)),
                    ]);
                    $count++;
                }
            }
        }
        $this->command->line("  ✓ {$count} Timetable entries");
    }

    private function createFeesAndPayments(): void
    {
        $feeCat = FeeCategory::where('school_id', $this->schoolId)->where('type', 'tuition')->first();
        if (!$feeCat) return;

        $structures = [];
        foreach ($this->classes as $className => $class) {
            $amount = str_contains($className, 'Nursery') ? 1500000
                : (str_contains($className, 'Primary') ? 2000000
                : (str_contains($className, 'JSS') ? 2500000 : 3000000));

            $structures[$className] = FeeStructure::create([
                'school_id' => $this->schoolId,
                'class_id' => $class->id,
                'fee_category_id' => $feeCat->id,
                'academic_year' => '2025-2026',
                'amount' => $amount,
                'due_date' => '2025-10-31',
                'frequency' => 'annual',
                'is_active' => true,
            ]);
        }

        $paymentCount = 0;
        foreach ($this->students as $student) {
            $class = SchoolClass::withoutGlobalScopes()->find($student->class_id);
            if (!$class || !isset($structures[$class->name])) continue;

            $structure = $structures[$class->name];
            $amount = $structure->amount;
            $paid = rand(0, 1) ? $amount : (int) ($amount * (rand(40, 90) / 100));

            FeePayment::create([
                'school_id' => $this->schoolId,
                'student_id' => $student->id,
                'fee_structure_id' => $structure->id,
                'receipt_no' => 'FIA-RCPT-' . str_pad($student->id, 5, '0', STR_PAD_LEFT),
                'amount_due' => $amount,
                'amount_paid' => $paid,
                'discount' => 0,
                'fine' => 0,
                'payment_date' => now()->subDays(rand(1, 60))->toDateString(),
                'method' => rand(0, 1) ? 'cash' : 'mobile_money',
                'status' => $paid >= $amount ? 'paid' : 'partial',
            ]);
            $paymentCount++;
        }
        $this->command->line("  ✓ Fee Structures + {$paymentCount} Payments");
    }

    private function createPayroll(): void
    {
        $staffList = Staff::withoutGlobalScopes()->where('school_id', $this->schoolId)->where('status', 'active')->get();
        $count = 0;
        foreach ($staffList as $staff) {
            $basic = $staff->salary ?? 2500000;
            $allowances = (int) ($basic * 0.15);
            $deductions = (int) ($basic * 0.10);
            $net = $basic + $allowances - $deductions;

            Payroll::create([
                'school_id' => $this->schoolId,
                'staff_id' => $staff->id,
                'month_year' => now()->format('Y-m'),
                'basic_salary' => $basic,
                'total_allowances' => $allowances,
                'total_deductions' => $deductions,
                'net_salary' => $net,
                'working_days' => 22,
                'present_days' => rand(18, 22),
                'leave_days' => rand(0, 2),
                'status' => 'paid',
                'paid_on' => now()->subDays(5)->toDateString(),
            ]);
            $count++;
        }
        $this->command->line("  ✓ {$count} Payroll records");
    }

    private function createLibrary(): void
    {
        $books = [
            ['title' => 'Introduction to Mathematics', 'author' => 'J.K. Williams', 'isbn' => '978-001-001', 'category' => 'Textbook', 'qty' => 30],
            ['title' => 'English Grammar in Use', 'author' => 'Raymond Murphy', 'isbn' => '978-001-002', 'category' => 'Textbook', 'qty' => 25],
            ['title' => 'Chemistry for West Africa', 'author' => 'T.E. Archbold', 'isbn' => '978-001-003', 'category' => 'Textbook', 'qty' => 20],
            ['title' => 'African Short Stories', 'author' => 'Chinua Achebe', 'isbn' => '978-001-004', 'category' => 'Literature', 'qty' => 15],
            ['title' => 'The Things Fall Apart', 'author' => 'Chinua Achebe', 'isbn' => '978-001-005', 'category' => 'Literature', 'qty' => 10],
            ['title' => 'Sierra Leone History', 'author' => 'Akintola Wyse', 'isbn' => '978-001-006', 'category' => 'History', 'qty' => 12],
            ['title' => 'Computer Basics', 'author' => 'David Reynolds', 'isbn' => '978-001-007', 'category' => 'ICT', 'qty' => 18],
            ['title' => 'Physical Geography', 'author' => 'Peter Haggett', 'isbn' => '978-001-008', 'category' => 'Geography', 'qty' => 14],
            ['title' => 'Principles of Economics', 'author' => 'N. Gregory Mankiw', 'isbn' => '978-001-009', 'category' => 'Economics', 'qty' => 16],
            ['title' => 'Government of Sierra Leone', 'author' => 'Johnny Paul Koroma', 'isbn' => '978-001-010', 'category' => 'Government', 'qty' => 10],
            ['title' => 'Biology: A Global Approach', 'author' => 'Neil Campbell', 'isbn' => '978-001-011', 'category' => 'Textbook', 'qty' => 20],
            ['title' => 'My First Dictionary', 'author' => 'Oxford Press', 'isbn' => '978-001-012', 'category' => 'Dictionary', 'qty' => 25],
            ['title' => 'Advanced Mathematics', 'author' => 'S.L. Loney', 'isbn' => '978-001-013', 'category' => 'Textbook', 'qty' => 15],
            ['title' => 'West African Poetry', 'author' => 'Various Authors', 'isbn' => '978-001-014', 'category' => 'Literature', 'qty' => 12],
            ['title' => 'French for Beginners', 'author' => 'Gouadec', 'isbn' => '978-001-015', 'category' => 'Language', 'qty' => 18],
        ];

        $bookModels = [];
        foreach ($books as $b) {
            $bookModels[] = Book::create([
                'school_id' => $this->schoolId,
                'title' => $b['title'],
                'author' => $b['author'],
                'isbn' => $b['isbn'],
                'category' => $b['category'],
                'total_copies' => $b['qty'],
                'available_copies' => $b['qty'] - rand(0, 3),
                'location' => 'Rack ' . rand(1, 5),
                'is_active' => true,
            ]);
        }

        // Create some book issues
        $issueCount = 0;
        foreach (array_slice($bookModels, 0, 6) as $book) {
            $student = $this->students[array_rand($this->students)] ?? null;
            if (!$student) continue;

            BookIssue::create([
                'school_id' => $this->schoolId,
                'book_id' => $book->id,
                'member_type' => 'App\\Models\\Student',
                'member_id' => $student->id,
                'issued_date' => now()->subDays(rand(5, 30))->toDateString(),
                'due_date' => now()->addDays(rand(1, 14))->toDateString(),
                'returned_date' => rand(0, 1) ? now()->subDays(rand(1, 5))->toDateString() : null,
                'status' => rand(0, 1) ? 'returned' : 'issued',
                'fine' => 0,
            ]);
            $issueCount++;
        }
        $count = count($bookModels);
        $this->command->line("  ✓ {$count} Library Books + {$issueCount} Issues");
    }

    private function createTransport(): void
    {
        Vehicle::create([
            'school_id' => $this->schoolId,
            'registration_no' => 'SL-001-FIA',
            'name' => 'School Bus 1',
            'type' => 'bus',
            'capacity' => 45,
            'driver_name' => 'Mohamed Bangura',
            'driver_phone' => '+23278400001',
            'status' => 'active',
        ]);

        Vehicle::create([
            'school_id' => $this->schoolId,
            'registration_no' => 'SL-002-FIA',
            'name' => 'School Minibus',
            'type' => 'minibus',
            'capacity' => 14,
            'driver_name' => 'James Turay',
            'driver_phone' => '+23278400002',
            'status' => 'active',
        ]);
        $this->command->line('  ✓ 2 Vehicles');
    }

    private function createHostel(): void
    {
        $hostel = Hostel::create([
            'school_id' => $this->schoolId,
            'name' => 'FIA Boarding House',
            'type' => 'boys',
            'address' => '25 Lumley Beach Road, Freetown',
            'total_rooms' => 10,
            'total_capacity' => 30,
            'status' => 'active',
        ]);

        $rooms = [];
        for ($i = 1; $i <= 10; $i++) {
            $rooms[] = HostelRoom::create([
                'school_id' => $this->schoolId,
                'hostel_id' => $hostel->id,
                'room_no' => 'Room ' . $i,
                'type' => $i <= 2 ? 'single' : 'double',
                'capacity' => $i <= 2 ? 1 : 4,
                'occupied' => $i <= 4 ? rand(1, 3) : 0,
                'monthly_fee' => $i <= 2 ? 1500000 : 800000,
                'status' => 'available',
            ]);
        }

        // Allocate 3 students
        for ($i = 0; $i < min(3, count($this->students)); $i++) {
            HostelAllocation::create([
                'school_id' => $this->schoolId,
                'hostel_id' => $hostel->id,
                'room_id' => $rooms[$i % count($rooms)]->id,
                'student_id' => $this->students[$i]->id,
                'joining_date' => now()->subDays(rand(10, 60))->toDateString(),
                'status' => 'active',
            ]);
        }
        $this->command->line('  ✓ 1 Hostel + 10 Rooms + 3 Allocations');
    }

    private function createInventory(): void
    {
        $categories = [
            ['name' => 'Stationery', 'items' => [
                ['name' => 'Exercise Books (500 pages)', 'qty' => 500, 'price' => 15000, 'reorder' => 100],
                ['name' => 'Bic Pens (Box of 50)', 'qty' => 50, 'price' => 75000, 'reorder' => 20],
                ['name' => 'Pencils (Box of 24)', 'qty' => 30, 'price' => 45000, 'reorder' => 15],
                ['name' => 'Rulers (30cm)', 'qty' => 100, 'price' => 5000, 'reorder' => 30],
            ]],
            ['name' => 'Classroom Supplies', 'items' => [
                ['name' => 'Whiteboard Markers (Box)', 'qty' => 20, 'price' => 35000, 'reorder' => 10],
                ['name' => 'Chalk (Box of 100)', 'qty' => 15, 'price' => 25000, 'reorder' => 5],
                ['name' => 'Projector Bulbs', 'qty' => 5, 'price' => 250000, 'reorder' => 2],
                ['name' => 'Staplers', 'qty' => 10, 'price' => 15000, 'reorder' => 3],
            ]],
            ['name' => 'Office Supplies', 'items' => [
                ['name' => 'A4 Paper (Ream)', 'qty' => 40, 'price' => 85000, 'reorder' => 15],
                ['name' => 'Printer Toner', 'qty' => 8, 'price' => 180000, 'reorder' => 3],
                ['name' => 'File Folders', 'qty' => 100, 'price' => 3000, 'reorder' => 30],
                ['name' => 'Binders', 'qty' => 30, 'price' => 12000, 'reorder' => 10],
            ]],
        ];

        $count = 0;
        foreach ($categories as $cat) {
            $catModel = InventoryCategory::create([
                'school_id' => $this->schoolId,
                'name' => $cat['name'],
                'description' => $cat['name'] . ' for school operations',
            ]);
            foreach ($cat['items'] as $item) {
                InventoryItem::create([
                    'school_id' => $this->schoolId,
                    'category_id' => $catModel->id,
                    'name' => $item['name'],
                    'unit' => 'piece',
                    'current_stock' => $item['qty'],
                    'minimum_stock' => $item['reorder'],
                    'is_active' => true,
                ]);
                $count++;
            }
        }
        $this->command->line("  ✓ 3 Inventory Categories + {$count} Items");
    }

    private function createCommunication(): void
    {
        $authorId = User::where('school_id', $this->schoolId)->where('email', 'admin@freetownacademy.edu')->first()?->id;

        $announcements = [
            ['title' => 'Welcome Back - Term 1', 'body' => 'Welcome back to Freetown International Academy for the 2025-2026 academic year. School resumes on Monday, September 8th.'],
            ['title' => 'Mid-Term Break Notice', 'body' => 'Mid-term break will commence from October 20th to October 24th, 2025. Classes resume on October 27th.'],
            ['title' => 'Parent-Teacher Conference', 'body' => 'A parent-teacher conference is scheduled for November 15th, 2025. All parents are expected to attend.'],
            ['title' => 'Sports Day', 'body' => 'Annual sports day will hold on December 1st, 2025. All students should come in their sports attire.'],
            ['title' => 'First Term Examination', 'body' => 'First term examinations commence on November 24th and end on December 5th, 2025. Students are advised to prepare adequately.'],
        ];

        foreach ($announcements as $a) {
            Announcement::create([
                'school_id' => $this->schoolId,
                'title' => $a['title'],
                'body' => $a['body'],
                'author_id' => $authorId,
                'published_at' => now()->subDays(rand(1, 30)),
            ]);
        }

        $messages = [
            ['subject' => 'Fee Payment Reminder', 'body' => 'Dear Parent, this is a reminder that fee payment for Term 1 is due. Please ensure payment is made before the deadline.'],
            ['subject' => 'School Calendar Update', 'body' => 'Please note that the school calendar has been updated. Check the school portal for the latest schedule.'],
            ['subject' => 'Examination Guidelines', 'body' => 'Students are reminded to follow all examination guidelines. Mobile phones are strictly prohibited in exam halls.'],
            ['subject' => 'Welcome New Students', 'body' => 'We extend a warm welcome to all newly admitted students. We wish you a productive academic year.'],
            ['subject' => 'Staff Meeting Notice', 'body' => 'All staff members are required to attend a staff meeting on Friday at 2:00 PM in the conference room.'],
        ];

        $adminUser = User::where('school_id', $this->schoolId)->where('email', 'admin@freetownacademy.edu')->first();
        $firstParent = User::where('school_id', $this->schoolId)->where('email', 'ibrahim.parent1@freetownacademy.edu')->first();
        $recipientId = $firstParent?->id ?? $adminUser?->id;

        foreach ($messages as $m) {
            Message::create([
                'school_id' => $this->schoolId,
                'sender_id' => $authorId,
                'recipient_id' => $recipientId,
                'subject' => $m['subject'],
                'body' => $m['body'],
            ]);
        }

        $this->command->line('  ✓ 5 Announcements + 5 Messages');
    }

    private function createHolidays(): void
    {
        $holidays = [
            ['name' => "New Year's Day", 'date' => '2026-01-01', 'description' => 'National holiday'],
            ['name' => 'National Independence Day', 'date' => '2026-04-27', 'description' => 'Republic Day / Independence Day'],
            ['name' => 'Good Friday', 'date' => '2026-04-03', 'description' => 'Religious holiday'],
            ['name' => 'Eid-ul-Fitr', 'date' => '2026-03-30', 'description' => 'Religious holiday'],
            ['name' => 'Christmas Day', 'date' => '2026-12-25', 'description' => 'Religious holiday'],
        ];

        foreach ($holidays as $h) {
            Holiday::create(array_merge($h, ['school_id' => $this->schoolId]));
        }
        $this->command->line('  ✓ 5 Holidays');
    }
}
