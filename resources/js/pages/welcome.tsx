import { dashboard, login } from '@/routes';
import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

export default function Welcome() {
    const { auth } = usePage<SharedData>().props;

    return (
        <>
            <Head title="Welcome">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
            </Head>
            <div className="flex min-h-screen flex-col items-center lg:justify-center lg:p-8">
                <header className="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl">
                    <nav className="flex items-center justify-end gap-4">
                        {auth.user ? (
                            <Link
                                href={dashboard()}
                                className="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                            >
                                Dashboard
                            </Link>
                        ) : (
                            <>
                                <Link
                                    href={login()}
                                    className="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                                >
                                    Inloggen
                                </Link>
                            </>
                        )}
                    </nav>
                </header>
                <main className="flex-1">
                    <section className="w-full py-6 sm:py-12 md:py-24 lg:py-32 xl:py-48">
                        <div className="container px-4 md:px-6">
                            <div className="grid gap-6 lg:grid-cols-[1fr_400px] lg:gap-12 xl:grid-cols-[1fr_600px]">
                                <div className="bg-neutral-100 dark:bg-neutral-800 mx-auto aspect-video overflow-hidden rounded-xl object-cover sm:w-full lg:order-last lg:aspect-square" />
                                <div className="flex flex-col justify-center space-y-4">
                                    <div className="space-y-2">
                                        <h1 className="text-3xl font-bold tracking-tighter sm:text-5xl xl:text-6xl/none">
                                            Bestel je drankjes met gemak!
                                        </h1>

                                        <p className="max-w-[600px] text-neutral-500 md:text-xl dark:text-neutral-400">
                                            Van een lauwe blauwe tot een goudgele rakker, dit is de app om je drankjes te bestellen.
                                        </p>
                                    </div>
                                    <div className="flex flex-col gap-2 min-[400px]:flex-row">
                                        <Link
                                            className="inline-flex h-10 items-center justify-center rounded-md bg-neutral-900 px-8 text-sm font-medium text-neutral-50 shadow transition-colors hover:bg-neutral-900/90 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-neutral-950 disabled:pointer-events-none disabled:opacity-50 dark:bg-neutral-50 dark:text-neutral-900 dark:hover:bg-neutral-50/90 dark:focus-visible:ring-neutral-300"
                                            href="/sign-in"
                                        >
                                            Ga naar Inloggen
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section id="features" className="w-full py-12 md:py-24 lg:py-32">
                        <div className="container px-4 md:px-6">
                            <div className="flex flex-col items-center justify-center space-y-4 text-center">
                                <div className="space-y-2">
                                    <div className="inline-block rounded-lg bg-neutral-100 px-3 py-1 text-sm dark:bg-neutral-800">
                                        Key Features
                                    </div>
                                    <h2 className="text-3xl font-bold tracking-tighter md:text-4xl/tight">
                                        Sneller afrekenen, sneller drinken.
                                    </h2>
                                    <p className="max-w-[900px] text-neutral-500 md:text-xl/relaxed lg:text-base/relaxed xl:text-xl/relaxed dark:text-neutral-400">
                                        De app om je drankjes met af te rekenen. Met een paar drukken ben je al aan het genieten van je drankje.
                                    </p>
                                </div>
                            </div>
                            <div className="mx-auto grid max-w-5xl items-center gap-6 py-12 lg:grid-cols-2 lg:gap-10">
                                <div className="mx-auto aspect-video overflow-hidden bg-neutral-100 dark:bg-neutral-800 rounded-xl object-cover object-center sm:w-full lg:order-last" />
                                <div className="flex flex-col justify-center space-y-4">
                                    <ul className="grid gap-6">
                                        <li>
                                            <div className="grid gap-1">
                                                <h3 className="text-xl font-bold">Gemak</h3>
                                                <p className="text-neutral-500 dark:text-neutral-400">
                                                    Maak vanaf de bank en keuze en reken daar al af, je hoeft alleen nog maar je drankje op te halen.
                                                </p>
                                            </div>
                                        </li>
                                        <li>
                                            <div className="grid gap-1">
                                                <h3 className="text-xl font-bold">Voor anderen</h3>
                                                <p className="text-neutral-500 dark:text-neutral-400">
                                                    Ben je zo aardig om voor andere een drankje te pakken, dan kan je dat super snel regelen met de app
                                                </p>
                                            </div>
                                        </li>
                                        <li>
                                            <div className="grid gap-1">
                                                <h3 className="text-xl font-bold">Nieuwe features</h3>
                                                <p className="text-neutral-500 dark:text-neutral-400">
                                                    De app wordt ook bijgewerkt met steeds nieuwe features
                                                </p>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </section>
                </main>
                <footer className="flex flex-col gap-2 sm:flex-row py-6 w-full shrink-0 items-center px-4 md:px-6 border-t">
                    <p className="text-xs text-neutral-500 dark:text-neutral-400">
                        © {new Date().getFullYear()} De bar app. All rights reserved.
                    </p>
                    <nav className="sm:ml-auto flex gap-4 sm:gap-6">
                        <Link className="text-xs hover:underline underline-offset-4" href="#">
                            Terms of Service
                        </Link>
                        <Link className="text-xs hover:underline underline-offset-4" href="#">
                            Privacy
                        </Link>
                    </nav>
                </footer>
            </div>
        </>
    );
}
