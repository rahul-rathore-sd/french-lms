"use client";

import { useState } from "react";
import { Volume2, RotateCw } from "lucide-react";
import { useSpeech } from "./useSpeech";

const PHRASES = [
  { fr: "Enchanté de faire votre connaissance.", ipa: "ɑ̃.ʃɑ̃.te də fɛʁ vɔ.tʁə kɔ.nɛ.sɑ̃s", en: "Pleased to make your acquaintance.", hi: "आपसे मिलकर खुशी हुई।" },
  { fr: "Je voudrais un café, s’il vous plaît.", ipa: "ʒə vu.dʁɛ œ̃ ka.fe sil vu plɛ", en: "I would like a coffee, please.", hi: "मुझे एक कॉफ़ी चाहिए, कृपया।" },
  { fr: "La vie est belle quand on la partage.", ipa: "la vi ɛ bɛl kɑ̃ tɔ̃ la paʁ.taʒ", en: "Life is beautiful when you share it.", hi: "ज़िंदगी तब खूबसूरत है जब उसे बाँटा जाए।" },
];

export default function HeroDemo() {
  const [i, setI] = useState(0);
  const { speak, speaking } = useSpeech();
  const p = PHRASES[i];
  const active = speaking === p.fr;

  return (
    <div className="relative animate-rise rounded-3xl border border-line bg-surface p-6 shadow-soft sm:p-8">
      <div className="absolute -inset-px -z-10 rounded-3xl bg-gradient-to-br from-accent/30 via-transparent to-gold/20 blur-2xl" />
      <div className="mb-6 flex items-center justify-between">
        <span className="rounded-full bg-accent/10 px-3 py-1 text-xs font-medium text-accent">Try it · tap to listen</span>
        <button
          onClick={() => setI((i + 1) % PHRASES.length)}
          className="flex items-center gap-1.5 text-xs text-muted transition hover:text-fg"
          aria-label="Next phrase"
        >
          <RotateCw className="h-3.5 w-3.5" /> Next
        </button>
      </div>

      <button
        onClick={() => speak(p.fr)}
        className="group block w-full text-left"
        aria-label={`Listen: ${p.fr}`}
      >
        <p className="font-display text-3xl font-medium leading-snug tracking-tight sm:text-4xl">{p.fr}</p>
        <p className="mt-3 font-mono text-sm text-muted">/{p.ipa}/</p>
      </button>

      <div className="mt-6 flex items-center gap-4">
        <button
          onClick={() => speak(p.fr)}
          className={`grid h-12 w-12 shrink-0 place-items-center rounded-full bg-accent text-accent-fg transition hover:scale-105 ${active ? "shadow-glow" : ""}`}
          aria-label="Play pronunciation"
        >
          {active ? (
            <span className="flex h-4 items-end gap-0.5">
              {[0, 1, 2, 3].map((n) => (
                <span key={n} className="h-4 w-0.5 origin-bottom animate-wave rounded bg-accent-fg" style={{ animationDelay: `${n * 120}ms` }} />
              ))}
            </span>
          ) : (
            <Volume2 className="h-5 w-5" />
          )}
        </button>
        <div className="space-y-1 text-sm">
          <p className="text-fg">{p.en}</p>
          <p className="text-muted">{p.hi}</p>
        </div>
      </div>
    </div>
  );
}
