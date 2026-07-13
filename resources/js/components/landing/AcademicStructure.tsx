import { Check } from 'lucide-react';

const LEVELS = [
    { stage: 'Nursery', classes: ['Nursery 1', 'Nursery 2', 'Nursery 3'] },
    { stage: 'Primary', classes: ['Class 1', 'Class 2', 'Class 3', 'Class 4', 'Class 5', 'Class 6'] },
    { stage: 'Junior Secondary', classes: ['JSS 1', 'JSS 2', 'JSS 3'] },
    { stage: 'Senior Secondary', classes: ['SSS 1', 'SSS 2', 'SSS 3'] },
];

const POINTS = [
    'Grades split into multiple sections (A, B, C or 1, 2, 3)',
    'Each section has a Form Master, students, timetable, attendance and results',
    'Configurable SSS departments: Science, Arts, Commercial, Technical',
    'Roles that match reality — Principal, Form Master, Subject Teacher, Accountant',
];

export default function AcademicStructure() {
    return (
        <section id="academic" className="scroll-mt-20 bg-secondary/40 py-24 lg:py-32">
            <div className="mx-auto max-w-7xl px-6 lg:px-10">
                <div className="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
                    <div>
                        <p className="font-mono text-xs uppercase tracking-[0.2em] text-primary">
                            Made for Sierra Leone
                        </p>
                        <h2 className="mt-4 text-balance font-serif text-3xl font-medium leading-tight tracking-tight sm:text-4xl">
                            Structured the way your schools actually work
                        </h2>
                        <p className="mt-5 text-pretty text-lg leading-relaxed text-muted-foreground">
                            Rather than a generic system, Syscend Campus reflects local class structures, teacher
                            roles and national examinations — from Nursery all the way to Senior Secondary.
                        </p>

                        <ul className="mt-8 space-y-4">
                            {POINTS.map((point) => (
                                <li key={point} className="flex items-start gap-3">
                                    <span className="mt-0.5 grid size-5 shrink-0 place-items-center rounded-sm bg-primary text-primary-foreground">
                                        <Check className="size-3" />
                                    </span>
                                    <span className="text-sm leading-relaxed text-foreground/90">{point}</span>
                                </li>
                            ))}
                        </ul>
                    </div>

                    <div className="relative">
                        <img
                            src="/images/classroom.jpeg"
                            alt="Students in a Sierra Leone classroom engaged in learning"
                            className="w-full rounded-md border border-border object-cover shadow-sm"
                        />
                    </div>
                </div>

                <div className="mt-14 grid grid-cols-1 gap-px overflow-hidden rounded-md border border-border bg-border sm:grid-cols-2 lg:grid-cols-4">
                    {LEVELS.map((level) => (
                        <div key={level.stage} className="bg-card p-5">
                            <h3 className="font-mono text-xs font-medium uppercase tracking-[0.2em] text-primary">
                                {level.stage}
                            </h3>
                            <ul className="mt-3 flex flex-wrap gap-2">
                                {level.classes.map((c) => (
                                    <li
                                        key={c}
                                        className="rounded-sm bg-secondary px-2.5 py-1 text-xs font-medium text-secondary-foreground"
                                    >
                                        {c}
                                    </li>
                                ))}
                            </ul>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
