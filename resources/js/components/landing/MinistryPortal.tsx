import { Landmark } from 'lucide-react';

export default function MinistryPortal() {
    return (
        <section id="ministry" className="scroll-mt-20 bg-secondary/40 py-24 lg:py-32">
            <div className="mx-auto max-w-7xl px-6 lg:px-10">
                <div className="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
                    <div>
                        <p className="font-mono text-xs uppercase tracking-[0.2em] text-primary">
                            National Oversight
                        </p>
                        <h2 className="mt-4 text-balance font-serif text-3xl font-medium leading-tight tracking-tight sm:text-4xl">
                            Ministry of Education Portal
                        </h2>
                        <p className="mt-5 text-pretty text-lg leading-relaxed text-muted-foreground">
                            Syscend Campus connects directly to the Ministry of Education&apos;s national oversight
                            and policy analytics platform, ensuring your school data aligns with government
                            reporting standards.
                        </p>

                        <ul className="mt-6 space-y-3">
                            {[
                                'Real-time policy compliance reporting',
                                'National examination data sync (NPSE, BECE, WASSCE)',
                                'Government accreditation support',
                                'Automated regulatory submissions',
                            ].map((item) => (
                                <li key={item} className="flex items-center gap-2.5 text-sm text-foreground/80">
                                    <span className="grid size-5 shrink-0 place-items-center rounded-full bg-primary/10 text-primary">
                                        <Landmark className="size-3" />
                                    </span>
                                    {item}
                                </li>
                            ))}
                        </ul>
                    </div>

                    <div className="flex items-center justify-center">
                        <div className="relative w-full max-w-sm rounded-md border border-border bg-card p-10 text-center shadow-sm">
                            <span className="grid mx-auto size-16 place-items-center rounded-md bg-primary/10 text-primary">
                                <Landmark className="size-8" />
                            </span>
                            <h3 className="mt-6 font-serif text-xl font-medium">Ministry of Education</h3>
                            <p className="mt-2 text-sm text-muted-foreground">Sierra Leone</p>
                            <div className="mt-6 space-y-1.5">
                                <p className="text-xs text-muted-foreground">
                                    National Oversight & Policy Analytics
                                </p>
                            </div>
                            <div className="mt-6 h-px bg-border" />
                            <p className="mt-4 text-xs leading-relaxed text-muted-foreground">
                                Integrated with Syscend Campus for seamless government reporting and compliance.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
