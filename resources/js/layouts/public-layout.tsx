import { Link, usePage } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { archive, home } from '@/routes';

function isStandalone(): boolean {
    if (typeof window === 'undefined') return false;
    return (
        (window.navigator as unknown as { standalone?: boolean }).standalone ===
            true ||
        window.matchMedia('(display-mode: standalone)').matches
    );
}

function isIos(): boolean {
    if (typeof window === 'undefined') return false;
    return /iPad|iPhone|iPod/.test(navigator.userAgent);
}

export default function PublicLayout({
    children,
}: {
    children: React.ReactNode;
}) {
    const { auth } = usePage().props as { auth: { user: unknown } };
    const [installPrompt, setInstallPrompt] = useState<{
        prompt: () => Promise<void>;
    } | null>(null);
    const [installDismissed, setInstallDismissed] = useState(false);
    const [showIosHint, setShowIosHint] = useState(false);

    useEffect(() => {
        const handler = (e: Event) => {
            e.preventDefault();
            setInstallPrompt({
                prompt: () =>
                    (e as unknown as { prompt: () => Promise<void> }).prompt(),
            });
        };
        window.addEventListener('beforeinstallprompt', handler);
        return () => window.removeEventListener('beforeinstallprompt', handler);
    }, []);

    useEffect(() => {
        if (isIos() && !isStandalone()) {
            const key = 'filmnight-ios-hint-dismissed';
            if (!localStorage.getItem(key)) {
                setShowIosHint(true);
            }
        }
    }, []);

    const handleInstall = () => {
        if (installPrompt) {
            installPrompt.prompt();
            setInstallDismissed(true);
            setInstallPrompt(null);
        }
    };

    const dismissIosHint = () => {
        setShowIosHint(false);
        localStorage.setItem('filmnight-ios-hint-dismissed', '1');
    };

    return (
        <div className="min-h-screen bg-background text-foreground">
            <header className="sticky top-0 z-10 border-b border-border bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
                <nav className="mx-auto flex h-14 max-w-6xl items-center justify-between px-4 sm:px-6">
                    <Link
                        href={home().url}
                        className="text-lg font-semibold text-foreground hover:opacity-80"
                    >
                        Film Night
                    </Link>
                    <div className="flex items-center gap-6">
                        <Link
                            href={home().url}
                            className="text-sm text-muted-foreground hover:text-foreground"
                        >
                            Home
                        </Link>
                        <Link
                            href={archive().url}
                            className="text-sm text-muted-foreground hover:text-foreground"
                        >
                            Archive
                        </Link>
                        {installPrompt && !installDismissed && (
                            <button
                                type="button"
                                onClick={handleInstall}
                                className="rounded-md bg-primary px-3 py-1.5 text-sm font-medium text-primary-foreground hover:opacity-90"
                            >
                                Install app
                            </button>
                        )}
                        {auth?.user && (
                            <a
                                href="/admin"
                                className="text-sm text-muted-foreground hover:text-foreground"
                            >
                                Admin
                            </a>
                        )}
                    </div>
                </nav>
            </header>

            {showIosHint && (
                <div className="border-b border-border bg-muted/50 px-4 py-2 text-center text-sm text-muted-foreground">
                    <span>
                        Add Film Night to your Home Screen: tap the share
                        button
                        <span className="mx-1 inline-block align-middle font-bold">
                            ⎋
                        </span>
                        then &quot;Add to Home Screen&quot;.
                    </span>
                    <button
                        type="button"
                        onClick={dismissIosHint}
                        className="ml-2 font-medium text-foreground hover:underline"
                        aria-label="Dismiss"
                    >
                        Dismiss
                    </button>
                </div>
            )}

            <main className="mx-auto max-w-6xl px-4 py-8 sm:px-6">
                {children}
            </main>
        </div>
    );
}
