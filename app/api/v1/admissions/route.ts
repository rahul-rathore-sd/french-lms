import { NextResponse } from "next/server";
import { initialAdmissionRequests, AdmissionRequest } from "@/lib/data";

let admissionsStore: AdmissionRequest[] = [...initialAdmissionRequests];

export async function GET(req: Request) {
  const { searchParams } = new URL(req.url);
  const status = searchParams.get("status");

  let filtered = [...admissionsStore];
  if (status && status !== "all") {
    filtered = filtered.filter((r) => r.status === status);
  }

  return NextResponse.json({
    success: true,
    total: filtered.length,
    pending_count: admissionsStore.filter((r) => r.status === "pending").length,
    data: filtered,
  });
}

export async function POST(req: Request) {
  try {
    const body = await req.json();
    const { full_name, email, target_cefr = "B1", academic_background, motivation } = body;

    if (!full_name || !email || !motivation) {
      return NextResponse.json(
        { error: "Le nom complet, le courriel et la motivation sont obligatoires." },
        { status: 422 }
      );
    }

    const existing = admissionsStore.find(
      (r) => r.email.toLowerCase() === email.toLowerCase()
    );

    if (existing) {
      return NextResponse.json(
        {
          error: "Une candidature avec cette adresse courriel est déjà en cours d'examen ou traitée.",
          existing_status: existing.status,
        },
        { status: 409 }
      );
    }

    const newApplication: AdmissionRequest = {
      id: admissionsStore.length + 1,
      full_name,
      email,
      target_cefr,
      academic_background: academic_background || "Candidat Indépendant",
      motivation,
      status: "pending",
      submitted_at: new Date().toISOString(),
    };

    admissionsStore.unshift(newApplication);

    return NextResponse.json({
      success: true,
      message: "Votre candidature d'admission a été reçue et est en cours d'examen par la Faculté.",
      data: newApplication,
    });
  } catch (err: unknown) {
    const errorMsg = err instanceof Error ? err.message : "Erreur interne";
    return NextResponse.json({ error: errorMsg }, { status: 500 });
  }
}

export async function PATCH(req: Request) {
  try {
    const body = await req.json();
    const { id, status, rejection_reason } = body;

    if (!id || !["approved", "rejected", "pending"].includes(status)) {
      return NextResponse.json(
        { error: "Identifiant et statut valide ('approved' ou 'rejected') requis." },
        { status: 422 }
      );
    }

    const index = admissionsStore.findIndex((r) => r.id === Number(id));
    if (index === -1) {
      return NextResponse.json({ error: "Candidature introuvable." }, { status: 404 });
    }

    admissionsStore[index] = {
      ...admissionsStore[index],
      status,
      reviewed_at: new Date().toISOString(),
      rejection_reason: rejection_reason || admissionsStore[index].rejection_reason,
    };

    return NextResponse.json({
      success: true,
      message:
        status === "approved"
          ? "Candidature approuvée. Le savant est admis au Sanctuaire."
          : "Candidature rejetée.",
      data: admissionsStore[index],
    });
  } catch (err: unknown) {
    const errorMsg = err instanceof Error ? err.message : "Erreur interne";
    return NextResponse.json({ error: errorMsg }, { status: 500 });
  }
}
