import { Head } from '@inertiajs/react';
import SiteHeader from '@/components/landing/SiteHeader';
import SiteFooter from '@/components/landing/SiteFooter';
import { Award, Users, BookMarked, BarChart3, Compass, Scroll } from 'lucide-react';

const features = [
    {
        icon: Award,
        title: 'BECE & WASSCE Tracking',
        desc: 'Track candidate performance against BECE and WASSCE benchmarks. Identify gaps early and focus revision efforts.',
    },
    {
        icon: Users,
        title: 'Stream Management',
        desc: 'Organise classes into streams, assign teachers, and manage schedules for both JSS and SSS levels with ease.',
    },
    {
        icon: BookMarked,
        title: 'Subject Assignments',
        desc: 'Assign subjects to teachers, manage elective combinations, and balance workloads across your academic staff.',
    },
    {
        icon: BarChart3,
        title: 'Exam Analysis',
        desc: 'Deep analytics on exam results — class averages, subject trends, pass rates, and individual student progress over time.',
    },
    {
        icon: Compass,
        title: 'Career Guidance',
        desc: 'Track student subject performance and provide data-driven recommendations for career paths and further education.',
    },
    {
        icon: Scroll,
        title: 'Graduation Tracking',
        desc: 'Manage graduation requirements, track completion status, and produce certificates and transcripts for graduating students.',
    },
];

export default function SolutionsSecondary() {
    return (
        <>
            <Head title="Secondary School Management — Syscend Campus" />
            <SiteHeader />
            <main className="bg-background pt-24 pb-20">
                <div className="mx-auto max-w-6xl px-6 lg:px-10">
                    <div className="max-w-3xl">
                        <p className="font-mono text-xs uppercase tracking-[0.2em] text-primary">Solutions</p>
                        <h1 className="mt-4 font-serif text-4xl font-medium tracking-tight sm:text-5xl">
                            Secondary School Management
                        </h1>
                        <p className="mt-6 text-lg leading-relaxed text-muted-foreground">
                            Comprehensive tools for junior and senior secondary schools. Handle BECE and WASSCE
                            exams, stream management, and advanced analytics.
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
                            Contact us to learn how Syscend Campus can transform your secondary school.
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
