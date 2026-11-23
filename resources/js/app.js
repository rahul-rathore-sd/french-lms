// TCF Canada Prep: tiny vanilla JS, no framework.
// Progress, streak and the flashcard schedule are kept per learner: they are cached in localStorage
// (namespaced by learner id, so two people can share one browser) and synced to the server, so they
// follow the learner to every device.

const $ = (s, r = document) => r.querySelector(s);
const $$ = (s, r = document) => [...r.querySelectorAll(s)];
const LS = {
    get(k, d) { try { const v = localStorage.getItem(k); return v ? JSON.parse(v) : d; } catch { return d; } },
    set(k, v) { try { localStorage.setItem(k, JSON.stringify(v)); } catch { /* private mode */ } },
};
const todayStr = () => new Date().toLocaleDateString('en-CA'); // local YYYY-MM-DD
const addDays = (d, n) => { const x = new Date(d + 'T00:00:00'); x.setDate(x.getDate() + n); return x.toLocaleDateString('en-CA'); };
const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

/* ------------------------------------------------------------ requests --- */
async function post(url, body = {}, method = 'POST') {
    try {
        const r = await fetch(url, {
            method, credentials: 'same-origin',
            headers: { 'X-Requested-With': 'fetch', 'Content-Type': 'application/json', Accept: 'application/json' },
            body: JSON.stringify(body),
        });
        const j = await r.json().catch(() => ({ ok: false, error: 'Unexpected response from the server.' }));
        if (r.status === 401 && !location.pathname.startsWith('/login')) location.href = '/login';
        return j;
    } catch { return { ok: false, error: 'No connection. Try again.' }; }
}

/* --------------------------------------------------- per-learner storage --- */
const meEl = $('#me');
const ME = meEl ? JSON.parse(meEl.textContent) : null;
const NS = ME ? `tcf.${ME.id}.` : 'tcf.';
const K = (k) => NS + k;
const dirty = () => LS.get(NS + 'dirty', {});
const timers = {};

function push(key) {
    const value = LS.get(K(key), null);
    if (!ME || value === null) return;
    post(`/me/state/${key}`, { value }, 'PUT').then((j) => {
        if (j.ok) { const d = dirty(); delete d[key]; LS.set(NS + 'dirty', d); }
    });
}
function save(key, value) {
    LS.set(K(key), value);
    if (!ME) return;
    const d = dirty(); d[key] = 1; LS.set(NS + 'dirty', d);
    clearTimeout(timers[key]);
    timers[key] = setTimeout(() => push(key), 700);
}
function flush() { Object.keys(dirty()).forEach((k) => { clearTimeout(timers[k]); push(k); }); }
document.addEventListener('visibilitychange', () => { if (document.visibilityState === 'hidden') flush(); });
addEventListener('pagehide', flush);

/** Server wins on load (unless this device has unsent changes); an empty server gets this browser's old data once. */
function hydrate() {
    if (!ME) return;
    for (const key of ME.keys || []) {
        const server = ME.state[key];
        if (dirty()[key]) { push(key); continue; }
        if (server !== undefined) { LS.set(K(key), server); continue; }
        const legacy = LS.get('tcf.' + key, null);                 // data from before profiles existed
        if (legacy && !LS.get('tcf.legacy.claimed', false)) { LS.set(K(key), legacy); LS.set('tcf.legacy.claimed', true); save(key, legacy); }
    }
}
hydrate();

/* ------------------------------------------------------------ progress --- */
const progress = () => {
    const p = LS.get(K('progress'), {});
    for (const k of ['done', 'days', 'checks']) if (!p[k] || Array.isArray(p[k])) p[k] = {}; // an empty {} can come back as []
    return p;
};
function markActive() {
    const p = progress();
    p.days[todayStr()] = true;
    save('progress', p);
    renderStreak();
}
function streak() {
    const days = progress().days;
    let d = todayStr(), n = 0;
    if (!days[d]) d = addDays(d, -1); // today may not be done yet
    while (days[d]) { n++; d = addDays(d, -1); }
    return n;
}
function renderStreak() { $$('[data-streak]').forEach((e) => (e.textContent = streak())); }

/* --------------------------------------------------------------- theme --- */
$$('[data-theme-set]').forEach((b) => b.addEventListener('click', () => {
    const t = b.dataset.themeSet;
    document.documentElement.dataset.theme = t;
    const m = $('meta[name=theme-color]');
    if (m) m.content = t === 'light' ? '#f8f6f1' : '#111219';
    try { localStorage.setItem('tcf.theme', t); } catch { /* ignore */ }
}));

/* ------------------------------------------------- forms and post buttons --- */
$$('form[data-ajax]').forEach((f) => f.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (f.dataset.confirm && !confirm(f.dataset.confirm)) return;
    const err = $('[data-error]', f), btn = $('[type=submit]', f);
    const show = (msg, ok) => { if (!err) return; err.textContent = msg; err.classList.remove('hidden', 'text-danger', 'text-success'); err.classList.add(ok ? 'text-success' : 'text-danger'); };
    err?.classList.add('hidden'); btn && (btn.disabled = true);
    const j = await post(f.dataset.ajax, Object.fromEntries(new FormData(f)));
    if (j.ok && j.redirect) { location.href = j.redirect; return; }
    show(j.ok ? j.message || 'Saved.' : j.error || 'Something went wrong.', j.ok);
    btn && (btn.disabled = false);
}));
$$('[data-post]').forEach((b) => b.addEventListener('click', async () => {
    if (b.dataset.confirm && !confirm(b.dataset.confirm)) return;
    flush();
    const j = await post(b.dataset.post, JSON.parse(b.dataset.body || '{}'));
    if (j.ok && j.redirect) location.href = j.redirect; else if (!j.ok) alert(j.error || 'Something went wrong.');
}));

/* ---------------------------------------------------------- speech (TTS) --- */
let voices = [];
const loadVoices = () => { voices = window.speechSynthesis ? speechSynthesis.getVoices() : []; };
if ('speechSynthesis' in window) { loadVoices(); speechSynthesis.onvoiceschanged = loadVoices; }

function speak(text, rate = 1) {
    if (!('speechSynthesis' in window)) return alert('Your browser has no text-to-speech.');
    speechSynthesis.cancel();
    const v = voices.find((x) => /^fr[-_](FR|CA)/i.test(x.lang)) || voices.find((x) => /^fr/i.test(x.lang));
    // long texts are read sentence by sentence (some browsers cut off long utterances)
    text.split(/(?<=[.!?…])\s+|\n+/).map((s) => s.trim()).filter(Boolean).forEach((s) => {
        const u = new SpeechSynthesisUtterance(s);
        u.lang = 'fr-FR'; u.rate = rate; if (v) u.voice = v;
        speechSynthesis.speak(u);
    });
}
document.addEventListener('click', (e) => {
    const b = e.target.closest('[data-speak]');
    if (!b) return;
    const src = b.dataset.speakFrom ? $(b.dataset.speakFrom)?.textContent : b.dataset.speak;
    if (b.dataset.stop === '1' && speechSynthesis.speaking) { speechSynthesis.cancel(); return; }
    if (src) speak(src, parseFloat(b.dataset.rate || '1'));
});

/* ----------------------------------------------------------------- quiz --- */
$$('[data-quiz]').forEach((quiz) => {
    const items = $$('.qz', quiz);
    const total = items.length;
    let answered = 0, right = 0;
    const score = $('[data-score]', quiz);
    items.forEach((qz) => {
        const correct = parseInt(qz.dataset.correct, 10);
        $$('.opt', qz).forEach((o) => o.addEventListener('click', () => {
            if (qz.dataset.done) return;
            qz.dataset.done = '1';
            const i = parseInt(o.dataset.i, 10);
            $$('.opt', qz).forEach((x) => { x.disabled = true; if (parseInt(x.dataset.i, 10) === correct) x.classList.add('ok'); });
            if (i !== correct) o.classList.add('bad'); else right++;
            $('.qz-explain', qz)?.removeAttribute('hidden');
            answered++;
            if (score) score.textContent = `${right} / ${total}` + (answered === total ? ` (${Math.round((right / total) * 100)}%)` : '');
            if (answered === total) { markActive(); quiz.dispatchEvent(new CustomEvent('quizdone', { detail: { right, total } })); }
        }));
    });
});

/* ---------------------------------------------------------- done / checks --- */
$$('[data-done]').forEach((b) => {
    const slug = b.dataset.done;
    const paint = () => { const d = !!progress().done[slug]; b.textContent = d ? '✓ Done' : 'Mark as done'; b.classList.toggle('btn-primary', !d); };
    paint();
    b.addEventListener('click', () => {
        const p = progress();
        if (p.done[slug]) delete p.done[slug]; else { p.done[slug] = todayStr(); p.days[todayStr()] = true; }
        save('progress', p); paint(); renderStreak();
    });
});
$$('[data-check]').forEach((c) => {
    const key = c.dataset.check, d = todayStr();
    c.checked = !!progress().checks[d]?.[key];
    c.addEventListener('change', () => {
        const p = progress();
        (p.checks[d] ||= {})[key] = c.checked;
        if (c.checked) p.days[d] = true;
        save('progress', p); renderStreak(); renderChecklist();
    });
});
function renderChecklist() {
    const boxes = $$('[data-check]'); if (!boxes.length) return;
    const n = boxes.filter((b) => b.checked).length;
    const out = $('[data-check-count]'); if (out) out.textContent = `${n} / ${boxes.length}`;
    const bar = $('[data-check-bar]'); if (bar) bar.style.width = `${(n / boxes.length) * 100}%`;
}
renderChecklist();
$$('[data-done-count]').forEach((e) => (e.textContent = Object.keys(progress().done).length));
renderStreak();

/* ------------------------------------------------ writing / speaking helpers --- */
$$('[data-wordcount]').forEach((ta) => {
    const out = $(ta.dataset.wordcount), min = parseInt(ta.dataset.min || '0', 10);
    const upd = () => {
        const n = (ta.value.trim().match(/\S+/g) || []).length;
        out.textContent = `${n} words` + (min ? ` (minimum ${min})` : '');
        out.className = n >= min ? 'text-success' : 'text-muted';
    };
    ta.addEventListener('input', upd); upd();
});
$$('[data-reveal]').forEach((b) => b.addEventListener('click', () => {
    const t = $(b.dataset.reveal); if (!t) return;
    t.toggleAttribute('hidden');
    b.textContent = t.hasAttribute('hidden') ? b.dataset.showText || 'Show' : b.dataset.hideText || 'Hide';
}));
$$('[data-timer]').forEach((box) => {
    const out = $('[data-time]', box), btn = $('button', box);
    let left = parseInt(box.dataset.timer, 10), t = null;
    const fmt = (s) => `${String(Math.floor(s / 60)).padStart(2, '0')}:${String(s % 60).padStart(2, '0')}`;
    out.textContent = fmt(left);
    btn.addEventListener('click', () => {
        if (t) { clearInterval(t); t = null; btn.textContent = 'Start'; return; }
        if (left <= 0) left = parseInt(box.dataset.timer, 10);
        btn.textContent = 'Pause';
        t = setInterval(() => {
            left--; out.textContent = fmt(Math.max(left, 0));
            if (left <= 0) { clearInterval(t); t = null; btn.textContent = 'Restart'; markActive(); }
        }, 1000);
    });
});

/* -------------------------------------------------------------- level check --- */
const lc = $('#level-check');
if (lc) {
    const out = $('#lc-result');
    const run = () => {
        const a1 = $$('[data-lc="a1"]:checked').length, a2 = $$('[data-lc="a2"]:checked').length;
        let msg;
        if (a1 < 4) msg = 'Start at A1: you are at the beginning, which is exactly where the plan starts.';
        else if (a2 < 4) msg = 'A1 is solid. Move to A2 material (passé composé, daily-life topics).';
        else msg = 'You can already do A2 things. Try the B1 grammar and the quiz below to confirm.';
        out.textContent = msg;
    };
    $$('input[type=checkbox]', lc).forEach((c) => c.addEventListener('change', run));
    run();
    $('[data-quiz]')?.addEventListener('quizdone', (e) => {
        const { right, total } = e.detail, pct = Math.round((right / total) * 100);
        $('#quiz-result').textContent = pct >= 80 ? 'Strong: you are above A1. Check the A2 list above.' : pct >= 50 ? 'Mixed: revise A1 basics, then continue to A2.' : 'Start at A1 and do the daily plan. That is normal.';
    });
}

/* --------------------------------------------------------------- word list --- */
const wordsEl = $('#words');
if (wordsEl) {
    const data = JSON.parse($('#deck-data').textContent);
    const list = $('#word-list'), q = $('#word-q'); let lvl = 'all';
    const render = () => {
        const s = q.value.trim().toLowerCase();
        const rows = data.filter((w) => (lvl === 'all' || w.lvl === lvl) && (!s || (w.w + ' ' + w.en + ' ' + (w.hi || '')).toLowerCase().includes(s)));
        $('#word-count').textContent = `${rows.length} words`;
        list.innerHTML = rows.slice(0, 300).map((w) => `<div class="panel flex items-start justify-between gap-3 p-4"><div class="min-w-0"><p class="fr text-xl font-semibold">${esc(w.w)} <span class="chip">${esc(w.lvl)}</span></p><p class="ipa">${esc(w.ipa || '')} ${esc(w.pos || '')}</p><p>${esc(w.en)}${w.hi ? ' · <span class="text-muted">' + esc(w.hi) + '</span>' : ''}</p><p class="mt-1 text-sm text-muted">${esc(w.ex || '')}</p></div><button class="btn btn-sm shrink-0" data-speak="${esc(w.w)}" aria-label="Listen">🔊</button></div>`).join('') || '<p class="text-muted">No words match.</p>';
    };
    q.addEventListener('input', render);
    $$('[data-lvl]').forEach((b) => b.addEventListener('click', () => { lvl = b.dataset.lvl; $$('[data-lvl]').forEach((x) => x.classList.toggle('chip-accent', x === b)); render(); }));
    render();
}

/* ------------------------------------------------------ flashcards (SM-2 lite) --- */
const deckEl = $('#deck');
if (deckEl) {
    const all = JSON.parse($('#deck-data').textContent);
    const levels = (deckEl.dataset.levels || 'A1').split(',');
    let state = LS.get(K('srs'), {});                // word -> {ease, interval, due, reps, lapses}
    if (Array.isArray(state)) state = {};
    const intro = LS.get(K('srs.new'), { date: '', n: 0 });
    if (intro.date !== todayStr()) { intro.date = todayStr(); intro.n = 0; }
    const newLimit = parseInt(deckEl.dataset.new || '10', 10);
    const key = (w) => w.w;

    const due = all.filter((w) => state[key(w)] && state[key(w)].due <= todayStr()).sort((a, b) => state[key(a)].due.localeCompare(state[key(b)].due));
    const fresh = all.filter((w) => !state[key(w)] && levels.includes(w.lvl)).slice(0, Math.max(0, newLimit - intro.n));
    let queue = [...due, ...fresh];
    const total0 = queue.length;
    let current = null, shown = false;

    const back = $('#card-back'), stage = $('#card-stage'), done = $('#deck-done');
    const show = () => { shown = true; back.removeAttribute('hidden'); $('#btn-show').setAttribute('hidden', ''); $('#grades').removeAttribute('hidden'); };
    function next() {
        shown = false; current = queue.shift();
        $('#left').textContent = `${queue.length + (current ? 1 : 0)} left`;
        if (!current) { stage.setAttribute('hidden', ''); done.removeAttribute('hidden'); $('#done-text').textContent = total0 ? `Session complete: ${total0} cards reviewed.` : 'Nothing due right now. Come back tomorrow, or raise the daily new-card limit.'; return; }
        back.setAttribute('hidden', ''); $('#grades').setAttribute('hidden', ''); $('#btn-show').removeAttribute('hidden');
        $('#c-word').textContent = current.w; $('#c-ipa').textContent = current.ipa || ''; $('#c-pos').textContent = current.pos || '';
        $('#c-lvl').textContent = current.lvl;
        $('#c-en').textContent = current.en; $('#c-hi').textContent = current.hi || '';
        $('#c-ex').textContent = current.ex || ''; $('#c-exen').textContent = current.exen || '';
        $('#c-listen').dataset.speak = current.w; $('#c-listen-ex').dataset.speak = current.ex || current.w;
        speak(current.w);
    }
    function grade(g) { // 1 again, 2 hard, 3 good, 4 easy
        if (!current || !shown) return;
        const k = key(current), isNew = !state[k];
        const s = state[k] || { ease: 2.5, interval: 0, reps: 0, lapses: 0 };
        if (g === 1) { s.lapses++; s.ease = Math.max(1.3, s.ease - 0.2); s.interval = 0; s.reps = 0; queue.push(current); }
        else {
            if (g === 2) { s.ease = Math.max(1.3, s.ease - 0.15); s.interval = Math.max(1, Math.round((s.interval || 1) * 1.2)); }
            if (g === 3) s.interval = s.reps === 0 ? 1 : s.reps === 1 ? 3 : Math.round(s.interval * s.ease);
            if (g === 4) { s.ease += 0.15; s.interval = s.reps === 0 ? 4 : Math.round(s.interval * s.ease * 1.3) + 1; }
            s.reps++;
        }
        s.due = addDays(todayStr(), s.interval);
        state[k] = s; save('srs', state);
        if (isNew) { intro.n++; save('srs.new', intro); }
        markActive(); next();
    }
    $('#btn-show').addEventListener('click', show);
    $$('[data-grade]').forEach((b) => b.addEventListener('click', () => grade(parseInt(b.dataset.grade, 10))));
    document.addEventListener('keydown', (e) => {
        if (e.target.matches('input,textarea')) return;
        if (e.code === 'Space') { e.preventDefault(); if (!shown) show(); }
        else if (shown && '1234'.includes(e.key)) grade(parseInt(e.key, 10));
        else if (e.key.toLowerCase() === 'p' && current) speak(current.w);
    });
    $('#stat-due').textContent = due.length; $('#stat-new').textContent = fresh.length;
    $('#stat-learned').textContent = Object.keys(state).length;
    next();
}
