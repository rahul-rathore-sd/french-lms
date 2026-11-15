import { NextResponse } from "next/server";
import { initialAdmissionRequests, AdmissionRequest } from "@/lib/data";
import { isAdmin } from "@/lib/auth";

// In-memory store for now (resets on restart). Swap for a database when ready.
const admissionsStore: AdmissionRequest[] = [...initialAdmissionRequests];

const CEFR = ["A1", "A2", "B1", "B2", "C1", "C2"] as const;
const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

function clean(value: unknown, max: number): string {
  return typeof value === "string" ? value.trim().slice(0, max) : "";
}

/** Admin only: applications contain personal data. */
export async function GET(req: Request) {
  if (!(await isAdmin())) {
    return NextResponse.json({ error: "Forbidden" }, { status: 403 });
  }
  const status = new URL(req.url).searchParams.get("status");
  const filtered =
    status && status !== "all"
      ? admissionsStore.filter((r) => r.status === status)
      : admissionsStore;

  return NextResponse.json({
    success: true,
    total: filtered.length,
    pending_count: admissionsStore.filter((r) => r.status === "pending").length,
    data: filtered,
  });
}

/** Public: anyone can apply. Input is validated and length-limited. */
export async function POST(req: Request) {
  let body: Record<string, unknown>;
  try {
    body = await req.json();
  } catch {
    return NextResponse.json({ error: "Invalid JSON body." }, { status: 400 });
  }

  const full_name = clean(body.full_name, 120);
  const email = clean(body.email, 200).toLowerCase();
  const motivation = clean(body.motivation, 2000);
  const academic_background = clean(body.academic_background, 300) || "Independent learner";
  const target_cefr = CEFR.includes(body.target_cefr as (typeof CEFR)[number])
    ? (body.target_cefr as AdmissionRequest["target_cefr"])
    : "B1";

  if (!full_name || !EMAIL_RE.test(email) || !motivation) {
    return NextResponse.json(
      { error: "Full name, a valid email and your motivation are required." },
      { status: 422 }
    );
  }

  if (admissionsStore.some((r) => r.email.toLowerCase() === email)) {
    // Do not reveal the status of an existing application to the public.
    return NextResponse.json(
      { error: "An application with this email already exists." },
      { status: 409 }
    );
  }

  const application: AdmissionRequest = {
    id: Math.max(0, ...admissionsStore.map((r) => r.id)) + 1,
    full_name,
    email,
    target_cefr,
    academic_background,
    motivation,
    status: "pending",
    submitted_at: new Date().toISOString(),
  };
  admissionsStore.unshift(application);

  return NextResponse.json({
    success: true,
    message: "Your application has been received and will be reviewed shortly.",
    data: { id: application.id, status: application.status },
  });
}

/** Admin only: approve or reject an application. */
export async function PATCH(req: Request) {
  if (!(await isAdmin())) {
    return NextResponse.json({ error: "Forbidden" }, { status: 403 });
  }

  let body: Record<string, unknown>;
  try {
    body = await req.json();
  } catch {
    return NextResponse.json({ error: "Invalid JSON body." }, { status: 400 });
  }

  const id = Number(body.id);
  const status = body.status;
  if (!id || (status !== "approved" && status !== "rejected" && status !== "pending")) {
    return NextResponse.json(
      { error: "A valid id and status (approved, rejected or pending) are required." },
      { status: 422 }
    );
  }

  const index = admissionsStore.findIndex((r) => r.id === id);
  if (index === -1) {
    return NextResponse.json({ error: "Application not found." }, { status: 404 });
  }

  admissionsStore[index] = {
    ...admissionsStore[index],
    status,
    reviewed_at: new Date().toISOString(),
    rejection_reason:
      clean(body.rejection_reason, 500) || admissionsStore[index].rejection_reason,
  };

  return NextResponse.json({ success: true, data: admissionsStore[index] });
}
