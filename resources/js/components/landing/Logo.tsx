import { cn } from '@/lib/utils';

export default function Logo({
    className,
    showWordmark = true,
    tone = 'dark',
}: {
    className?: string;
    showWordmark?: boolean;
    tone?: 'dark' | 'light';
}) {
    const light = tone === 'light';

    return (
        <span className={cn('inline-flex items-center gap-2', className)}>
            <img
                src="/images/logo.jpeg"
                alt="Syscend Campus"
                className="size-10 rounded-md object-cover"
            />
            {showWordmark && (
                <span
                    className={cn(
                        'text-xl font-bold tracking-tight',
                        light ? 'text-white' : 'text-foreground',
                    )}
                >
                    Syscend Campus
                </span>
            )}
        </span>
    );
}
