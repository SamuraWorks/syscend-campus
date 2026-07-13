import { Head } from '@inertiajs/react';
import SiteHeader from '@/components/landing/SiteHeader';
import SiteFooter from '@/components/landing/SiteFooter';

export default function About() {
    return (
        <>
            <Head title="About Syscend — Syscend Campus" />
            <SiteHeader />
            <main className="bg-background pt-24 pb-20">
                <div className="mx-auto max-w-4xl px-6 lg:px-10">
                    <p className="font-mono text-xs uppercase tracking-[0.2em] text-primary">About Us</p>
                    <h1 className="mt-4 font-serif text-4xl font-medium tracking-tight sm:text-5xl">
                        Building the future of school management
                    </h1>
                    <p className="mt-6 text-lg leading-relaxed text-muted-foreground">
                        Syscend is a technology company focused on empowering schools across Sierra Leone and West Africa
                        with modern, affordable, and easy-to-use management tools.
                    </p>

                    <div className="mt-12 space-y-10">
                        <section>
                            <h2 className="font-serif text-2xl font-medium">Who we are</h2>
                            <p className="mt-3 leading-relaxed text-muted-foreground">
                                We are a team of educators, engineers, and designers who understand the challenges
                                schools face daily. From paper-based records to disconnected systems, we&apos;ve seen
                                firsthand how administrative burdens take time away from what matters most — teaching.
                            </p>
                        </section>

                        <section>
                            <h2 className="font-serif text-2xl font-medium">What we built</h2>
                            <p className="mt-3 leading-relaxed text-muted-foreground">
                                Syscend Campus is our answer: a single, integrated platform that handles everything
                                from student admissions and attendance to examinations, payroll, and communication.
                                Built specifically for the Sierra Leone education system, it supports NPSE, BECE,
                                and WASSCE exam structures, multi-school management, and role-based access for
                                every member of the school community.
                            </p>
                        </section>

                        <section>
                            <h2 className="font-serif text-2xl font-medium">Why it matters</h2>
                            <p className="mt-3 leading-relaxed text-muted-foreground">
                                Every school deserves access to professional management tools, regardless of size
                                or budget. Syscend Campus is designed to be cloud-ready, secure, and affordable —
                                so that administrators can focus on educating, not organizing.
                            </p>
                        </section>
                    </div>
                </div>
            </main>
            <SiteFooter />
        </>
    );
}
