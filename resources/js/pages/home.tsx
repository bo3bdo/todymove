import { Head, Link } from '@inertiajs/react';
import { archive, watch } from '@/routes';
import PublicLayout from '@/layouts/public-layout';

type MovieSummary = {
    id: number;
    title: string;
    poster_url: string | null;
    trailer_url?: string | null;
    release_date?: string | null;
    vote_average?: string | number | null;
};

type Props = {
    filmOfWeek?: MovieSummary[];
    archivePreview?: { id: number; title: string; poster_url: string | null }[];
};

function FilmOfWeekCard({ movie }: { movie: MovieSummary }) {
    return (
        <Link
            href={watch.url(movie.id)}
            className="group mx-auto flex w-full max-w-[220px] flex-col overflow-hidden rounded-2xl bg-card shadow-md ring-1 ring-black/5 transition hover:-translate-y-1 hover:shadow-xl sm:max-w-[260px] dark:ring-white/5"
        >
            <div className="aspect-[2/3] w-full overflow-hidden bg-muted">
                {movie.poster_url ? (
                    <img
                        src={movie.poster_url}
                        alt={movie.title}
                        className="size-full object-cover transition duration-300 group-hover:scale-[1.03]"
                    />
                ) : (
                    <div className="flex size-full items-center justify-center text-muted-foreground text-sm">
                        No poster
                    </div>
                )}
            </div>
            <div className="p-3 sm:p-4">
                <h3 className="font-semibold text-foreground line-clamp-2 text-[15px] leading-snug transition group-hover:text-primary sm:text-base">
                    {movie.title}
                </h3>
                <div className="mt-2 flex items-center gap-2 text-xs text-muted-foreground">
                    {movie.release_date && <span>{movie.release_date}</span>}
                    {movie.vote_average != null && (
                        <span className="font-medium text-amber-600 dark:text-amber-400">
                            ★ {Number(movie.vote_average).toFixed(1)}
                        </span>
                    )}
                </div>
            </div>
        </Link>
    );
}

function ArchiveCard({
    movie,
}: {
    movie: { id: number; title: string; poster_url: string | null };
}) {
    return (
        <Link
            href={watch.url(movie.id)}
            className="group block overflow-hidden rounded-xl bg-card shadow-sm ring-1 ring-black/5 transition hover:-translate-y-0.5 hover:shadow-lg dark:ring-white/5"
        >
            <div className="aspect-[2/3] overflow-hidden bg-muted">
                {movie.poster_url ? (
                    <img
                        src={movie.poster_url}
                        alt={movie.title}
                        className="size-full object-cover transition duration-300 group-hover:scale-[1.03]"
                    />
                ) : (
                    <div className="flex size-full items-center justify-center text-muted-foreground text-xs">
                        No poster
                    </div>
                )}
            </div>
            <p className="truncate px-3 py-2.5 text-sm font-medium text-foreground">
                {movie.title}
            </p>
        </Link>
    );
}

function filmOfWeekGridClass(count: number): string {
    if (count <= 0) return '';
    if (count === 1) return 'flex justify-center';
    if (count === 2) return 'grid grid-cols-2 gap-4 sm:gap-6 justify-items-center';
    if (count === 3) return 'grid grid-cols-3 gap-4 sm:gap-6 justify-items-center';
    return 'grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6 justify-items-center';
}

export default function Home({ filmOfWeek = [], archivePreview = [] }: Props) {
    return (
        <PublicLayout>
            <Head title="Film Night" />
            <div className="min-h-screen">
                {/* Hero */}
                <section className="bg-gradient-to-b from-muted/60 to-background px-4 pb-16 pt-14 sm:px-6 sm:pt-20">
                    <div className="mx-auto max-w-5xl">
                        <p className="text-center text-xs font-medium uppercase tracking-[0.2em] text-muted-foreground">
                            Featured
                        </p>
                        <h1 className="mt-2 text-center text-2xl font-bold tracking-tight text-foreground sm:text-3xl">
                            Film of the week
                        </h1>
                        <p className="mt-1 text-center text-sm text-muted-foreground">
                            Tap to watch
                        </p>

                        {(filmOfWeek?.length ?? 0) > 0 ? (
                            <div
                                className={`mt-10 ${filmOfWeekGridClass(filmOfWeek.length)}`}
                            >
                                {filmOfWeek.map((movie) => (
                                    <FilmOfWeekCard
                                        key={movie.id}
                                        movie={movie}
                                    />
                                ))}
                            </div>
                        ) : (
                            <div className="mt-10 rounded-2xl border border-dashed border-border bg-muted/30 py-14 text-center text-muted-foreground text-sm">
                                No film of the week yet.
                            </div>
                        )}
                    </div>
                </section>

                {/* Archive */}
                <section className="border-t border-border bg-background px-4 py-12 sm:px-6">
                    <div className="mx-auto max-w-5xl">
                        <div className="mb-6 flex items-center justify-between">
                            <h2 className="text-lg font-semibold text-foreground">
                                Archive
                            </h2>
                            <Link
                                href={archive().url}
                                className="text-sm font-medium text-primary underline-offset-4 hover:underline"
                            >
                                View all →
                            </Link>
                        </div>
                        {(archivePreview?.length ?? 0) > 0 ? (
                            <div className="grid grid-cols-2 gap-4 sm:gap-5 lg:grid-cols-4">
                                {archivePreview.map((movie) => (
                                    <ArchiveCard
                                        key={movie.id}
                                        movie={movie}
                                    />
                                ))}
                            </div>
                        ) : (
                            <p className="text-muted-foreground text-sm">
                                No films in the archive yet.
                            </p>
                        )}
                    </div>
                </section>
            </div>
        </PublicLayout>
    );
}
