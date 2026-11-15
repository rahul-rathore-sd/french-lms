"use client";

import { useCallback, useEffect, useRef, useState } from "react";

/** Text-to-speech helper using the Web Speech API (French voices when available). */
export function useSpeech() {
  const [speaking, setSpeaking] = useState<string | null>(null);
  const [supported, setSupported] = useState(false);
  const voiceRef = useRef<SpeechSynthesisVoice | null>(null);

  useEffect(() => {
    if (typeof window === "undefined" || !("speechSynthesis" in window)) return;
    setSupported(true);
    const pick = () => {
      const voices = window.speechSynthesis.getVoices();
      voiceRef.current =
        voices.find((v) => v.lang === "fr-FR") ||
        voices.find((v) => v.lang.toLowerCase().startsWith("fr")) ||
        null;
    };
    pick();
    window.speechSynthesis.addEventListener("voiceschanged", pick);
    return () => window.speechSynthesis.removeEventListener("voiceschanged", pick);
  }, []);

  const speak = useCallback((text: string, rate = 0.9) => {
    if (typeof window === "undefined" || !("speechSynthesis" in window)) return;
    window.speechSynthesis.cancel();
    const clean = text.replace(/_/g, " ");
    const u = new SpeechSynthesisUtterance(clean);
    u.lang = "fr-FR";
    u.rate = rate;
    if (voiceRef.current) u.voice = voiceRef.current;
    u.onend = () => setSpeaking(null);
    u.onerror = () => setSpeaking(null);
    setSpeaking(text);
    window.speechSynthesis.speak(u);
  }, []);

  return { speak, speaking, supported };
}
