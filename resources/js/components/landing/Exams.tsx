import { GraduationCap } from 'lucide-react';

const EXAMS = [
    {
        code: 'NPSE',
        name: 'National Primary School Examination',
        desc: 'Prepare and track Primary 6 candidates with continuous assessment and readiness reports.',
    },
    {
        code: 'BECE',
        name: 'Basic Education Certificate Examination',
        desc: 'Manage JSS 3 candidates, subject registration and performance analytics through to results.',
    },
    {
        code: 'WASSCE',
        name: 'West African Senior School Certificate',
        desc: 'Support SSS 3 candidates across departments with grading aligned to national standards.',
    },
];

export default function Exams() {
    return (
        <section id="exams" className="scroll-mt-20 bg-foreground py-24 text-background lg:py-32">
            <div className="mx-auto max-w-7xl px-6 lg:px-10">
                <div className="mx-auto max-w-2xl text-center">
                    <p className="font-mono text-xs uppercase tracking-[0.2em] text-background/50">
                        National examinations
                    </p>
                    <h2 className="mt-4 text-balance font-serif text-3xl font-medium leading-tight tracking-tight sm:text-4xl">
                        Prepare candidates with confidence
                    </h2>
                    <p className="mt-5 text-pretty text-lg leading-relaxed text-background/70">
                        Built-in support for Sierra Leone&apos;s national examinations, helping schools manage
                        continuous assessment and produce the reports that matter most.
                    </p>
                </div>

                <div className="mt-12 grid grid-cols-1 gap-px overflow-hidden rounded-md border border-background/15 bg-background/15 md:grid-cols-3">
                    {EXAMS.map((exam) => (
                        <div
                            key={exam.code}
                            className="bg-foreground p-6 transition-colors hover:bg-background/5"
                        >
                            <div className="flex items-center gap-3">
                                <span className="grid size-10 place-items-center rounded-sm bg-primary text-primary-foreground">
                                    <GraduationCap className="size-5" />
                                </span>
                                <span className="font-serif text-2xl font-medium">{exam.code}</span>
                            </div>
                            <h3 className="mt-4 text-sm font-medium text-background/90">{exam.name}</h3>
                            <p className="mt-2 text-sm leading-relaxed text-background/60">{exam.desc}</p>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
