import { Head } from '@inertiajs/react';
import SiteHeader from '@/components/landing/SiteHeader';
import SiteFooter from '@/components/landing/SiteFooter';
import { Megaphone, MessageSquare, Smartphone, Mail, AlertTriangle, MessageCircle } from 'lucide-react';

const features = [
    {
        icon: Megaphone,
        title: 'Announcements',
        desc: 'Publish school-wide, class-specific, or targeted announcements that reach the right audience instantly through the portal.',
    },
    {
        icon: MessageSquare,
        title: 'Parent Messaging',
        desc: 'Enable direct, secure messaging between parents and teachers for progress updates, behaviour reports, and general inquiries.',
    },
    {
        icon: Smartphone,
        title: 'SMS Notifications',
        desc: 'Send bulk or individual SMS messages for attendance alerts, fee reminders, exam schedules, and event invitations.',
    },
    {
        icon: Mail,
        title: 'Email Reports',
        desc: 'Automatically email weekly summaries, report cards, and newsletters to parents and staff with branded templates.',
    },
    {
        icon: AlertTriangle,
        title: 'Emergency Alerts',
        desc: 'Push instant emergency notifications to all parents and staff via SMS and in-app alerts for urgent situations.',
    },
    {
        icon: MessageCircle,
        title: 'Discussion Forums',
        desc: 'Create moderated forums for classes, departments, or the whole school to share ideas, resources, and updates.',
    },
];

export default function PlatformCommunication() {
    return (
        <>
            <Head title="Communication Hub — Syscend Campus" />
            <SiteHeader />
            <main className="bg-background pt-24 pb-20">
                <div className="mx-auto max-w-6xl px-6 lg:px-10">
                    {/* Hero */}
                    <div className="max-w-3xl">
                        <p className="font-mono text-xs uppercase tracking-[0.2em] text-primary">Platform</p>
                        <h1 className="mt-4 font-serif text-4xl font-medium tracking-tight sm:text-5xl">
                            Seamless School Communication
                        </h1>
                        <p className="mt-6 text-lg leading-relaxed text-muted-foreground">
                            Connect teachers, parents, students, and administrators through announcements, messaging, and automated notifications.
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
