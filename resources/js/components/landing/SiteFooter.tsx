import { Link } from '@inertiajs/react';
import { Landmark } from 'lucide-react';
import Logo from '@/components/landing/Logo';

const COLUMNS = [
    {
        title: 'Platform',
        links: [
            { label: 'Student Information', href: '/platform/students' },
            { label: 'Fee Management', href: '/platform/fees' },
            { label: 'Examinations', href: '/platform/exams' },
            { label: 'Analytics', href: '/platform/analytics' },
            { label: 'Communication', href: '/platform/communication' },
        ],
    },
    {
        title: 'Solutions',
        links: [
            { label: 'Nursery Schools', href: '/solutions/nursery' },
            { label: 'Primary Schools', href: '/solutions/primary' },
            { label: 'Secondary Schools', href: '/solutions/secondary' },
            { label: 'Combined Schools', href: '/solutions/combined' },
            { label: 'Multi-School Groups', href: '/solutions/multi-school' },
        ],
    },
    {
        title: 'Company',
        links: [
            { label: 'About Syscend', href: '/about' },
            { label: 'Vision & Mission', href: '/vision-mission' },
            { label: 'Support', href: '/support' },
            { label: 'Training', href: '/training' },
            { label: 'Contact', href: '/contact' },
        ],
    },
];

export default function SiteFooter() {
    return (
        <footer className="bg-foreground text-background">
            <div className="mx-auto max-w-7xl px-6 py-16 lg:px-10">
                {/* Ministry of Education badge */}
                <div className="mb-12 flex justify-center">
                    <div className="inline-flex items-center gap-4 rounded-md border border-background/10 bg-background/5 px-6 py-4">
                        <span className="grid size-12 place-items-center rounded-sm bg-background/10 text-background">
                            <Landmark className="size-6" />
                        </span>
                        <div>
                            <p className="text-sm font-semibold text-background">Ministry of Education Portal</p>
                            <p className="mt-0.5 font-mono text-[10px] uppercase tracking-[0.15em] text-background/35">National Oversight & Policy Analytics</p>
                        </div>
                    </div>
                </div>

                <div className="grid gap-10 lg:grid-cols-5">
                    <div className="lg:col-span-2">
                        <Link href="/">
                            <Logo tone="light" />
                        </Link>
                        <p className="mt-4 max-w-sm text-sm leading-relaxed text-background/50">
                            A modern, cloud-ready school management platform built by Syscend to simplify school
                            administration across Sierra Leone and West Africa.
                        </p>
                    </div>

                    {COLUMNS.map((col) => (
                        <div key={col.title}>
                            <p className="font-mono text-xs uppercase tracking-[0.2em] text-background/40">
                                {col.title}
                            </p>
                            <ul className="mt-4 space-y-3">
                                {col.links.map((link) => (
                                    <li key={link.label}>
                                        <Link
                                            href={link.href}
                                            className="text-sm text-background/55 transition-colors hover:text-background/90"
                                        >
                                            {link.label}
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    ))}
                </div>

                <div className="mt-12 flex flex-col items-start justify-between gap-4 border-t border-background/10 pt-8 sm:flex-row sm:items-center">
                    <p className="text-sm text-background/35">
                        &copy; {new Date().getFullYear()} Syscend. Syscend Campus. All rights reserved.
                    </p>
                    <div className="flex gap-6">
                        <Link href="/privacy" className="text-sm text-background/35 transition-colors hover:text-background/70">
                            Privacy
                        </Link>
                        <Link href="/terms" className="text-sm text-background/35 transition-colors hover:text-background/70">
                            Terms
                        </Link>
                        <Link href="/security" className="text-sm text-background/35 transition-colors hover:text-background/70">
                            Security
                        </Link>
                    </div>
                </div>
            </div>
        </footer>
    );
}
