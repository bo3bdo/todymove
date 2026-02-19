import { Head, Link } from '@inertiajs/react';
import { home, watch } from '@/routes';
import PublicLayout from '@/layouts/public-layout';

type MovieSummary = {
    id: number;
    title: string;
    poster_url: string | null;
    release_date?: string | null;
    vote_average?: string | number | null;
};

type PaginatedData<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
};

type Props = {
    movies: PaginatedData<MovieSummary>;
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

export default function Archive({ movies }: Props) {
    const { data, last_page, links } = movies;

    return (
        <PublicLayout>
            <Head title="Archive – Film Night" />
            <div className="space-y-8">
                <div>
                    <Link
                        href={home().url}
                        className="text-sm text-muted-foreground hover:text-foreground"
                    >
                        ← Home
                    </Link>
                    <h1 className="mt-2 text-2xl font-bold tracking-tight">
                        Archive
                    </h1>
                    <p className="mt-1 text-muted-foreground">
                        All films. Click to watch.
                    </p>
                </div>

                {data.length > 0 ? (
                    <>
                        <div className="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                            {data.map((movie) => (
                                <MovieCard key={movie.id} movie={movie} />
                            ))}
                        </div>

                        {last_page > 1 && (
                            <nav
                                className="flex flex-wrap items-center justify-center gap-2"
                                aria-label="Pagination"
                            >
                                {links.map((link, i) => (
                                    <Link
                                        key={i}
                                        href={link.url ?? '#'}
                                        className={`inline-flex min-w-[2.5rem] items-center justify-center rounded-md border px-3 py-1.5 text-sm ${
                                            link.active
                                                ? 'border-primary bg-primary text-primary-foreground'
                                                : 'border-border bg-background hover:bg-muted'
                                        } ${!link.url ? 'pointer-events-none opacity-50' : ''}`}
                                        preserveScroll
                                    >
                                        {link.label
                                            .replace('&laquo;', '«')
                                            .replace('&raquo;', '»')}
                                    </Link>
                                ))}
                            </nav>
                        )}
                    </>
                ) : (
                    <p className="text-muted-foreground">
                        No films yet. Add movies from the admin.
                    </p>
                )}
            </div>
        </PublicLayout>
    );
}
