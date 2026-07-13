import { Head } from '@inertiajs/react';
import SiteHeader from '@/components/landing/SiteHeader';
import SiteFooter from '@/components/landing/SiteFooter';
import { LayoutDashboard, TrendingUp, DollarSign, Users, FileSearch, Download } from 'lucide-react';

const features = [
    {
        icon: LayoutDashboard,
        title: 'Real-time Dashboards',
        desc: 'See your school\'s key metrics at a glance — enrollment numbers, attendance rates, fee collections, and exam averages — all updated live.',
    },
    {
        icon: TrendingUp,
        title: 'Enrollment Trends',
        desc: 'Track enrollment growth, gender ratios, and class capacity over terms and years to plan for the future.',
    },
    {
        icon: DollarSign,
        title: 'Financial Reports',
        desc: 'Drill into revenue streams, outstanding balances, and collection rates with interactive charts and detailed breakdowns.',
    },
    {
        icon: Users,
        title: 'Staff Analytics',
        desc: 'Monitor teacher workload, qualification distribution, and staff-to-student ratios to optimise resource allocation.',
    },
    {
        icon: FileSearch,
        title: 'Custom Reports',
        desc: 'Build your own reports by selecting metrics, filters, and date ranges — then save and share them with stakeholders.',
    },
    {
        icon: Download,
        title: 'Export & Share',
        desc: 'Export any report as PDF, Excel, or CSV and share via email or WhatsApp directly from the platform.',
    },
];

export default function PlatformAnalytics() {
    return (
        <>
            <Head title="Analytics & Reports — Syscend Campus" />
            <SiteHeader />
            <main className="bg-background pt-24 pb-20">
                <div className="mx-auto max-w-6xl px-6 lg:px-10">
                    {/* Hero */}
                    <div className="max-w-3xl">
                        <p className="font-mono text-xs uppercase tracking-[0.2em] text-primary">Platform</p>
                        <h1 className="mt-4 font-serif text-4xl font-medium tracking-tight sm:text-5xl">
                            Data-Driven Decision Making
                        </h1>
                        <p className="mt-6 text-lg leading-relaxed text-muted-foreground">
                            Real-time dashboards and customizable reports give school administrators and the Ministry of Education the insights they need.
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
