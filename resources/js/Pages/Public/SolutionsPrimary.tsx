import { Head } from '@inertiajs/react';
import SiteHeader from '@/components/landing/SiteHeader';
import SiteFooter from '@/components/landing/SiteFooter';
import { GraduationCap, BookOpen, BarChart3, CalendarCheck, Banknote, FileText } from 'lucide-react';

const features = [
    {
        icon: GraduationCap,
        title: 'Student Records',
        desc: 'Maintain complete profiles for every student — admissions, guardians, medical history, and academic history in one place.',
    },
    {
        icon: BookOpen,
        title: 'NPSE Preparation',
        desc: 'Tools and analytics specifically designed to prepare students for the National Primary School Examination with targeted practice.',
    },
    {
        icon: BarChart3,
        title: 'Grade Tracking',
        desc: 'Record and visualise grades across subjects and terms. Identify strengths and areas needing attention for each student.',
    },
    {
        icon: CalendarCheck,
        title: 'Attendance',
        desc: 'Automated attendance tracking with parent alerts and detailed reports for administrative and compliance needs.',
    },
    {
        icon: Banknote,
        title: 'Fee Management',
        desc: 'Create fee structures, send invoices, track payments, and generate receipts — all with automated reminders.',
    },
    {
        icon: FileText,
        title: 'Report Cards',
        desc: 'Generate professional, branded report cards in minutes. Customisable templates to match your school\'s standards.',
    },
];

export default function SolutionsPrimary() {
    return (
        <>
            <Head title="Primary School Management — Syscend Campus" />
            <SiteHeader />
            <main className="bg-background pt-24 pb-20">
                <div className="mx-auto max-w-6xl px-6 lg:px-10">
                    <div className="max-w-3xl">
                        <p className="font-mono text-xs uppercase tracking-[0.2em] text-primary">Solutions</p>
                        <h1 className="mt-4 font-serif text-4xl font-medium tracking-tight sm:text-5xl">
                            Primary School Management
                        </h1>
                        <p className="mt-6 text-lg leading-relaxed text-muted-foreground">
                            Built for primary schools (Grades 1&ndash;6). Manage students, teachers, curriculum,
                            and NPSE exam preparation with tools designed for foundational education.
                        </p>
                    </div>

                    <div className="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        {features.map((f) => (
                            <div
                                key={f.title}
                                className="rounded-md border border-border bg-card p-6 transition-colors hover:border-primary/20"
                            >
                                <span className="grid size-10 place-items-center rounded-sm bg-primary/10 text-primary">
                                    <f.icon className="size-5" />
                                </span>
                                <h3 className="mt-4 font-serif text-lg font-medium">{f.title}</h3>
                                <p className="mt-2 text-sm leading-relaxed text-muted-foreground">{f.desc}</p>
                            </div>
                        ))}
                    </div>

                    <section className="mt-16 rounded-md border border-accent/20 bg-accent/[0.06] p-8 text-center">
                        <h2 className="font-serif text-2xl font-medium">Ready to get started?</h2>
                        <p className="mt-3 text-muted-foreground">
                            Contact us to learn how Syscend Campus can transform your primary school.
                        </p>
                        <a
                            href="/contact"
                            className="mt-6 inline-flex items-center rounded-sm bg-primary px-6 py-3 text-sm font-medium text-primary-foreground transition-opacity hover:opacity-90"
                        >
                            Get in Touch
                        </a>
                    </section>
                </div>
            </main>
            <SiteFooter />
        </>
    );
}
