import AppLayout from '@/Layouts/AppLayout';
import { Link } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import {
    Fingerprint, CalendarDays, GraduationCap, Layers, BookOpen,
    Building2, BadgeCheck, Clock, CalendarOff, Settings, BarChart3,
    Landmark, Plug, CalendarClock,
} from 'lucide-react';
import { cn } from '@/lib/utils';

const SETUP_ITEMS = [
    {
        label: 'School Identity',
        description: 'School profile, name, motto, registration, contact, leadership, branding, official documents',
        href: '/school/school-identity',
        icon: Fingerprint,
        color: 'bg-indigo-50 text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-400',
    },
    {
        label: 'Academic Years',
        description: 'Manage academic years, terms, and term dates',
        href: '/school/academic-terms',
        icon: CalendarDays,
        color: 'bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400',
    },
    {
        label: 'Classes',
        description: 'Define classes, assign teachers, set class capacity',
        href: '/school/classes',
        icon: GraduationCap,
        color: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400',
    },
    {
        label: 'Sections',
        description: 'Manage class sections and subdivisions',
        href: '/school/sections',
        icon: Layers,
        color: 'bg-teal-50 text-teal-600 dark:bg-teal-950/40 dark:text-teal-400',
    },
    {
        label: 'Subjects',
        description: 'Define subjects, assign codes, link to departments',
        href: '/school/subjects',
        icon: BookOpen,
        color: 'bg-cyan-50 text-cyan-600 dark:bg-cyan-950/40 dark:text-cyan-400',
    },
    {
        label: 'Departments',
        description: 'Organize staff and subjects into departments',
        href: '/school/departments',
        icon: Building2,
        color: 'bg-violet-50 text-violet-600 dark:bg-violet-950/40 dark:text-violet-400',
    },
    {
        label: 'Designations',
        description: 'Staff roles and designation levels',
        href: '/school/designations',
        icon: BadgeCheck,
        color: 'bg-purple-50 text-purple-600 dark:bg-purple-950/40 dark:text-purple-400',
    },
    {
        label: 'Shifts',
        description: 'Configure school shifts (morning, afternoon, evening)',
        href: '/school/shifts',
        icon: Clock,
        color: 'bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400',
    },
    {
        label: 'Holidays',
        description: 'Set school holidays and non-working days',
        href: '/school/holidays',
        icon: CalendarOff,
        color: 'bg-orange-50 text-orange-600 dark:bg-orange-950/40 dark:text-orange-400',
    },
    {
        label: 'Timetable Settings',
        description: 'School hours, periods, break times, event types',
        href: '/school/settings/school-time',
        icon: CalendarClock,
        color: 'bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400',
    },
    {
        label: 'Assessment Config',
        description: 'Coursework and examination weight percentages',
        href: '/school/assessment-config',
        icon: Settings,
        color: 'bg-slate-50 text-slate-600 dark:bg-slate-950/40 dark:text-slate-400',
    },
    {
        label: 'Grade Scales',
        description: 'Grade, GPA, marks range, and remarks mapping',
        href: '/school/grade-scales',
        icon: BarChart3,
        color: 'bg-lime-50 text-lime-600 dark:bg-lime-950/40 dark:text-lime-400',
    },
    {
        label: 'School Preferences',
        description: 'General settings, branding, academic config, notifications',
        href: '/school/settings',
        icon: Settings,
        color: 'bg-gray-50 text-gray-600 dark:bg-gray-950/40 dark:text-gray-400',
    },
    {
        label: 'Sierra Leone Config',
        description: 'National grading system, education level, exam centres',
        href: '/school/settings/sierra-leone',
        icon: Landmark,
        color: 'bg-sky-50 text-sky-600 dark:bg-sky-950/40 dark:text-sky-400',
    },
    {
        label: 'Integrations',
        description: 'SMTP email, SMS gateway, payment gateway configuration',
        href: '/school/settings/integrations',
        icon: Plug,
        color: 'bg-pink-50 text-pink-600 dark:bg-pink-950/40 dark:text-pink-400',
    },
];

export default function SchoolSetupIndex() {
    return (
        <AppLayout title="School Setup">
            <div className="space-y-6">
                <div>
                    <h1 className="text-2xl font-bold text-slate-900 dark:text-white">School Setup</h1>
                    <p className="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Configure your school once — every module automatically inherits these settings.
                    </p>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    {SETUP_ITEMS.map((item) => (
                        <Link key={item.href} href={item.href}>
                            <Card className="h-full hover:shadow-md hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-150 cursor-pointer group">
                                <CardContent className="p-5">
                                    <div className="flex items-start gap-4">
                                        <div className={cn('p-2.5 rounded-xl shrink-0', item.color)}>
                                            <item.icon className="w-5 h-5" />
                                        </div>
                                        <div className="min-w-0">
                                            <h3 className="font-semibold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                {item.label}
                                            </h3>
                                            <p className="text-sm text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">
                                                {item.description}
                                            </p>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        </Link>
                    ))}
                </div>
            </div>
        </AppLayout>
    );
}
