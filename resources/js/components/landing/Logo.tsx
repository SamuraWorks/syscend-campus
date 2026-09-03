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
            <span className="inline-flex h-12 w-12 items-center justify-center overflow-hidden rounded-md bg-white p-1.5 shadow-sm ring-1 ring-black/10">
                <img
                    src="/images/logo.png"
                    alt="Syscend Campus"
                    className="h-full w-full object-contain [filter:none]"
                    draggable={false}
                />
            </span>
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
