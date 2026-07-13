import { Target, Compass } from 'lucide-react';

export default function VisionMission() {
    return (
        <section className="bg-background py-24 lg:py-32">
            <div className="mx-auto max-w-7xl px-6 lg:px-10">
                <div className="grid gap-5 md:grid-cols-2">
                    <div className="rounded-md border border-border bg-card p-8 sm:p-10">
                        <span className="grid size-10 place-items-center rounded-sm bg-primary/10 text-primary">
                            <Compass className="size-5" />
                        </span>
                        <h3 className="mt-5 font-serif text-xl font-medium">Our Vision</h3>
                        <p className="mt-3 text-pretty leading-relaxed text-muted-foreground">
                            To become the leading school management platform in Sierra Leone by giving schools an
                            affordable, secure and easy-to-use system that manages every aspect of school
                            operations.
                        </p>
                    </div>

                    <div className="rounded-md border border-accent/20 bg-accent/[0.06] p-8 sm:p-10">
                        <span className="grid size-10 place-items-center rounded-sm bg-accent text-accent-foreground">
                            <Target className="size-5" />
                        </span>
                        <h3 className="mt-5 font-serif text-xl font-medium">Our Mission</h3>
                        <p className="mt-3 text-pretty leading-relaxed text-muted-foreground">
                            Help schools replace paper-based processes with one integrated platform for
                            administrators, teachers, students, parents and accountants — starting in Sierra Leone
                            and expanding across West Africa.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    );
}
