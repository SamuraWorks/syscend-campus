import { Link } from '@inertiajs/react';
import {
    Users,
    ClipboardCheck,
    CalendarDays,
    GraduationCap,
    FileText,
    ArrowUpRight,
    Wallet,
    Banknote,
    BriefcaseBusiness,
    Library,
    Boxes,
    Bus,
    MessagesSquare,
    BarChart3,
    ArrowRight,
} from 'lucide-react';

const FEATURED = {
    icon: GraduationCap,
    title: 'Examinations',
    desc: 'Continuous assessment, grading and exam preparation.',
    href: '/school/exams',
    detail: 'From NPSE to WASSCE, track every student\'s journey with automated grading, report cards and performance analytics.',
};

const MODULES = [
    { icon: Users, title: 'Student Information', desc: 'Central records, admissions and enrollment for every pupil.', href: '/school/students' },
    { icon: ClipboardCheck, title: 'Attendance', desc: 'Daily class and subject attendance with instant summaries.', href: '/school/attendance' },
    { icon: CalendarDays, title: 'Timetable', desc: 'Build section timetables for teachers and students.', href: '/school/timetable' },
    { icon: FileText, title: 'Report Cards', desc: 'Generate professional term reports automatically.', href: '/school/reports/academic' },
    { icon: ArrowUpRight, title: 'Promotion', desc: 'Move students between classes and sections with ease.', href: '/school/students' },
    { icon: Wallet, title: 'Fee Management', desc: 'Invoices, payments, balances and fee reporting.', href: '/school/fees/payments' },
    { icon: Banknote, title: 'Payroll', desc: 'Manage staff salaries, deductions and payslips.', href: '/school/hr/payroll' },
    { icon: BriefcaseBusiness, title: 'HR', desc: 'Staff profiles, roles and permissions in one place.', href: '/school/staff' },
    { icon: Library, title: 'Library', desc: 'Catalogue books, track lending and returns.', href: '/school/library/books' },
    { icon: Boxes, title: 'Inventory', desc: 'Track school assets, supplies and stock levels.', href: '/school/inventory/items' },
    { icon: Bus, title: 'Transport', desc: 'Routes, vehicles and student transport logistics.', href: '/school/transport/vehicles' },
    { icon: MessagesSquare, title: 'Communication', desc: 'Notifications and messaging across the school community.', href: '/school/communication/announcements' },
    { icon: BarChart3, title: 'Analytics', desc: 'Dashboards and insights on every part of operations.', href: '/school/reports/dashboard' },
];

export default function Modules() {
    return (
        <section id="modules" className="scroll-mt-20 bg-background py-24 lg:py-32">
            <div className="mx-auto max-w-7xl px-6 lg:px-10">
                {/* Header — centered */}
                <div className="mx-auto max-w-2xl text-center">
                    <p className="font-mono text-xs uppercase tracking-[0.2em] text-primary">
                        One platform, every operation
                    </p>
                    <h2 className="mt-4 text-balance font-serif text-3xl font-medium leading-tight tracking-tight sm:text-4xl lg:text-5xl">
                        Everything a school needs to run, connected
                    </h2>
                    <p className="mt-5 text-pretty text-lg leading-relaxed text-muted-foreground">
                        From admissions to graduation, Syscend Campus brings administration, teaching, learning,
                        finance and communication into a single, secure system.
                    </p>
                </div>

                {/* Featured module + screenshot */}
                <div className="mt-16 grid items-center gap-10 lg:grid-cols-2 lg:gap-14">
                    <div className="order-2 lg:order-1">
                        <div className="inline-flex items-center gap-2 rounded-sm bg-primary/10 px-3 py-1.5">
                            <span className="grid size-7 place-items-center rounded-sm bg-primary text-primary-foreground">
                                <FEATURED.icon className="size-3.5" />
                            </span>
                            <span className="font-mono text-xs font-medium uppercase tracking-wider text-primary">
                                Core Module
                            </span>
                        </div>

                        <h3 className="mt-5 font-serif text-2xl font-medium sm:text-3xl">
                            {FEATURED.title}
                        </h3>
                        <p className="mt-3 text-pretty text-base leading-relaxed text-muted-foreground">
                            {FEATURED.detail}
                        </p>

                        <ul className="mt-6 space-y-3">
                            {['Automated grading & GPA calculation', 'NPSE, BECE & WASSCE exam prep', 'Report card generation in seconds', 'Student performance analytics'].map((item) => (
                                <li key={item} className="flex items-center gap-2.5 text-sm text-foreground/80">
                                    <span className="grid size-5 shrink-0 place-items-center rounded-full bg-primary/10 text-primary">
                                        <FEATURED.icon className="size-3" />
                                    </span>
                                    {item}
                                </li>
                            ))}
                        </ul>

                        <Link
                            href={FEATURED.href}
                            className="mt-8 inline-flex items-center gap-2 text-sm font-medium text-primary transition-colors hover:text-primary/80"
                        >
                            Explore examinations
                            <ArrowRight className="size-4" />
                        </Link>
                    </div>

                    <div className="order-1 lg:order-2">
                        <Link href={FEATURED.href}>
                            <img
                                src="/images/dashboard.jpeg"
                                alt="Syscend Campus dashboard showing examination management"
                                className="w-full rounded-md border border-border object-cover shadow-sm transition-shadow hover:shadow-md"
                            />
                        </Link>
                    </div>
                </div>

                {/* Module grid — linked cards */}
                <div className="mt-20 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    {MODULES.map((mod) => (
                        <Link
                            key={mod.title}
                            href={mod.href}
                            className="group relative flex items-start gap-4 rounded-md border border-border bg-card p-5 transition-all duration-200 hover:border-primary/20 hover:shadow-md"
                        >
                            <span className="grid size-10 shrink-0 place-items-center rounded-sm bg-primary/10 text-primary transition-colors group-hover:bg-primary group-hover:text-primary-foreground">
                                <mod.icon className="size-5" />
                            </span>
                            <div>
                                <h3 className="text-sm font-medium">{mod.title}</h3>
                                <p className="mt-1 text-sm leading-relaxed text-muted-foreground">{mod.desc}</p>
                            </div>
                        </Link>
                    ))}
                </div>

                {/* Bottom accent line */}
                <div className="mx-auto mt-16 h-px max-w-xs bg-gradient-to-r from-transparent via-primary/30 to-transparent" />
            </div>
        </section>
    );
}
