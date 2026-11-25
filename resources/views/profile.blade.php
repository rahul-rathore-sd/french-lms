@extends('layouts.app')

@section('title', 'Profile · TCF Canada Prep')

@section('content')
@php($st = $plan['stage'])
<h1 class="font-display text-4xl font-medium sm:text-5xl">Hi, <em>{{ $learner->name }}</em></h1>
<p class="mt-2 text-muted">Your own plan: day {{ $plan['day_number'] }}, {{ $st['level'] }} ({{ $st['nclc'] }}). Started {{ $learner->start_date->format('j M Y') }}{{ $learner->credit_hours > 0 ? ', with '.(int) $learner->credit_hours.' h already mastered' : '' }}. {{ $others }} other {{ $others === 1 ? 'person uses' : 'people use' }} this site, each with their own progress.</p>

<div class="mt-6 grid gap-4 md:grid-cols-2">
    <form data-ajax="/me/settings" class="panel grid gap-3 p-5">
        <h2 class="font-display text-xl font-semibold">Study time and voice</h2>
        <label class="grid gap-1 text-sm font-medium">Minutes per day
            <select name="minutes" class="rounded-xl border border-line bg-raised/50 px-4 py-3 text-base font-normal">
                @foreach ([60 => '1 hour', 90 => '1.5 hours', 120 => '2 hours', 180 => '3 hours'] as $m => $label)
                    <option value="{{ $m }}" {{ $learner->daily_minutes == $m ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label class="grid gap-1 text-sm font-medium">My wish: be ready in (months)
            <input name="wish_months" type="number" min="1" max="24" value="{{ $learner->wish_months }}" class="rounded-xl border border-line bg-raised/50 px-4 py-3 text-base font-normal">
        </label>
        <label class="grid gap-1 text-sm font-medium">Voice for listening
            <select name="voice" id="voice-select" class="rounded-xl border border-line bg-raised/50 px-4 py-3 text-base font-normal">
                <option value="mix" {{ $learner->voice === 'mix' ? 'selected' : '' }}>Mixed accents (Canada, France, Belgium, Switzerland): good exam practice</option>
                @foreach (collect($voices)->groupBy('accent') as $accent => $group)
                    <optgroup label="{{ $accent }}{{ $accent === 'Canada' ? ' (TCF Canada)' : '' }}">
                        @foreach ($group as $id => $v)
                            <option value="{{ $id }}" {{ $learner->voice === $id ? 'selected' : '' }}>{{ $v['label'] }} ({{ $v['gender'] === 'F' ? 'woman' : 'man' }}){{ $id === 'fr-CA-SylvieNeural' ? ' ★ default' : '' }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
                <option value="browser" {{ $learner->voice === 'browser' ? 'selected' : '' }}>My browser voice (no server audio)</option>
            </select>
        </label>
        <button type="button" class="btn btn-sm justify-self-start" data-voice-sample="Bonjour ! Comment allez-vous aujourd’hui ? J’apprends le français pour vivre au Canada." data-target="#voice-select">▶ Listen to a sample</button>
        <p class="text-xs text-muted">Dialogues are read by two different voices. Pick the one you find easiest to understand; "Mixed accents" prepares you for the different speakers in the real test.</p>
        <p class="hidden text-sm" data-error role="alert"></p>
        <button class="btn btn-primary" type="submit">Save</button>
    </form>

    <form data-ajax="/me/pin" class="panel grid gap-3 p-5">
        <h2 class="font-display text-xl font-semibold">Change PIN</h2>
        <label class="grid gap-1 text-sm font-medium">Current PIN
            <input name="current" type="password" inputmode="numeric" pattern="\d{4,8}" required maxlength="8" autocomplete="current-password" class="rounded-xl border border-line bg-raised/50 px-4 py-3 text-base font-normal tracking-widest">
        </label>
        <label class="grid gap-1 text-sm font-medium">New PIN (6 to 8 digits)
            <input name="new" type="password" inputmode="numeric" pattern="\d{6,8}" required maxlength="8" autocomplete="new-password" class="rounded-xl border border-line bg-raised/50 px-4 py-3 text-base font-normal tracking-widest">
        </label>
        <p class="hidden text-sm" data-error role="alert"></p>
        <button class="btn btn-primary" type="submit">Change PIN</button>
    </form>

    <form data-ajax="/me/restart" class="panel grid gap-3 p-5 md:col-span-2" data-confirm="Restart your plan from today? Your streak and flashcards stay, only the plan day restarts.">
        <h2 class="font-display text-xl font-semibold">Restart my plan from today</h2>
        <p class="text-sm text-muted">Use this if the level check shows you are further ahead than A1. Choose your real level and the plan starts again at day 1 from there. Your cards and streak are kept.</p>
        <div class="flex flex-wrap items-end gap-3">
            <label class="grid gap-1 text-sm font-medium">Level
                <select name="level" class="rounded-xl border border-line bg-raised/50 px-4 py-3 text-base font-normal">
                    @foreach (['A1', 'A2', 'B1', 'B2'] as $lv)<option value="{{ $lv }}" {{ $lv === $st['level'] ? 'selected' : '' }}>{{ $lv }}</option>@endforeach
                </select>
            </label>
            <button class="btn" type="submit">Restart plan</button>
        </div>
        <p class="hidden text-sm" data-error role="alert"></p>
    </form>
</div>

<div class="mt-8 flex flex-wrap gap-3">
    <button type="button" class="btn" data-post="/logout">Log out</button>
</div>
@endsection
