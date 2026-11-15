import { NextResponse } from "next/server";
import { initialCourses, initialVocabulary, mockScholars } from "@/lib/data";

export async function GET() {
  return NextResponse.json({
    name: "Académie de Français - Classical LMS & Lexicon Sanctuary",
    version: "2.0.0 (Next.js App Router)",
    status: "operational",
    statistics: {
      total_courses: initialCourses.length,
      total_lessons: initialCourses.reduce((acc, c) => acc + c.lessons.length, 0),
      vocabulary_terms: initialVocabulary.length,
      active_scholars: mockScholars.length,
    },
    integrations: {
      clerk_auth: "enabled",
      resend_webhook: "enabled",
      tts_audio: "Web Speech API (fr-FR / fr-CA)",
      hermes_autonomous_ingestion: "active",
    },
    timestamp: new Date().toISOString(),
  });
}
