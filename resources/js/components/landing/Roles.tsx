import {
    Crown,
    Building,
    ShieldUser,
    School,
    Presentation,
    UserCog,
    BookOpen,
    User,
    Users,
    Calculator,
    Library,
    Bus,
    BedDouble,
} from 'lucide-react';

const ROLES = [
    { icon: Crown, label: 'Super Admin' },
    { icon: Building, label: 'School Owner' },
    { icon: ShieldUser, label: 'Administrator' },
    { icon: School, label: 'Principal' },
    { icon: Presentation, label: 'Teacher' },
    { icon: UserCog, label: 'Form Master' },
    { icon: BookOpen, label: 'Subject Teacher' },
    { icon: User, label: 'Student' },
    { icon: Users, label: 'Parent / Guardian' },
    { icon: Calculator, label: 'Accountant' },
    { icon: Library, label: 'Librarian' },
    { icon: Bus, label: 'Transport Officer' },
    { icon: BedDouble, label: 'Hostel Manager' },
];

export default function Roles() {
    return (
        <section id="roles" className="scroll-mt-20 bg-secondary/40 py-24 lg:py-32">
            <div className="mx-auto max-w-7xl px-6 lg:px-10">
                <div className="mx-auto max-w-2xl text-center">
                    <p className="font-mono text-xs uppercase tracking-[0.2em] text-primary">
                        Role-based access
                    </p>
                    <h2 className="mt-4 text-balance font-serif text-3xl font-medium leading-tight tracking-tight sm:text-4xl">
                        A tailored experience for every person
                    </h2>
                    <p className="mt-5 text-pretty text-lg leading-relaxed text-muted-foreground">
                        Secure, role-based portals give each member of the school community exactly what they
                        need — and nothing they don&apos;t.
                    </p>
                </div>

                <div className="mx-auto mt-12 flex max-w-4xl flex-wrap justify-center gap-3">
                    {ROLES.map((role) => (
                        <span
                            key={role.label}
                            className="inline-flex items-center gap-2 rounded-sm border border-border bg-card px-4 py-2.5 text-sm font-medium transition-colors hover:border-primary/40 hover:text-primary"
                        >
                            <role.icon className="size-4 text-primary" />
                            {role.label}
                        </span>
                    ))}
                </div>
            </div>
        </section>
    );
}
