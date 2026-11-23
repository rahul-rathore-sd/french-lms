@extends('layouts.app')

@section('title', 'Log in · TCF Canada Prep')

@section('content')
<div class="mx-auto max-w-md pt-6">
    <h1 class="font-display text-4xl font-medium">Bon<em>jour</em></h1>
    <p class="mt-2 text-muted">Log in with your name and PIN. Your progress, streak and flashcards follow you on every device.</p>

    <form data-ajax="/login" class="panel mt-6 grid gap-4 p-5 sm:p-6" autocomplete="on">
        <input type="hidden" name="next" value="{{ $next }}">
        <label class="grid gap-1 text-sm font-medium">Name
            <input name="name" required maxlength="30" autocomplete="username" class="rounded-xl border border-line bg-raised/50 px-4 py-3 text-base font-normal" autofocus>
        </label>
        <label class="grid gap-1 text-sm font-medium">PIN
            <input name="pin" type="password" inputmode="numeric" pattern="\d{4,8}" required maxlength="8" autocomplete="current-password" class="rounded-xl border border-line bg-raised/50 px-4 py-3 text-base font-normal tracking-widest">
        </label>
        <p class="hidden text-sm text-danger" data-error role="alert"></p>
        <button type="submit" class="btn btn-primary">Log in</button>
    </form>

    <p class="mt-5 text-center text-sm text-muted">New here? <a href="/register" class="text-accent underline">Create your profile</a></p>
</div>
@endsection
