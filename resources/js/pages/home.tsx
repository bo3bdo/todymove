import { Head, Link } from '@inertiajs/react';
import { archive, home, watch } from '@/routes';
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

function MovieCard({ movie }: { movie: MovieSummary }) {
    return (
        <Link
            href={watch.url(movie.id)}
            className="group block overflow-hidden rounded-lg border border-border bg-card shadow-sm transition hover:shadow-md"
        >
            <div className="aspect-[2/3] overflow-hidden bg-muted">
                {movie.poster_url ? (
                    <img
                        src={movie.poster_url}
                        alt={movie.title}
                        className="size-full object-cover transition group-hover:scale-105"
                    />
                ) : (
                    <div className="flex size-full items-center justify-center text-muted-foreground">
                        No poster
                    </div>
                )}
            </div>
            <div className="p-3">
                <h3 className="font-semibold text-foreground line-clamp-2 group-hover:underline">
                    {movie.title}
                </h3>
                <div className="mt-1 flex items-center gap-2 text-sm text-muted-foreground">
                    {movie.release_date && <span>{movie.release_date}</span>}
                    {movie.vote_average != null && (
                        <span>★ {Number(movie.vote_average).toFixed(1)}</span>
                    )}
                </div>
            </div>
        </Link>
    );
}

export default function Home({ filmOfWeek = [], archivePreview = [] }: Props) {
    return (
        <PublicLayout>
            <Head title="Film Night" />
            <div className="space-y-10">
                <section>
                    <h1 className="mb-2 text-2xl font-bold tracking-tight">
                        Film of the week
                    </h1>
                    <p className="mb-6 text-muted-foreground">
                        Up to four picks. Click a movie to watch.
                    </p>
                    {(filmOfWeek?.length ?? 0) > 0 ? (
                        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                            {filmOfWeek.map((movie) => (
                                <MovieCard key={movie.id} movie={movie} />
                            ))}
                        </div>
                    ) : (
                        <div className="rounded-lg border border-dashed border-border bg-muted/30 py-12 text-center text-muted-foreground">
                            No film of the week yet.
                        </div>
                    )}
                </section>

                <section>
                    <div className="mb-4 flex items-center justify-between">
                        <h2 className="text-xl font-semibold">Archive</h2>
                        <Link
                            href={archive().url}
                            className="text-sm font-medium text-primary hover:underline"
                        >
                            View all
                        </Link>
                    </div>
                    {(archivePreview?.length ?? 0) > 0 ? (
                        <div className="grid gap-6 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-4">
                            {archivePreview.map((movie) => (
                                <Link
                                    key={movie.id}
                                    href={watch.url(movie.id)}
                                    className="group block overflow-hidden rounded-lg border border-border bg-card shadow-sm transition hover:shadow-md"
                                >
                                    <div className="aspect-[2/3] overflow-hidden bg-muted">
                                        {movie.poster_url ? (
                                            <img
                                                src={movie.poster_url}
                                                alt={movie.title}
                                                className="size-full object-cover transition group-hover:scale-105"
                                            />
                                        ) : (
                                            <div className="flex size-full items-center justify-center text-muted-foreground">
                                                No poster
                                            </div>
                                        )}
                                    </div>
                                    <p className="truncate p-2 font-medium">
                                        {movie.title}
                                    </p>
                                </Link>
                            ))}
                        </div>
                    ) : (
                        <p className="text-muted-foreground">
                            No films in the archive yet.
                        </p>
                    )}
                </section>
            </div>
        </PublicLayout>
    );
}
