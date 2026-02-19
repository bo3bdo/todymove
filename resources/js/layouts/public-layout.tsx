import { usePage } from '@inertiajs/react';
import { useState, useEffect } from 'react';

function isStandalone(): boolean {
    if (typeof window === 'undefined') return false;
    return (
        (window.navigator as unknown as { standalone?: boolean }).standalone ===
            true || window.matchMedia('(display-mode: standalone)').matches
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
    usePage().props;
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
        <div className="relative min-h-screen bg-background text-foreground">
            {/* Film grain texture overlay */}
            <div
                className="pointer-events-none fixed inset-0 z-[9998] opacity-[0.035] mix-blend-overlay"
                style={{
                    backgroundImage: `url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E")`,
                }}
            />

            {/* Vignette effect */}
            <div className="vignette pointer-events-none fixed inset-0 z-[9997]" />

            {/* iOS Install Hint */}
            {showIosHint && (
                <div className="relative z-50 border-b border-border/50 bg-card/80 px-4 py-3 text-center text-sm text-muted-foreground backdrop-blur-sm">
                    <span className="flex items-center justify-center gap-2">
                        Add Film Night to your Home Screen: tap the share button
                        <span className="inline-block align-middle font-bold text-primary">
                            ⎋
                        </span>
                        then "Add to Home Screen".
                    </span>
                    <button
                        type="button"
                        onClick={dismissIosHint}
                        className="ml-3 font-medium text-foreground transition-colors hover:text-primary"
                        aria-label="Dismiss"
                    >
                        Dismiss
                    </button>
                </div>
            )}

            <main className="relative min-h-screen">{children}</main>
        </div>
    );
}
