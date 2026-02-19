import { Head, Link } from '@inertiajs/react';
import { useCallback, useMemo, useRef, useState } from 'react';
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

type VideoSource = {
    id: number;
    label: string;
    url: string;
    type: string;
    quality: string | null;
    priority: number;
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
        video_sources: VideoSource[];
    };
};

export default function Watch({ movie }: Props) {
    const [currentSourceIndex, setCurrentSourceIndex] = useState(0);
    const [error, setError] = useState<string | null>(null);
    const videoRef = useRef<HTMLVideoElement>(null);

    const sources = movie.video_sources;
    const currentSource = sources[currentSourceIndex] ?? null;

    const tryNextSource = useCallback(() => {
        setError(null);
        if (currentSourceIndex < sources.length - 1) {
            setCurrentSourceIndex((i) => i + 1);
        } else {
            setError('This video could not be loaded.');
        }
    }, [currentSourceIndex, sources.length]);

    return (
        <PublicLayout>
            <Head title={`${movie.title} – Film Night`} />
            <div className="space-y-8">
                {/* Breadcrumb */}
                <div className="animate-fade-in opacity-0">
                    <Link
                        href={home().url}
                        className="group inline-flex items-center gap-2 text-sm text-muted-foreground transition-colors hover:text-foreground"
                    >
                        <svg
                            className="size-4 transition-transform duration-300 group-hover:-translate-x-1"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={1.5}
                                d="M15 19l-7-7 7-7"
                            />
                        </svg>
                        Back to films
                    </Link>
                </div>

                <div className="grid gap-8 lg:grid-cols-3">
                    {/* Video Player */}
                    <div
                        className="animate-fade-in-up opacity-0 lg:col-span-2"
                        style={{ animationDelay: '100ms' }}
                    >
                        <div className="cinematic-glow overflow-hidden rounded-sm border border-border/50 bg-black shadow-2xl">
                            {sources.length === 0 ? (
                                <div className="flex aspect-video flex-col items-center justify-center gap-3 bg-card">
                                    <svg
                                        className="size-12 text-muted-foreground/50"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={1}
                                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
                                        />
                                    </svg>
                                    <p className="text-muted-foreground">
                                        No video source available for this
                                        movie.
                                    </p>
                                </div>
                            ) : (
                                <>
                                    <video
                                        ref={videoRef}
                                        key={currentSource?.id}
                                        className="aspect-video w-full bg-black"
                                        controls
                                        poster={movie.poster_url ?? undefined}
                                        onError={tryNextSource}
                                        src={currentSource?.url}
                                    >
                                        <source
                                            src={currentSource?.url}
                                            type={
                                                currentSource?.type ||
                                                'video/mp4'
                                            }
                                        />
                                        Your browser does not support the video
                                        tag.
                                    </video>
                                    {error && (
                                        <div className="border-t border-destructive/30 bg-destructive/20 px-4 py-3 text-destructive">
                                            <span className="flex items-center gap-2">
                                                <svg
                                                    className="size-4"
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20"
                                                >
                                                    <path
                                                        fillRule="evenodd"
                                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                        clipRule="evenodd"
                                                    />
                                                </svg>
                                                {error}
                                            </span>
                                        </div>
                                    )}
                                </>
                            )}
                        </div>

                        {sources.length > 1 && (
                            <div className="mt-4">
                                <label className="mb-2 block text-xs font-medium tracking-wider text-muted-foreground uppercase">
                                    Video Source
                                </label>
                                <select
                                    className="w-full rounded-sm border border-border bg-card px-4 py-3 text-sm text-foreground transition-all duration-300 focus:border-primary/50 focus:ring-1 focus:ring-primary/30 focus:outline-none"
                                    value={currentSourceIndex}
                                    onChange={(e) => {
                                        setError(null);
                                        setCurrentSourceIndex(
                                            Number(e.target.value),
                                        );
                                    }}
                                >
                                    {sources.map((src, i) => (
                                        <option key={src.id} value={i}>
                                            {src.label}
                                            {src.quality
                                                ? ` (${src.quality})`
                                                : ''}
                                        </option>
                                    ))}
                                </select>
                            </div>
                        )}
                    </div>

                    {/* Movie Info */}
                    <div
                        className="animate-fade-in-up space-y-6 opacity-0 lg:col-span-1"
                        style={{ animationDelay: '200ms' }}
                    >
                        {movie.poster_url && (
                            <div className="relative max-w-[180px]">
                                <div className="absolute -inset-2 rounded-sm bg-primary/10 blur-xl" />
                                <img
                                    src={movie.poster_url}
                                    alt={movie.title}
                                    className="relative w-full rounded-sm border border-border/50 object-cover shadow-xl"
                                />
                            </div>
                        )}

                        <div>
                            <div className="mb-3 h-px w-16 bg-primary/40" />
                            <h1 className="font-display text-3xl leading-tight font-semibold tracking-wide text-foreground sm:text-4xl">
                                {movie.title}
                            </h1>
                            <div className="mt-4 flex flex-wrap items-center gap-4 text-sm text-muted-foreground">
                                {movie.release_date && (
                                    <span className="flex items-center gap-1.5">
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
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                            />
                                        </svg>
                                        {movie.release_date}
                                    </span>
                                )}
                                {movie.runtime_minutes != null && (
                                    <span className="flex items-center gap-1.5">
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
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                        {movie.runtime_minutes} min
                                    </span>
                                )}
                                {movie.vote_average != null && (
                                    <span className="flex items-center gap-1.5 font-medium text-amber-400">
                                        <svg
                                            className="size-4"
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

                        {movie.overview && (
                            <div>
                                <h3 className="mb-2 font-display text-lg font-medium text-foreground">
                                    Synopsis
                                </h3>
                                <p className="text-sm leading-relaxed text-muted-foreground">
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
