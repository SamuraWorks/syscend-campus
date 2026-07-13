import { Head } from '@inertiajs/react';
import SiteHeader from '@/components/landing/SiteHeader';
import SiteFooter from '@/components/landing/SiteFooter';
import { ClipboardList, PenLine, FileBadge, TrendingUp, BookOpen, Globe } from 'lucide-react';

const features = [
    {
        icon: ClipboardList,
        title: 'Exam Creation',
        desc: 'Create and schedule exams for any grading system — NPSE, BECE, WASSCE, or your school\'s internal assessments — with flexible mark structures.',
    },
    {
        icon: PenLine,
        title: 'Mark Entry',
        desc: 'Enter and validate marks subject-by-subject with built-in range checks, bulk import from Excel, and real-time class averages.',
    },
    {
        icon: FileBadge,
        title: 'Report Cards',
        desc: 'Auto-generate professional report cards with grades, teacher remarks, attendance summaries, and customizable school templates.',
    },
    {
        icon: TrendingUp,
        title: 'Grade Analysis',
        desc: 'Visualise grade distributions, pass rates, and improvement trends per class, per subject, and per student over time.',
    },
    {
        icon: BookOpen,
        title: 'Subject Performance',
        desc: 'Compare average scores across subjects to identify curriculum strengths and areas that need additional attention.',
    },
    {
        icon: Globe,
        title: 'District Comparisons',
        desc: 'Benchmark your school\'s results against district and national averages to understand where you stand.',
    },
];

export default function PlatformExams() {
    return (
        <>
            <Head title="Examination & Results — Syscend Campus" />
            <SiteHeader />
            <main className="bg-background pt-24 pb-20">
                <div className="mx-auto max-w-6xl px-6 lg:px-10">
                    {/* Hero */}
                    <div className="max-w-3xl">
                        <p className="font-mono text-xs uppercase tracking-[0.2em] text-primary">Platform</p>
                        <h1 className="mt-4 font-serif text-4xl font-medium tracking-tight sm:text-5xl">
                            Comprehensive Examination Management
                        </h1>
                        <p className="mt-6 text-lg leading-relaxed text-muted-foreground">
                            Create exams, record marks, generate report cards, and analyze performance across classes, subjects, and entire districts.
                        </p>
                    </div>

                    {/* Feature cards */}
                    <div className="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        {features.map((f) => (
                            <div key={f.title} className="rounded-md border border-border bg-card p-6 transition-colors hover:border-primary/20">
                                <span className="grid size-10 place-items-center rounded-sm bg-primary/10 text-primary">
                                    <f.icon className="size-5" />
                                </span>
                                <h3 className="mt-4 font-serif text-lg font-medium">{f.title}</h3>
                                <p className="mt-2 text-sm leading-relaxed text-muted-foreground">{f.desc}</p>
                            </div>
                        ))}
                    </div>

                    {/* CTA */}
                    <section className="mt-16 rounded-md border border-accent/20 bg-accent/[0.06] p-8 text-center">
                        <h2 className="font-serif text-2xl font-medium">Ready to get started?</h2>
                        <p className="mt-3 text-muted-foreground">Contact us to learn how Syscend Campus can transform your school.</p>
                        <a href="/contact" className="mt-6 inline-flex items-center rounded-sm bg-primary px-6 py-3 text-sm font-medium text-primary-foreground transition-opacity hover:opacity-90">
                            Get in Touch
                        </a>
                    </section>
                </div>
            </main>
            <SiteFooter />
        </>
    );
}
