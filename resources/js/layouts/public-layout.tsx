import { usePage } from '@inertiajs/react';
import { useState, useEffect } from 'react';

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
        <div className="min-h-screen bg-background text-foreground">
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

            <main className="min-h-screen">
                {children}
            </main>
        </div>
    );
}
