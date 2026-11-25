@extends('layouts.app')

@section('title', 'Create your profile · TCF Canada Prep')

@section('content')
<div class="mx-auto max-w-md pt-6">
    <h1 class="font-display text-4xl font-medium">Create your <em>profile</em></h1>
    <p class="mt-2 text-muted">Everyone has their own plan, progress and flashcards. Choose a name and a PIN you will remember.</p>

    @if ($full)
        <p class="panel mt-6 p-5 text-sm">This site already has {{ $max }} profiles. Ask the owner to raise the limit.</p>
    @else
        <form data-ajax="/register" class="panel mt-6 grid gap-4 p-5 sm:p-6">
            <label class="grid gap-1 text-sm font-medium">Your name
                <input name="name" required minlength="2" maxlength="30" autocomplete="username" class="rounded-xl border border-line bg-raised/50 px-4 py-3 text-base font-normal" autofocus>
            </label>
            <label class="grid gap-1 text-sm font-medium">PIN (6 to 8 digits)
                <input name="pin" type="password" inputmode="numeric" pattern="\d{6,8}" required maxlength="8" autocomplete="new-password" class="rounded-xl border border-line bg-raised/50 px-4 py-3 text-base font-normal tracking-widest">
            </label>
            @if ($needsInvite)
                <label class="grid gap-1 text-sm font-medium">Invite code (from the site owner)
                    <input name="invite" required autocomplete="off" autocapitalize="characters" class="rounded-xl border border-line bg-raised/50 px-4 py-3 text-base font-normal tracking-widest">
                </label>
            @endif
            <label class="grid gap-1 text-sm font-medium">Where do you start?
                <select name="level" class="rounded-xl border border-line bg-raised/50 px-4 py-3 text-base font-normal">
                    <option value="A1" selected>A1: complete beginner</option>
                    <option value="A2">A2: I handle everyday basics</option>
                    <option value="B1">B1: I speak about familiar topics</option>
                    <option value="B2">B2: I need exam practice</option>
                </select>
            </label>
            <label class="grid gap-1 text-sm font-medium">Study time per day
                <select name="minutes" class="rounded-xl border border-line bg-raised/50 px-4 py-3 text-base font-normal">
                    <option value="60">1 hour</option>
                    <option value="90">1.5 hours</option>
                    <option value="120" selected>2 hours</option>
                    <option value="180">3 hours</option>
                </select>
            </label>
            <p class="hidden text-sm text-danger" data-error role="alert"></p>
            <button type="submit" class="btn btn-primary">Create profile</button>
        </form>
    @endif

    <p class="mt-5 text-center text-sm text-muted">Already have a profile? <a href="/login" class="text-accent underline">Log in</a></p>
</div>
@endsection
