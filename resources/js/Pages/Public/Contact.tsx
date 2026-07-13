import { Head } from '@inertiajs/react';
import SiteHeader from '@/components/landing/SiteHeader';
import SiteFooter from '@/components/landing/SiteFooter';
import { Mail, Phone, MapPin } from 'lucide-react';

export default function Contact() {
    return (
        <>
            <Head title="Contact Us — Syscend Campus" />
            <SiteHeader />
            <main className="bg-background pt-24 pb-20">
                <div className="mx-auto max-w-4xl px-6 lg:px-10">
                    <p className="font-mono text-xs uppercase tracking-[0.2em] text-primary">Get in Touch</p>
                    <h1 className="mt-4 font-serif text-4xl font-medium tracking-tight sm:text-5xl">
                        Contact us
                    </h1>
                    <p className="mt-6 text-lg leading-relaxed text-muted-foreground">
                        Have a question, want a demo, or need support? We&apos;d love to hear from you.
                    </p>

                    <div className="mt-12 grid gap-8 sm:grid-cols-3">
                        {[
                            { icon: Mail, label: 'Email', value: 'syscend@gmail.com', href: 'mailto:syscend@gmail.com' },
                            { icon: Phone, label: 'Phone / WhatsApp', value: '+232 79 630 777', href: 'tel:+23279630777' },
                            { icon: MapPin, label: 'Location', value: 'Freetown, Sierra Leone', href: '#' },
                        ].map((item) => (
                            <div key={item.label} className="rounded-md border border-border bg-card p-6">
                                <span className="grid size-10 place-items-center rounded-sm bg-primary/10 text-primary">
                                    <item.icon className="size-5" />
                                </span>
                                <h3 className="mt-4 text-sm font-medium">{item.label}</h3>
                                <a href={item.href} className="mt-1 block text-sm text-muted-foreground hover:text-primary transition-colors">
                                    {item.value}
                                </a>
                            </div>
                        ))}
                    </div>

                    <section className="mt-16">
                        <h2 className="font-serif text-2xl font-medium">Send us a message</h2>
                        <p className="mt-3 text-muted-foreground">
                            Fill out the form below and our team will get back to you within 24 hours.
                        </p>
                        <form className="mt-6 space-y-4" onSubmit={(e) => e.preventDefault()}>
                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="space-y-1.5">
                                    <label htmlFor="name" className="text-sm font-medium text-foreground">Name</label>
                                    <input id="name" type="text" placeholder="Your name" className="h-10 w-full rounded-sm border border-border bg-card px-3 text-sm outline-none focus:ring-2 focus:ring-primary/30" />
                                </div>
                                <div className="space-y-1.5">
                                    <label htmlFor="email" className="text-sm font-medium text-foreground">Email</label>
                                    <input id="email" type="email" placeholder="you@example.com" className="h-10 w-full rounded-sm border border-border bg-card px-3 text-sm outline-none focus:ring-2 focus:ring-primary/30" />
                                </div>
                            </div>
                            <div className="space-y-1.5">
                                <label htmlFor="subject" className="text-sm font-medium text-foreground">Subject</label>
                                <input id="subject" type="text" placeholder="How can we help?" className="h-10 w-full rounded-sm border border-border bg-card px-3 text-sm outline-none focus:ring-2 focus:ring-primary/30" />
                            </div>
                            <div className="space-y-1.5">
                                <label htmlFor="message" className="text-sm font-medium text-foreground">Message</label>
                                <textarea id="message" rows={5} placeholder="Tell us more..." className="w-full rounded-sm border border-border bg-card px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/30 resize-none" />
                            </div>
                            <button type="submit" className="inline-flex h-10 items-center justify-center rounded-sm bg-primary px-6 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90">
                                Send message
                            </button>
                        </form>
                    </section>
                </div>
            </main>
            <SiteFooter />
        </>
    );
}
