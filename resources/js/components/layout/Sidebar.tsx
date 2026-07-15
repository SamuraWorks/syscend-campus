import { Link, usePage } from '@inertiajs/react';
import {
    LayoutDashboard, School, Users, GraduationCap, UserCog,
    CalendarDays, BookOpen, ClipboardList, DollarSign,
    Library, Bus, Home, Package, MessageSquare, BarChart3,
    Settings, ChevronLeft, ChevronRight, Layers, Clock, CalendarOff,
    Building2, BadgeCheck, NotebookPen, Video, Megaphone, Mail, Send, Bell,
    PieChart, FileText, TrendingUp, Wrench, ShieldCheck, Plug,
    CreditCard, Tag, Wallet, Receipt, FileSpreadsheet, AlertTriangle, Landmark,
    Brain, Trophy, AlertCircle, Target, Eye, Sparkles, Crown, Stethoscope,
    Fingerprint, CalendarClock, RotateCcw,
} from 'lucide-react';
import { cn } from '@/lib/utils';
import { useUIStore } from '@/Stores/useUIStore';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import type { PageProps } from '@/Types';

interface NavItem {
    label: string;
    href: string;
    icon: React.ElementType;
    roles?: string[];
    exact?: boolean;
}

interface NavGroup {
    title: string;
    items: NavItem[];
}

const navGroups: NavGroup[] = [
    // ═══════════════════════════════════════════════════════════════
    // SUPER ADMIN PORTAL — Platform Command Center
    // ═══════════════════════════════════════════════════════════════
    {
        title: 'Executive',
        items: [
            { label: 'Executive Dashboard', href: '/super-admin/dashboard', icon: LayoutDashboard, roles: ['super-admin'], exact: true },
        ],
    },
    {
        title: 'School Management',
        items: [
            { label: 'Schools', href: '/super-admin/schools', icon: School, roles: ['super-admin'] },
            { label: 'Demo Requests', href: '/super-admin/demo-requests', icon: CalendarClock, roles: ['super-admin'] },
        ],
    },
    {
        title: 'Subscriptions & Billing',
        items: [
            { label: 'Packages',       href: '/super-admin/packages',       icon: Package,     roles: ['super-admin'] },
            { label: 'Subscriptions',  href: '/super-admin/subscriptions',  icon: CreditCard,  roles: ['super-admin'] },
            { label: 'Coupons',        href: '/super-admin/coupons',        icon: Tag,         roles: ['super-admin'] },
            { label: 'Module Manager', href: '/super-admin/module-manager', icon: Layers,      roles: ['super-admin'] },
        ],
    },
    {
        title: 'Users & Access',
        items: [
            { label: 'All Users', href: '/super-admin/users', icon: Users, roles: ['super-admin'] },
        ],
    },
    {
        title: 'Platform Management',
        items: [
            { label: 'Platform Settings', href: '/super-admin/settings',         icon: Settings, roles: ['super-admin'] },
            { label: 'Integrations',      href: '/school/settings/integrations', icon: Plug,     roles: ['super-admin'] },
            { label: 'Demo Reset',        href: '/super-admin/demo-reset',       icon: RotateCcw, roles: ['super-admin'] },
        ],
    },
    {
        title: 'Platform Analytics',
        items: [
            { label: 'Performance Analytics', href: '/super-admin/performance', icon: Sparkles, roles: ['super-admin'] },
        ],
    },
    {
        title: 'Support & Communication',
        items: [
            { label: 'Announcements',   href: '/school/communication/announcements',   icon: Megaphone,     roles: ['super-admin'] },
            { label: 'Messages',        href: '/school/communication/messages',         icon: MessageSquare, roles: ['super-admin'] },
            { label: 'Notifications',   href: '/school/communication/notifications',   icon: Bell,          roles: ['super-admin'] },
            { label: 'Email Templates', href: '/school/communication/email-templates', icon: Mail,          roles: ['super-admin'] },
            { label: 'SMS/Email Blast', href: '/school/communication/blast',           icon: Send,          roles: ['super-admin'] },
        ],
    },
    {
        title: 'Reports & Audit',
        items: [
            { label: 'Audit Log', href: '/school/reports/audit-log', icon: ShieldCheck, roles: ['super-admin'] },
        ],
    },

    // ═══════════════════════════════════════════════════════════════
    // MINISTRY OF EDUCATION PORTAL — National Oversight
    // ═══════════════════════════════════════════════════════════════
    {
        title: 'Executive',
        items: [
            { label: 'National Dashboard', href: '/ministry/dashboard', icon: LayoutDashboard, roles: ['ministry-admin', 'district-officer'], exact: true },
        ],
    },
    {
        title: 'Schools',
        items: [
            { label: 'National Registry', href: '/ministry/schools', icon: School, roles: ['ministry-admin', 'district-officer'] },
            { label: 'Pending Approvals', href: '/ministry/schools/approvals', icon: ClipboardList, roles: ['ministry-admin'] },
        ],
    },
    {
        title: 'Districts',
        items: [
            { label: 'Districts', href: '/ministry/districts', icon: Building2, roles: ['ministry-admin'] },
            { label: 'District Officers', href: '/ministry/districts/officers', icon: Users, roles: ['ministry-admin'] },
        ],
    },
    {
        title: 'Student Registry',
        items: [
            { label: 'National Students', href: '/ministry/students', icon: GraduationCap, roles: ['ministry-admin', 'district-officer'] },
            { label: 'Enrollment Analytics', href: '/ministry/students/analytics', icon: BarChart3, roles: ['ministry-admin'] },
        ],
    },
    {
        title: 'Teacher Registry',
        items: [
            { label: 'National Teachers', href: '/ministry/teachers', icon: UserCog, roles: ['ministry-admin', 'district-officer'] },
            { label: 'Licensing', href: '/ministry/teachers/licensing', icon: BadgeCheck, roles: ['ministry-admin'] },
        ],
    },
    {
        title: 'National Examinations',
        items: [
            { label: 'NPSE', href: '/ministry/exams/npse', icon: FileText, roles: ['ministry-admin', 'district-officer'] },
            { label: 'BECE', href: '/ministry/exams/bece', icon: FileText, roles: ['ministry-admin', 'district-officer'] },
            { label: 'WASSCE', href: '/ministry/exams/wasse', icon: FileText, roles: ['ministry-admin', 'district-officer'] },
            { label: 'Exam Analytics', href: '/ministry/exams/analytics', icon: BarChart3, roles: ['ministry-admin'] },
        ],
    },
    {
        title: 'Quality Assurance',
        items: [
            { label: 'Inspections', href: '/ministry/inspections', icon: ShieldCheck, roles: ['ministry-admin', 'district-officer'] },
            { label: 'Compliance', href: '/ministry/inspections/compliance', icon: ClipboardList, roles: ['ministry-admin'] },
            { label: 'School Ratings', href: '/ministry/inspections/ratings', icon: ShieldCheck, roles: ['ministry-admin'] },
        ],
    },
    {
        title: 'Reports',
        items: [
            { label: 'National Overview', href: '/ministry/reports', icon: PieChart, roles: ['ministry-admin', 'district-officer'] },
            { label: 'District Reports', href: '/ministry/reports/districts', icon: Building2, roles: ['ministry-admin'] },
            { label: 'Enrollment', href: '/ministry/reports/enrollment', icon: GraduationCap, roles: ['ministry-admin'] },
            { label: 'Gender Reports', href: '/ministry/reports/gender', icon: TrendingUp, roles: ['ministry-admin'] },
        ],
    },
    {
        title: 'Communication',
        items: [
            { label: 'Announcements', href: '/ministry/communication/announcements', icon: Megaphone, roles: ['ministry-admin'] },
            { label: 'Circulars', href: '/ministry/communication/circulars', icon: Mail, roles: ['ministry-admin'] },
            { label: 'Emergency Alerts', href: '/ministry/communication/alerts', icon: Bell, roles: ['ministry-admin'] },
        ],
    },
    {
        title: 'Downloads',
        items: [
            { label: 'Documents', href: '/ministry/downloads', icon: Layers, roles: ['ministry-admin', 'district-officer'] },
        ],
    },
    {
        title: 'Administration',
        items: [
            { label: 'Ministry Users', href: '/ministry/admin/users', icon: Users, roles: ['ministry-admin'] },
            { label: 'Roles & Permissions', href: '/ministry/admin/roles', icon: Settings, roles: ['ministry-admin'] },
            { label: 'Audit Log', href: '/ministry/admin/audit', icon: ShieldCheck, roles: ['ministry-admin'] },
            { label: 'Settings', href: '/ministry/admin/settings', icon: Settings, roles: ['ministry-admin'] },
        ],
    },

    // ═══════════════════════════════════════════════════════════════
    // PRINCIPAL / HEADTEACHER PORTAL
    // ═══════════════════════════════════════════════════════════════
    {
        title: 'Executive',
        items: [
            { label: 'Executive Dashboard', href: '/school/principal/dashboard', icon: Crown, roles: ['principal'], exact: true },
        ],
    },
    {
        title: 'School',
        items: [
            { label: 'School Identity', href: '/school/school-identity', icon: Fingerprint, roles: ['principal'] },
            { label: 'Classes',   href: '/school/principal/students',  icon: GraduationCap, roles: ['principal'] },
            { label: 'Holidays',  href: '/school/holidays',            icon: CalendarOff,  roles: ['principal'] },
        ],
    },
    {
        title: 'People',
        items: [
            { label: 'Students', href: '/school/principal/students', icon: GraduationCap, roles: ['principal'] },
            { label: 'Staff',    href: '/school/principal/staff',    icon: Users,         roles: ['principal'] },
        ],
    },
    {
        title: 'Academics',
        items: [
            { label: 'Timetable',    href: '/school/principal/timetable',  icon: CalendarDays,  roles: ['principal'] },
            { label: 'Attendance',   href: '/school/principal/attendance', icon: ClipboardList, roles: ['principal'] },
            { label: 'Examinations', href: '/school/principal/exams',      icon: BookOpen,      roles: ['principal'] },
            { label: 'Results',      href: '/school/principal/results',    icon: BarChart3,     roles: ['principal'] },
            { label: 'Report Cards', href: '/school/report-cards',         icon: FileText,      roles: ['principal'] },
        ],
    },
    {
        title: 'Finance',
        items: [
            { label: 'Fee Collection', href: '/school/principal/fees',  icon: DollarSign, roles: ['principal'] },
            { label: 'Fee Payments',   href: '/school/fees/payments',   icon: Receipt,    roles: ['principal'] },
        ],
    },
    {
        title: 'Operations',
        items: [
            { label: 'Inquiries', href: '/school/admissions/inquiries', icon: ClipboardList, roles: ['principal'] },
            { label: 'Library',   href: '/school/library/books',        icon: Library,       roles: ['principal'] },
        ],
    },
    {
        title: 'Communication',
        items: [
            { label: 'Announcements', href: '/school/principal/announcements',         icon: Megaphone,     roles: ['principal'] },
            { label: 'Messages',      href: '/school/principal/messages',              icon: MessageSquare, roles: ['principal'] },
            { label: 'Notifications', href: '/school/communication/notifications',     icon: Bell,          roles: ['principal'] },
        ],
    },
    {
        title: 'Reports',
        items: [
            { label: 'Overview',         href: '/school/principal/reports',  icon: PieChart,      roles: ['principal'] },
            { label: 'Attendance Reports', href: '/school/reports/attendance', icon: ClipboardList, roles: ['principal'] },
            { label: 'Academic Reports',   href: '/school/reports/academic',   icon: TrendingUp,    roles: ['principal'] },
            { label: 'Custom Reports',     href: '/school/reports/custom',     icon: FileText,      roles: ['principal'] },
        ],
    },
    {
        title: 'My Account',
        items: [
            { label: 'Downloads', href: '/school/principal/downloads', icon: FileText, roles: ['principal'] },
            { label: 'Profile',   href: '/school/principal/profile',   icon: UserCog,  roles: ['principal'] },
        ],
    },

    // ═══════════════════════════════════════════════════════════════
    // SCHOOL ADMIN PORTAL — Operational Management
    // ═══════════════════════════════════════════════════════════════
    {
        title: 'Dashboard',
        items: [
            { label: 'Dashboard', href: '/school/reports/dashboard', icon: LayoutDashboard, roles: ['school-admin'] },
        ],
    },
    {
        title: 'School',
        items: [
            { label: 'School Identity', href: '/school/school-identity', icon: Fingerprint, roles: ['school-admin'] },
            { label: 'Classes',   href: '/school/classes',  icon: GraduationCap, roles: ['school-admin'] },
            { label: 'Sections',  href: '/school/sections', icon: Layers,        roles: ['school-admin'] },
            { label: 'Subjects',  href: '/school/subjects', icon: BookOpen,      roles: ['school-admin'] },
            { label: 'Shifts',    href: '/school/shifts',   icon: Clock,         roles: ['school-admin'] },
            { label: 'Holidays',  href: '/school/holidays', icon: CalendarOff,   roles: ['school-admin'] },
        ],
    },
    {
        title: 'People',
        items: [
            { label: 'Students',        href: '/school/students',    icon: GraduationCap, roles: ['school-admin','receptionist'] },
            { label: 'Staff',           href: '/school/staff',       icon: UserCog,       roles: ['school-admin'] },
            { label: 'Departments',     href: '/school/departments', icon: Building2,     roles: ['school-admin'] },
            { label: 'Designations',    href: '/school/designations',icon: BadgeCheck,    roles: ['school-admin'] },
            { label: 'Leave Requests',  href: '/school/hr/leaves',   icon: CalendarDays,  roles: ['school-admin'] },
            { label: 'Manage Users',    href: '/school/settings/admins', icon: UserCog,   roles: ['school-admin'] },
        ],
    },
    {
        title: 'Academics',
        items: [
            { label: 'Timetable',         href: '/school/timetable',   icon: CalendarDays,    roles: ['school-admin','teacher'] },
            { label: 'Attendance',        href: '/school/attendance',  icon: ClipboardList,   roles: ['school-admin','teacher'] },
            { label: 'Examinations',      href: '/school/exams',       icon: BookOpen,        roles: ['school-admin','teacher'] },
            { label: 'Assessment Config', href: '/school/assessment-config', icon: Settings, roles: ['school-admin'] },
            { label: 'Report Cards',      href: '/school/report-cards',icon: FileText,        roles: ['school-admin','accountant'] },
            { label: 'Result Approvals',  href: '/school/approvals',   icon: ClipboardList,   roles: ['school-admin'] },
        ],
    },
    {
        title: 'Teaching & Learning',
        items: [
            { label: 'Homework',           href: '/school/homework',                icon: NotebookPen,   roles: ['school-admin','teacher'] },
            { label: 'Lesson Plans',       href: '/school/homework/lesson-plans',   icon: ClipboardList, roles: ['school-admin','teacher'] },
            { label: 'Syllabus',           href: '/school/homework/syllabi',        icon: BookOpen,      roles: ['school-admin','teacher'] },
            { label: 'Online Classes',     href: '/school/homework/online-classes', icon: Video,         roles: ['school-admin','teacher'] },
            { label: 'AI Result Imports',  href: '/school/imports',                 icon: FileSpreadsheet, roles: ['school-admin'] },
        ],
    },
    {
        title: 'Finance',
        items: [
            { label: 'Fee Payments',   href: '/school/fees/payments',   icon: DollarSign,    roles: ['school-admin'] },
            { label: 'Fee Structures', href: '/school/fees/structures', icon: BarChart3,     roles: ['school-admin'] },
            { label: 'Fee Categories', href: '/school/fees/categories', icon: ClipboardList, roles: ['school-admin'] },
            { label: 'Payroll',        href: '/school/hr/payroll',      icon: Wallet,        roles: ['school-admin','accountant'] },
        ],
    },
    {
        title: 'Operations',
        items: [
            { label: 'Admissions', href: '/school/admissions/inquiries', icon: ClipboardList, roles: ['school-admin','receptionist'] },
            { label: 'Visitors',   href: '/school/admissions/visitors',  icon: Users,         roles: ['school-admin','receptionist'] },
            { label: 'Library',    href: '/school/library/books',        icon: Library,       roles: ['school-admin','librarian'] },
            { label: 'Transport',  href: '/school/transport/vehicles',   icon: Bus,           roles: ['school-admin','driver'] },
            { label: 'Hostel',     href: '/school/hostel',               icon: Home,          roles: ['school-admin','warden'] },
            { label: 'Inventory',  href: '/school/inventory/items',      icon: Package,       roles: ['school-admin','store-manager'] },
        ],
    },
    {
        title: 'Communication',
        items: [
            { label: 'Announcements',   href: '/school/communication/announcements',   icon: Megaphone,     roles: ['school-admin','teacher'] },
            { label: 'Messages',        href: '/school/communication/messages',         icon: MessageSquare, roles: ['school-admin','teacher','accountant'] },
            { label: 'SMS/Email Blast', href: '/school/communication/blast',            icon: Send,          roles: ['school-admin'] },
            { label: 'Email Templates', href: '/school/communication/email-templates',  icon: Mail,          roles: ['school-admin'] },
            { label: 'Notifications',   href: '/school/communication/notifications',    icon: Bell,          roles: ['school-admin','teacher','accountant'] },
        ],
    },
    {
        title: 'Reports',
        items: [
            { label: 'Dashboard Reports', href: '/school/reports/dashboard',  icon: PieChart,      roles: ['school-admin','teacher','accountant'] },
            { label: 'Attendance Reports', href: '/school/reports/attendance', icon: ClipboardList, roles: ['school-admin','teacher'] },
            { label: 'Academic Reports',   href: '/school/reports/academic',   icon: TrendingUp,    roles: ['school-admin','teacher'] },
            { label: 'Finance Reports',    href: '/school/reports/finance',    icon: DollarSign,    roles: ['school-admin','accountant'] },
            { label: 'Custom Reports',     href: '/school/reports/custom',     icon: FileText,      roles: ['school-admin','accountant'] },
            { label: 'Audit Log',          href: '/school/reports/audit-log',  icon: ShieldCheck,   roles: ['school-admin'] },
        ],
    },
    {
        title: 'Administration',
        items: [
            { label: 'Settings',     href: '/school/settings',              icon: Settings, roles: ['school-admin'] },
            { label: 'Integrations', href: '/school/settings/integrations', icon: Plug,     roles: ['school-admin'] },
        ],
    },

    // ═══════════════════════════════════════════════════════════════
    // STUDENT PORTAL
    // ═══════════════════════════════════════════════════════════════
    {
        title: 'My Dashboard',
        items: [
            { label: 'My Dashboard', href: '/school/student/dashboard', icon: LayoutDashboard, roles: ['student'] },
        ],
    },
    {
        title: 'My Academic',
        items: [
            { label: 'My Timetable',  href: '/school/student/timetable',  icon: CalendarDays,  roles: ['student'] },
            { label: 'My Attendance', href: '/school/student/attendance', icon: ClipboardList, roles: ['student'] },
            { label: 'My Results',    href: '/school/student/results',    icon: BarChart3,     roles: ['student'] },
            { label: 'Report Cards',  href: '/school/student/report-cards', icon: FileText,   roles: ['student'] },
            { label: 'My Homework',   href: '/school/student/homework',   icon: NotebookPen,   roles: ['student'] },
        ],
    },
    {
        title: 'My Finance',
        items: [
            { label: 'My Fees', href: '/school/student/fees', icon: DollarSign, roles: ['student'] },
        ],
    },
    {
        title: 'My Performance',
        items: [
            { label: 'Performance',  href: '/school/student/performance',  icon: Brain,  roles: ['student'] },
            { label: 'Goals',        href: '/school/student/goals',        icon: Target, roles: ['student'] },
            { label: 'Achievements', href: '/school/student/achievements', icon: Trophy, roles: ['student'] },
        ],
    },
    {
        title: 'School Info',
        items: [
            { label: 'Announcements', href: '/school/student/announcements', icon: Megaphone, roles: ['student'] },
        ],
    },

    // ═══════════════════════════════════════════════════════════════
    // PARENT PORTAL
    // ═══════════════════════════════════════════════════════════════
    {
        title: 'My Dashboard',
        items: [
            { label: 'My Dashboard', href: '/school/parent/dashboard', icon: LayoutDashboard, roles: ['parent'] },
        ],
    },
    {
        title: 'My Children',
        items: [
            { label: 'Attendance', href: '/school/parent/attendance', icon: ClipboardList, roles: ['parent'] },
            { label: 'Results',    href: '/school/parent/results',    icon: BarChart3,     roles: ['parent'] },
            { label: 'Report Cards', href: '/school/parent/report-cards', icon: FileText, roles: ['parent'] },
            { label: 'Fee Status', href: '/school/parent/fees',       icon: DollarSign,    roles: ['parent'] },
        ],
    },
    {
        title: "Child's Performance",
        items: [
            { label: 'Performance', href: '/school/parent/performance', icon: Brain, roles: ['parent'] },
        ],
    },
    {
        title: 'School Info',
        items: [
            { label: 'Announcements', href: '/school/parent/announcements', icon: Megaphone, roles: ['parent'] },
        ],
    },

    // ═══════════════════════════════════════════════════════════════
    // TEACHER PORTAL — Enterprise Teaching Workspace
    // ═══════════════════════════════════════════════════════════════
    {
        title: 'Dashboard',
        items: [
            { label: 'My Dashboard', href: '/school/teacher/dashboard', icon: LayoutDashboard, roles: ['teacher'], exact: true },
        ],
    },
    {
        title: 'Core Work',
        items: [
            { label: 'My Subjects',     href: '/school/teacher/academic',    icon: BookOpen,      roles: ['teacher'] },
            { label: 'Timetable',       href: '/school/teacher/timetable',   icon: CalendarDays,  roles: ['teacher'] },
            { label: 'Students',        href: '/school/teacher/students',    icon: GraduationCap, roles: ['teacher'] },
            { label: 'Lesson Plans',    href: '/school/teacher/lesson-plans',icon: ClipboardList, roles: ['teacher'] },
            { label: 'Syllabus',        href: '/school/teacher/syllabus',    icon: NotebookPen,   roles: ['teacher'] },
            { label: 'Homework',        href: '/school/teacher/homework',    icon: FileText,      roles: ['teacher'] },
            { label: 'Online Learning', href: '/school/teacher/online-classes', icon: Video,      roles: ['teacher'] },
        ],
    },
    {
        title: 'Assessment & Grades',
        items: [
            { label: 'Attendance',         href: '/school/teacher/attendance',  icon: ClipboardList, roles: ['teacher'] },
            { label: 'Examinations',       href: '/school/teacher/exams',       icon: BookOpen,      roles: ['teacher'] },
            { label: 'Grade Entry',        href: '/school/teacher/exams',       icon: BarChart3,     roles: ['teacher'] },
            { label: 'Student Performance',href: '/school/teacher/performance', icon: Brain,         roles: ['teacher'] },
        ],
    },
    {
        title: 'Reports & Analytics',
        items: [
            { label: 'Teaching Reports',   href: '/school/teacher/reports',        icon: PieChart,   roles: ['teacher'] },
            { label: 'Class Performance',  href: '/school/teacher/performance/class', icon: TrendingUp, roles: ['teacher'] },
        ],
    },
    {
        title: 'Communication',
        items: [
            { label: 'Announcements', href: '/school/teacher/announcements', icon: Megaphone,     roles: ['teacher'] },
            { label: 'Messages',      href: '/school/teacher/messages',      icon: MessageSquare, roles: ['teacher'] },
            { label: 'Notifications', href: '/school/teacher/notifications', icon: Bell,          roles: ['teacher'] },
        ],
    },
    {
        title: 'Resources & Account',
        items: [
            { label: 'Downloads', href: '/school/teacher/downloads', icon: FileText, roles: ['teacher'] },
            { label: 'Profile',   href: '/school/teacher/profile',   icon: UserCog,  roles: ['teacher'] },
        ],
    },

    // ═══════════════════════════════════════════════════════════════
    // ACCOUNTANT PORTAL — Enterprise Finance Workspace
    // ═══════════════════════════════════════════════════════════════
    {
        title: 'Dashboard',
        items: [
            { label: 'My Dashboard', href: '/school/accountant/dashboard', icon: LayoutDashboard, roles: ['accountant'], exact: true },
        ],
    },
    {
        title: 'Finance',
        items: [
            { label: 'Fee Payments',     href: '/school/accountant/fees',           icon: DollarSign,    roles: ['accountant'] },
            { label: 'Fee Structures',   href: '/school/accountant/fee-structure',  icon: BarChart3,     roles: ['accountant'] },
            { label: 'Collections',      href: '/school/accountant/fee-collections',icon: Receipt,       roles: ['accountant'] },
            { label: 'Outstanding Fees', href: '/school/accountant/outstanding',    icon: AlertTriangle, roles: ['accountant'] },
        ],
    },
    {
        title: 'Payroll & Expenses',
        items: [
            { label: 'Payroll',  href: '/school/accountant/payroll',  icon: Wallet,          roles: ['accountant'] },
            { label: 'Expenses', href: '/school/accountant/expenses', icon: FileSpreadsheet, roles: ['accountant'] },
        ],
    },
    {
        title: 'Academic Overview',
        items: [
            { label: 'Academic Overview', href: '/school/accountant/performance', icon: Eye, roles: ['accountant'] },
        ],
    },
    {
        title: 'Reports & Analytics',
        items: [
            { label: 'Financial Reports', href: '/school/accountant/reports', icon: PieChart, roles: ['accountant'] },
        ],
    },
    {
        title: 'Communication',
        items: [
            { label: 'Announcements', href: '/school/accountant/announcements', icon: Megaphone,     roles: ['accountant'] },
            { label: 'Messages',      href: '/school/accountant/messages',      icon: MessageSquare, roles: ['accountant'] },
            { label: 'Notifications', href: '/school/accountant/notifications', icon: Bell,          roles: ['accountant'] },
        ],
    },
    {
        title: 'Resources & Account',
        items: [
            { label: 'Downloads', href: '/school/accountant/downloads', icon: FileText, roles: ['accountant'] },
            { label: 'Profile',   href: '/school/accountant/profile',   icon: UserCog,  roles: ['accountant'] },
        ],
    },

    // ═══════════════════════════════════════════════════════════════
    // PROPRIETOR PORTAL
    // ═══════════════════════════════════════════════════════════════
    {
        title: 'My Dashboard',
        items: [
            { label: 'My Dashboard', href: '/school/proprietor/dashboard', icon: LayoutDashboard, roles: ['proprietor'] },
        ],
    },
    {
        title: 'Proprietor Overview',
        items: [
            { label: 'Financial', href: '/school/proprietor/financial', icon: Landmark,      roles: ['proprietor'] },
            { label: 'Academic',  href: '/school/proprietor/academic',  icon: BookOpen,      roles: ['proprietor'] },
            { label: 'Staff',     href: '/school/proprietor/staff',     icon: Users,         roles: ['proprietor'] },
            { label: 'Students',  href: '/school/proprietor/students',  icon: GraduationCap, roles: ['proprietor'] },
        ],
    },
    {
        title: 'Academic Intelligence',
        items: [
            { label: 'Academic Overview', href: '/school/proprietor/performance', icon: Eye, roles: ['proprietor'] },
        ],
    },
    {
        title: 'Proprietor Comms',
        items: [
            { label: 'Reports',       href: '/school/proprietor/reports',       icon: PieChart,      roles: ['proprietor'] },
            { label: 'Announcements', href: '/school/proprietor/announcements', icon: Megaphone,     roles: ['proprietor'] },
            { label: 'Messages',      href: '/school/proprietor/messages',      icon: MessageSquare, roles: ['proprietor'] },
            { label: 'School Info',   href: '/school/proprietor/school-info',   icon: School,        roles: ['proprietor'] },
            { label: 'Downloads',     href: '/school/proprietor/downloads',     icon: FileText,      roles: ['proprietor'] },
            { label: 'Profile',       href: '/school/proprietor/profile',       icon: UserCog,       roles: ['proprietor'] },
        ],
    },
];

function NavLink({ item, collapsed }: { item: NavItem; collapsed: boolean }) {
    const { url } = usePage();
    const isActive = item.exact ? url === item.href : url.startsWith(item.href);

    const link = (
        <Link
            href={item.href}
            onClick={() => {
                if (window.innerWidth < 768) {
                    useUIStore.getState().setSidebarOpen(false);
                }
            }}
            className={cn(
                'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150',
                'hover:bg-sidebar-accent hover:text-sidebar-accent-foreground',
                isActive
                    ? 'bg-primary/10 text-primary'
                    : 'text-sidebar-foreground',
                collapsed && 'justify-center px-2',
            )}
        >
            <item.icon className={cn('shrink-0', isActive ? 'text-primary' : 'text-muted-foreground', 'w-[18px] h-[18px]')} />
            {!collapsed && <span className="truncate">{item.label}</span>}
        </Link>
    );

    if (collapsed) {
        return (
            <Tooltip>
                <TooltipTrigger asChild>{link}</TooltipTrigger>
                <TooltipContent side="right">{item.label}</TooltipContent>
            </Tooltip>
        );
    }
    return link;
}

export default function Sidebar() {
    const { auth, schoolBranding } = usePage<PageProps>().props;
    const { sidebarCollapsed, toggleCollapsed } = useUIStore();
    const role = auth.user?.role ?? '';
    const branding = schoolBranding as Record<string, any> | null;

    const filteredGroups = navGroups
        .map((group) => ({
            ...group,
            items: group.items.filter((item) =>
                !item.roles || item.roles.includes(role),
            ),
        }))
        .filter((group) => group.items.length > 0);

    return (
        <TooltipProvider delayDuration={0}>
            <aside
                className={cn(
                    'flex flex-col border-r border-border bg-card transition-all duration-200 shrink-0 h-full',
                    'fixed inset-y-0 left-0 z-50 md:relative md:translate-x-0',
                    sidebarCollapsed ? 'md:w-[60px] w-60' : 'w-60',
                    'md:transform-none',
                )}
            >
                {/* Logo */}
                <div className={cn('flex items-center h-16 px-4 border-b border-border', sidebarCollapsed && 'justify-center px-2')}>
                    <div className="flex items-center justify-center w-9 h-9 rounded-lg overflow-hidden shrink-0">
                        {branding?.logo_url
                            ? <img src={branding.logo_url} alt={branding.name ?? 'School'} className="size-9 object-cover" />
                            : <img src="/images/logo.jpeg" alt="Syscend Campus" className="size-9 object-cover" />}
                    </div>
                    {!sidebarCollapsed && (
                        <span className="ml-2.5 font-bold text-foreground text-base tracking-tight truncate">
                            {branding?.short_name || branding?.name || 'Syscend Campus'}
                        </span>
                    )}
                </div>

                {/* Nav */}
                <nav className="flex-1 overflow-y-auto py-3 px-2 space-y-4">
                    {filteredGroups.map((group) => (
                        <div key={group.title}>
                            {!sidebarCollapsed && (
                                <p className="px-3 mb-1 text-[10px] font-semibold uppercase tracking-widest text-muted-foreground">
                                    {group.title}
                                </p>
                            )}
                            <div className="space-y-0.5">
                                {group.items.map((item) => (
                                    <NavLink key={item.href} item={item} collapsed={sidebarCollapsed} />
                                ))}
                            </div>
                        </div>
                    ))}
                </nav>

                {/* Collapse toggle */}
                <button
                    onClick={toggleCollapsed}
                    className="absolute -right-3 top-[1.625rem] flex items-center justify-center w-6 h-6 rounded-full border border-border bg-card shadow-sm hover:bg-accent transition-colors z-10"
                    aria-label="Toggle sidebar"
                >
                    {sidebarCollapsed
                        ? <ChevronRight className="w-3 h-3 text-muted-foreground" />
                        : <ChevronLeft className="w-3 h-3 text-muted-foreground" />
                    }
                </button>
            </aside>
        </TooltipProvider>
    );
}
