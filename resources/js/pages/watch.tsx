import { Head, Link } from '@inertiajs/react';
import { useMemo } from 'react';
import { home } from '@/routes';
import {
    Dialog,
    DialogContent,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import PublicLayout from '@/layouts/public-layout';

function getYoutubeEmbedUrl(url: string): string | null {
    try {
        const u = new URL(url);
        const id =
            u.hostname === 'youtu.be'
                ? u.pathname.slice(1)
                : u.searchParams.get('v');
        return id ? `https://www.youtube.com/embed/${id}?autoplay=1` : null;
    } catch {
        return null;
    }
}

function TrailerModal({
    trailerUrl,
    movieTitle,
}: {
    trailerUrl: string;
    movieTitle: string;
}) {
    const embedUrl = useMemo(
        () => getYoutubeEmbedUrl(trailerUrl),
        [trailerUrl],
    );

    if (!embedUrl) {
        return (
            <a
                href={trailerUrl}
                target="_blank"
                rel="noopener noreferrer"
                className="inline-flex items-center gap-2 rounded-sm border border-border bg-card px-4 py-2 text-sm font-medium text-foreground transition-all duration-300 hover:border-primary/50 hover:bg-primary/5"
            >
                <svg
                    className="size-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth={1.5}
                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"
                    />
                    <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth={1.5}
                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
                Watch trailer
            </a>
        );
    }

    return (
        <Dialog>
            <DialogTrigger asChild>
                <button
                    type="button"
                    className="inline-flex items-center gap-2 rounded-sm border border-border bg-card px-4 py-2 text-sm font-medium text-foreground transition-all duration-300 hover:border-primary/50 hover:bg-primary/5"
                >
                    <svg
                        className="size-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={1.5}
                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"
                        />
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={1.5}
                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    Watch trailer
                </button>
            </DialogTrigger>
            <DialogContent className="max-w-5xl border-none bg-black p-0 shadow-2xl">
                <DialogTitle className="sr-only">
                    {movieTitle} – Trailer
                </DialogTitle>
                <div className="aspect-video w-full overflow-hidden rounded-sm">
                    <iframe
                        src={embedUrl}
                        title={`${movieTitle} trailer`}
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowFullScreen
                        className="size-full"
                    />
                </div>
            </DialogContent>
        </Dialog>
    );
}

type WatchLink = {
    id: number;
    label: string;
    url: string;
};

type Props = {
    movie: {
        id: number;
        title: string;
        overview: string | null;
        poster_url: string | null;
        trailer_url: string | null;
        release_date: string | null;
        runtime_minutes: number | null;
        vote_average: string | number | null;
        watch_links: WatchLink[];
    };
};

export default function Watch({ movie }: Props) {
    const watchLinks = movie.watch_links ?? [];

    return (
        <PublicLayout>
            <Head title={`${movie.title} – Film Night`} />
            <div className="space-y-8 px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
                {/* Breadcrumb */}
                <div className="animate-fade-in opacity-0">
                    <Link
                        href={home().url}
                        className="group inline-flex items-center gap-2 rounded-sm border border-border bg-card px-3 py-2 text-sm font-medium text-muted-foreground shadow-sm transition-all duration-300 hover:border-primary/40 hover:bg-primary/5 hover:text-foreground"
                    >
                        <span className="flex h-6 w-6 items-center justify-center rounded-full border border-border bg-muted transition-transform duration-300 group-hover:-translate-x-0.5">
                            <svg
                                className="size-3"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M15 19l-7-7 7-7"
                                />
                            </svg>
                        </span>
                        <span>Back to films</span>
                    </Link>
                </div>

                <div className="grid gap-6 lg:grid-cols-3 lg:gap-8">
                    {/* Watch links */}
                    <div
                        className="animate-fade-in-up opacity-0 lg:col-span-2"
                        style={{ animationDelay: '100ms' }}
                    >
                        <div>
                            <h2 className="mb-3 font-display text-sm font-medium tracking-wide text-foreground lg:text-base">
                                أين تشاهد الفيلم
                            </h2>
                            {watchLinks.length === 0 ? (
                                <p className="rounded-sm border border-border/50 bg-card px-4 py-6 text-center text-sm text-muted-foreground">
                                    لا يوجد روابط مشاهدة لهذا الفيلم.
                                </p>
                            ) : (
                                <div className="flex flex-wrap gap-2">
                                    {watchLinks.map((link) => (
                                        <a
                                            key={link.id}
                                            href={link.url}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className="inline-flex items-center gap-2 rounded-sm border border-border bg-card px-4 py-2.5 text-sm font-medium text-foreground transition-all duration-300 hover:border-primary/50 hover:bg-primary/5 hover:text-primary"
                                        >
                                            <svg
                                                className="size-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                    strokeWidth={1.5}
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                                                />
                                            </svg>
                                            {link.label}
                                        </a>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Movie Info */}
                    <div
                        className="animate-fade-in-up space-y-4 opacity-0 lg:col-span-1 lg:space-y-6"
                        style={{ animationDelay: '200ms' }}
                    >
                        {/* Poster + title/meta: side-by-side on mobile, stacked on desktop */}
                        <div className="flex items-start gap-4 lg:flex-col lg:gap-0 lg:space-y-4">
                            {movie.poster_url && (
                                <div className="relative w-[110px] shrink-0 sm:w-[140px] lg:w-[180px]">
                                    <div className="absolute -inset-1 rounded-sm bg-primary/10 blur-xl lg:-inset-2" />
                                    <img
                                        src={movie.poster_url}
                                        alt={movie.title}
                                        className="relative w-full rounded-sm border border-border/50 object-cover shadow-lg lg:shadow-xl"
                                    />
                                </div>
                            )}

                            <div className="min-w-0 flex-1">
                                <div className="mb-2 h-px w-12 bg-primary/40 lg:mb-3 lg:w-16" />
                                <h1 className="font-display text-xl leading-tight font-semibold tracking-wide text-foreground sm:text-2xl lg:text-4xl">
                                    {movie.title}
                                </h1>
                                <div className="mt-2 flex flex-wrap items-center gap-2 text-xs text-muted-foreground lg:mt-4 lg:gap-4 lg:text-sm">
                                    {movie.release_date && (
                                        <span className="flex items-center gap-1">
                                            <svg
                                                className="size-3.5 lg:size-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                    strokeWidth={1.5}
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                />
                                            </svg>
                                            {movie.release_date}
                                        </span>
                                    )}
                                    {movie.runtime_minutes != null && (
                                        <span className="flex items-center gap-1">
                                            <svg
                                                className="size-3.5 lg:size-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                    strokeWidth={1.5}
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
                                            </svg>
                                            {movie.runtime_minutes} min
                                        </span>
                                    )}
                                    {movie.vote_average != null && (
                                        <span className="flex items-center gap-1 font-medium text-amber-400">
                                            <svg
                                                className="size-3.5 lg:size-4"
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                            >
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            {Number(movie.vote_average).toFixed(1)}
                                        </span>
                                    )}
                                </div>
                            </div>
                        </div>

                        {movie.overview && (
                            <div>
                                <h3 className="mb-1.5 font-display text-base font-medium text-foreground lg:mb-2 lg:text-lg">
                                    Synopsis
                                </h3>
                                <p className="text-xs leading-relaxed text-muted-foreground lg:text-sm">
                                    {movie.overview}
                                </p>
                            </div>
                        )}

                        {movie.trailer_url && (
                            <TrailerModal
                                trailerUrl={movie.trailer_url}
                                movieTitle={movie.title}
                            />
                        )}
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
