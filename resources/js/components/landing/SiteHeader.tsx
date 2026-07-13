import { useEffect, useState } from 'react';
import { Link } from '@inertiajs/react';
import { Menu, X } from 'lucide-react';
import Logo from '@/components/landing/Logo';

const NAV_LINKS = [
    { label: 'Platform', href: '#modules' },
    { label: 'For Sierra Leone', href: '#academic' },
    { label: 'Examinations', href: '#exams' },
    { label: 'Multi-School', href: '#multi-school' },
    { label: 'Roles', href: '#roles' },
];

export default function SiteHeader() {
    const [scrolled, setScrolled] = useState(false);
    const [open, setOpen] = useState(false);

    useEffect(() => {
        const onScroll = () => setScrolled(window.scrollY > 24);
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
        return () => window.removeEventListener('scroll', onScroll);
    }, []);

    return (
        <header
            className={`fixed inset-x-0 top-0 z-50 transition-colors duration-300 ${
                scrolled
                    ? 'border-b border-border bg-background/95 backdrop-blur-md'
                    : 'border-b border-white/10'
            }`}
        >
            <div className="mx-auto flex h-18 max-w-7xl items-center justify-between px-6 lg:px-10">
                <a href="#top">
                    <Logo tone={scrolled ? 'dark' : 'light'} />
                </a>

                <nav className="hidden items-center gap-8 md:flex" aria-label="Primary">
                    {NAV_LINKS.map((link) => (
                        <a
                            key={link.href}
                            href={link.href}
                            className={`text-sm transition-colors ${
                                scrolled
                                    ? 'text-muted-foreground hover:text-foreground'
                                    : 'text-white/70 hover:text-white'
                            }`}
                        >
                            {link.label}
                        </a>
                    ))}
                </nav>

                <div className="hidden items-center gap-3 md:flex">
                    <Link
                        href="/login"
                        className={`inline-flex items-center rounded-sm px-4 py-2 text-sm font-medium transition-colors ${
                            scrolled
                                ? 'text-muted-foreground hover:text-foreground'
                                : 'text-white/80 hover:text-white'
                        }`}
                    >
                        Sign in
                    </Link>
                    <Link
                        href="/request-demo"
                        className={`inline-flex items-center rounded-sm px-4 py-2 text-sm font-medium transition-opacity hover:opacity-90 ${
                            scrolled
                                ? 'bg-primary text-primary-foreground'
                                : 'bg-white text-foreground'
                        }`}
                    >
                        Request a demo
                    </Link>
                </div>

                <button
                    type="button"
                    onClick={() => setOpen((v) => !v)}
                    className={`inline-flex size-9 items-center justify-center rounded-sm md:hidden ${
                        scrolled ? 'text-foreground' : 'text-white'
                    }`}
                    aria-label={open ? 'Close menu' : 'Open menu'}
                    aria-expanded={open}
                >
                    {open ? <X className="size-5" /> : <Menu className="size-5" />}
                </button>
            </div>

            {open && (
                <div className="border-t border-border bg-background md:hidden">
                    <nav className="mx-auto flex max-w-7xl flex-col px-6 py-4" aria-label="Mobile">
                        {NAV_LINKS.map((link) => (
                            <a
                                key={link.href}
                                href={link.href}
                                onClick={() => setOpen(false)}
                                className="py-2 text-sm text-muted-foreground transition-colors hover:text-foreground"
                            >
                                {link.label}
                            </a>
                        ))}
                        <div className="mt-4 flex flex-col gap-2">
                            <Link
                                href="/login"
                                onClick={() => setOpen(false)}
                                className="inline-flex items-center justify-center rounded-sm border border-border px-4 py-2.5 text-sm font-medium text-foreground"
                            >
                                Sign in
                            </Link>
                            <Link
                                href="/request-demo"
                                onClick={() => setOpen(false)}
                                className="inline-flex items-center justify-center rounded-sm bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground"
                            >
                                Request a demo
                            </Link>
                        </div>
                    </nav>
                </div>
            )}
        </header>
    );
}
