import { Head } from '@inertiajs/react';
import SiteHeader from '@/components/landing/SiteHeader';
import SiteFooter from '@/components/landing/SiteFooter';
import { GraduationCap, ClipboardCheck, Settings, Users } from 'lucide-react';

const COURSES = [
    {
        icon: GraduationCap,
        title: 'Getting Started',
        desc: 'Learn how to set up your school, configure classes, sections, and subjects in under 30 minutes.',
    },
    {
        icon: Users,
        title: 'Student Management',
        desc: 'Master student admissions, enrollments, profile management, and bulk imports.',
    },
    {
        icon: ClipboardCheck,
        title: 'Attendance & Exams',
        desc: 'Set up daily attendance tracking, configure exam structures, and enter grades efficiently.',
    },
    {
        icon: Settings,
        title: 'Finance & Payroll',
        desc: 'Configure fee structures, collect payments, manage payroll, and generate financial reports.',
    },
];

export default function Training() {
    return (
        <>
            <Head title="Training — Syscend Campus" />
            <SiteHeader />
            <main className="bg-background pt-24 pb-20">
                <div className="mx-auto max-w-4xl px-6 lg:px-10">
                    <p className="font-mono text-xs uppercase tracking-[0.2em] text-primary">Learn</p>
                    <h1 className="mt-4 font-serif text-4xl font-medium tracking-tight sm:text-5xl">
                        Training
                    </h1>
                    <p className="mt-6 text-lg leading-relaxed text-muted-foreground">
                        Everything you need to get your school up and running with Syscend Campus.
                    </p>

                    <div className="mt-12 grid gap-6 sm:grid-cols-2">
                        {COURSES.map((course) => (
                            <div key={course.title} className="rounded-md border border-border bg-card p-6 transition-colors hover:border-primary/20">
                                <span className="grid size-10 place-items-center rounded-sm bg-primary/10 text-primary">
                                    <course.icon className="size-5" />
                                </span>
                                <h3 className="mt-4 font-serif text-lg font-medium">{course.title}</h3>
                                <p className="mt-2 text-sm leading-relaxed text-muted-foreground">{course.desc}</p>
                            </div>
                        ))}
                    </div>

                    <section className="mt-16">
                        <h2 className="font-serif text-2xl font-medium">Custom training for your school</h2>
                        <p className="mt-3 text-muted-foreground">
                            We offer personalized onboarding sessions for schools and school groups.
                            Our team will walk your administrators and teachers through every feature
                            relevant to your setup.
                        </p>
                        <a
                            href="/contact"
                            className="mt-6 inline-flex h-10 items-center justify-center rounded-sm bg-primary px-6 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90"
                        >
                            Contact us for training
                        </a>
                    </section>
                </div>
            </main>
            <SiteFooter />
        </>
    );
}
