import { MapPin, ArrowDown, ArrowRight, Check, ShieldCheck } from 'lucide-react';
import { Link } from '@inertiajs/react';

const HIGHLIGHTS = ['Built for Sierra Leone', 'NPSE · BECE · WASSCE ready', 'Multi-school by design'];

const STATS = [
    { stat: '20+', label: 'Core modules' },
    { stat: '13', label: 'User roles' },
    { stat: '3', label: 'National exams' },
    { stat: '100%', label: 'Data isolation' },
];

export default function Hero() {
    return (
        <section id="top" className="relative isolate overflow-hidden">
            {/* Background image */}
            <div className="absolute inset-0 -z-10">
                <img
                    src="/images/hero-bg.jpg"
                    alt=""
                    aria-hidden="true"
                    className="size-full object-cover brightness-125 saturate-110"
                />
                <div className="absolute inset-0 bg-gradient-to-b from-foreground/80 via-foreground/65 to-foreground/80" />
            </div>

            <div className="mx-auto flex min-h-screen max-w-7xl flex-col items-center justify-center px-6 pb-16 pt-28 text-center lg:px-10">
                <div className="inline-flex w-fit items-center gap-2 rounded-full border border-background/35 bg-background/15 px-3 py-1 text-xs font-medium text-background backdrop-blur-sm">
                    <ShieldCheck className="size-3.5" aria-hidden="true" />
                    Secure, cloud-ready school management
                </div>

                <h1 className="mt-6 max-w-4xl text-balance font-serif text-4xl font-medium leading-[1.05] tracking-tight text-background sm:text-6xl lg:text-7xl">
                    Run your entire school on one integrated platform.
                </h1>

                <p className="mt-6 max-w-2xl text-pretty text-lg leading-relaxed text-background/90">
                    Syscend Campus replaces paper-based processes with a single system for administrators,
                    teachers, students, parents, and accountants — designed around how schools in Sierra
                    Leone actually operate.
                </p>

                <div className="mt-9 flex flex-col items-center gap-3 sm:flex-row">
                    <Link
                        href="/request-demo"
                        className="inline-flex items-center justify-center gap-2 rounded-sm bg-background px-6 py-3 text-sm font-medium text-foreground transition-opacity hover:opacity-90"
                    >
                        Request a demo
                        <ArrowRight className="size-4" />
                    </Link>
                    <a
                        href="#modules"
                        className="inline-flex items-center justify-center rounded-sm border border-background/40 px-6 py-3 text-sm font-medium text-background transition-colors hover:bg-background/10"
                    >
                        Explore the platform
                    </a>
                </div>

                {/* Trust strip */}
                <dl className="mt-16 grid max-w-3xl grid-cols-2 gap-x-8 gap-y-8 border-t border-background/30 pt-8 sm:grid-cols-4">
                    {STATS.map((item) => (
                        <div key={item.label}>
                            <dt className="font-serif text-3xl font-medium text-background sm:text-4xl">
                                {item.stat}
                            </dt>
                            <dd className="mt-1 text-xs leading-relaxed text-background/80">
                                {item.label}
                            </dd>
                        </div>
                    ))}
                </dl>
            </div>

            <a
                href="#modules"
                className="absolute bottom-6 left-1/2 hidden -translate-x-1/2 items-center gap-2 text-xs text-background/80 transition-colors hover:text-background lg:inline-flex"
            >
                <ArrowDown className="size-4 animate-bounce" aria-hidden="true" />
                Explore the platform
            </a>
        </section>
    );
}
