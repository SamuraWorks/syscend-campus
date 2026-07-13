import { Head } from '@inertiajs/react';
import SiteHeader from '@/components/landing/SiteHeader';
import SiteFooter from '@/components/landing/SiteFooter';
import { CreditCard, Receipt, Wallet, PieChart, FileText, Bell } from 'lucide-react';

const features = [
    {
        icon: CreditCard,
        title: 'Automated Billing',
        desc: 'Generate fee invoices automatically based on class, grade level, and custom fee structures — no manual entry required.',
    },
    {
        icon: Receipt,
        title: 'Payment Tracking',
        desc: 'Monitor every payment in real time with clear ledgers showing paid, pending, and overdue balances for each student.',
    },
    {
        icon: Wallet,
        title: 'Multiple Payment Methods',
        desc: 'Accept cash, bank transfers, mobile money (Orange Money, Africell), and online payments with instant reconciliation.',
    },
    {
        icon: PieChart,
        title: 'Financial Reports',
        desc: 'Generate revenue summaries, outstanding balance reports, and income-vs-expense breakdowns at the click of a button.',
    },
    {
        icon: FileText,
        title: 'Invoice Generation',
        desc: 'Create professional, printable invoices with school branding, itemised fee breakdowns, and due dates.',
    },
    {
        icon: Bell,
        title: 'Reminder System',
        desc: 'Send automated SMS and email reminders for upcoming dues, overdue payments, and receipt confirmations.',
    },
];

export default function PlatformFees() {
    return (
        <>
            <Head title="Fee Management — Syscend Campus" />
            <SiteHeader />
            <main className="bg-background pt-24 pb-20">
                <div className="mx-auto max-w-6xl px-6 lg:px-10">
                    {/* Hero */}
                    <div className="max-w-3xl">
                        <p className="font-mono text-xs uppercase tracking-[0.2em] text-primary">Platform</p>
                        <h1 className="mt-4 font-serif text-4xl font-medium tracking-tight sm:text-5xl">
                            Smart Fee Management
                        </h1>
                        <p className="mt-6 text-lg leading-relaxed text-muted-foreground">
                            Automate billing, track payments, and generate financial reports with support for multiple payment methods including mobile money.
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
