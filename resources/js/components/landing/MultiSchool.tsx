import { Building2, Palette, CalendarRange, Lock } from 'lucide-react';

const FEATURES = [
    {
        icon: Building2,
        title: 'Many schools, one system',
        desc: 'A single installation supports multiple schools, each with its own students, teachers and data.',
    },
    {
        icon: Palette,
        title: 'Own branding',
        desc: 'Every school keeps its own identity, academic calendar, fee structure and reports.',
    },
    {
        icon: Lock,
        title: 'Complete isolation',
        desc: "Schools can never access one another's information — data stays private and secure.",
    },
    {
        icon: CalendarRange,
        title: 'Independent calendars',
        desc: "Terms, sessions and events are managed per school, on each school's own timeline.",
    },
];

export default function MultiSchool() {
    return (
        <section id="multi-school" className="scroll-mt-20 bg-background py-24 lg:py-32">
            <div className="mx-auto max-w-7xl px-6 lg:px-10">
                <div className="max-w-2xl">
                    <p className="font-mono text-xs uppercase tracking-[0.2em] text-primary">
                        Multi-school management
                    </p>
                    <h2 className="mt-4 text-balance font-serif text-3xl font-medium leading-tight tracking-tight sm:text-4xl">
                        Manage one school or an entire group
                    </h2>
                    <p className="mt-5 text-pretty text-lg leading-relaxed text-muted-foreground">
                        Whether you run a single campus or a network of schools, Syscend Campus keeps every
                        institution independent, secure and easy to administer.
                    </p>
                </div>

                <div className="mt-12 grid grid-cols-1 gap-px overflow-hidden rounded-md border border-border bg-border sm:grid-cols-2 lg:grid-cols-4">
                    {FEATURES.map((f) => (
                        <div key={f.title} className="bg-card p-6">
                            <span className="grid size-10 place-items-center rounded-sm bg-primary/10 text-primary">
                                <f.icon className="size-5" />
                            </span>
                            <h3 className="mt-4 text-base font-medium">{f.title}</h3>
                            <p className="mt-1.5 text-sm leading-relaxed text-muted-foreground">{f.desc}</p>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
