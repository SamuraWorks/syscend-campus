const STATS = [
    { value: '20+', label: 'Core modules in one platform' },
    { value: '13', label: 'Staff & user roles supported' },
    { value: '3', label: 'National exams: NPSE, BECE, WASSCE' },
    { value: '100%', label: 'Data isolated per school' },
];

export default function Stats() {
    return (
        <section className="border-y border-border bg-secondary/40">
            <div className="mx-auto grid max-w-7xl grid-cols-2 gap-px overflow-hidden px-6 lg:grid-cols-4 lg:px-10">
                {STATS.map((stat) => (
                    <div key={stat.label} className="px-2 py-8 text-center sm:py-10">
                        <div className="font-serif text-3xl font-medium tracking-tight text-primary sm:text-4xl">
                            {stat.value}
                        </div>
                        <p className="mx-auto mt-2 max-w-[16ch] text-pretty text-xs leading-snug text-muted-foreground">
                            {stat.label}
                        </p>
                    </div>
                ))}
            </div>
        </section>
    );
}
