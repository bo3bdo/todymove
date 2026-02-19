import { Head, Link } from '@inertiajs/react';
import { useCallback, useRef, useState } from 'react';
import { home } from '@/routes';
import PublicLayout from '@/layouts/public-layout';

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
            <div className="space-y-6">
                <div className="flex items-center gap-2 text-sm text-muted-foreground">
                    <Link href={home().url} className="hover:text-foreground">
                        Home
                    </Link>
                    <span>/</span>
                    <span className="text-foreground">{movie.title}</span>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    <div className="lg:col-span-2">
                        <div className="overflow-hidden rounded-lg border border-border bg-black">
                            {sources.length === 0 ? (
                                <div className="flex aspect-video items-center justify-center bg-muted text-muted-foreground">
                                    No video source available for this movie.
                                </div>
                            ) : (
                                <>
                                    <video
                                        ref={videoRef}
                                        key={currentSource?.id}
                                        className="aspect-video w-full"
                                        controls
                                        poster={movie.poster_url ?? undefined}
                                        onError={tryNextSource}
                                        src={currentSource?.url}
                                    >
                                        <source
                                            src={currentSource?.url}
                                            type={currentSource?.type || 'video/mp4'}
                                        />
                                        Your browser does not support the
                                        video tag.
                                    </video>
                                    {error && (
                                        <div className="bg-destructive/10 px-4 py-3 text-destructive">
                                            {error}
                                        </div>
                                    )}
                                </>
                            )}
                        </div>

                        {sources.length > 1 && (
                            <div className="mt-3">
                                <label className="mb-1 block text-sm font-medium text-muted-foreground">
                                    Source
                                </label>
                                <select
                                    className="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
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

                    <div className="space-y-4">
                        {movie.poster_url && (
                            <img
                                src={movie.poster_url}
                                alt={movie.title}
                                className="w-full rounded-lg border border-border object-cover shadow sm:max-w-[220px]"
                            />
                        )}
                        <div>
                            <h1 className="text-2xl font-bold tracking-tight">
                                {movie.title}
                            </h1>
                            <div className="mt-1 flex gap-3 text-sm text-muted-foreground">
                                {movie.release_date && (
                                    <span>{movie.release_date}</span>
                                )}
                                {movie.runtime_minutes != null && (
                                    <span>{movie.runtime_minutes} min</span>
                                )}
                                {movie.vote_average != null && (
                                    <span>
                                        ★{' '}
                                        {Number(
                                            movie.vote_average,
                                        ).toFixed(1)}
                                    </span>
                                )}
                            </div>
                        </div>
                        {movie.overview && (
                            <p className="text-sm leading-relaxed text-muted-foreground">
                                {movie.overview}
                            </p>
                        )}
                        {movie.trailer_url && (
                            <a
                                href={movie.trailer_url}
                                target="_blank"
                                rel="noopener noreferrer"
                                className="inline-flex items-center gap-2 rounded-md border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-muted"
                            >
                                Watch trailer
                            </a>
                        )}
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
