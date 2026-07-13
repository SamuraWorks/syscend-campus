import { Link } from '@inertiajs/react';
import { ArrowRight, PhoneCall } from 'lucide-react';

export default function CTA() {
    return (
        <section id="cta" className="scroll-mt-20 bg-background pb-24 pt-4">
            <div className="mx-auto max-w-7xl px-6 lg:px-10">
                <div className="relative overflow-hidden rounded-md bg-primary px-6 py-20 text-center text-primary-foreground sm:px-12 sm:py-24">
                    <div
                        aria-hidden="true"
                        className="pointer-events-none absolute -top-24 left-1/2 size-[500px] -translate-x-1/2 rounded-full bg-primary-foreground/10 blur-[120px]"
                    />
                    <div
                        aria-hidden="true"
                        className="pointer-events-none absolute -bottom-20 right-0 size-[300px] rounded-full bg-accent/10 blur-[100px]"
                    />

                    <div className="relative mx-auto max-w-2xl">
                        <h2 className="text-balance font-serif text-3xl font-medium tracking-tight sm:text-4xl lg:text-5xl">
                            Ready to digitize your school?
                        </h2>
                        <p className="mt-5 text-pretty text-base leading-relaxed text-primary-foreground/70 sm:text-lg">
                            Request a demo and our team will handle setup, training and support — so your school can
                            start managing everything in one place, sooner.
                        </p>
                        <div className="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                            <Link
                                href="/request-demo"
                                className="inline-flex h-13 items-center gap-2.5 rounded-sm bg-accent px-7 text-sm font-semibold text-accent-foreground shadow-lg transition-colors hover:bg-accent/90"
                            >
                                Request a demo
                                <ArrowRight className="size-4" />
                            </Link>
                            <Link
                                href="/login"
                                className="inline-flex h-13 items-center gap-2.5 rounded-sm border border-primary-foreground/20 px-7 text-sm font-semibold text-primary-foreground backdrop-blur-sm transition-colors hover:border-primary-foreground/40 hover:bg-primary-foreground/5"
                            >
                                <PhoneCall className="size-4" />
                                Talk to sales
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
