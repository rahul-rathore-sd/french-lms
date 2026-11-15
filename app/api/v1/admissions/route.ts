import { NextResponse } from "next/server";
import type { AdmissionRequest } from "@/lib/data";
import { isAdmin } from "@/lib/auth";
import { createAdmission, decideAdmission, listAdmissions, storageMode } from "@/lib/store";

export const dynamic = "force-dynamic";

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
  try {
    const all = await listAdmissions();
    const filtered = status && status !== "all" ? all.filter((r) => r.status === status) : all;
    return NextResponse.json({
      success: true,
      storage: storageMode,
      total: filtered.length,
      pending_count: all.filter((r) => r.status === "pending").length,
      data: filtered,
    });
  } catch (err) {
    console.error("Could not load admissions:", err);
    return NextResponse.json({ error: "Storage error." }, { status: 500 });
  }
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

  try {
    const application = await createAdmission({
      full_name,
      email,
      target_cefr,
      academic_background,
      motivation,
    });
    if (!application) {
      // Do not reveal the status of an existing application to the public.
      return NextResponse.json({ error: "An application with this email already exists." }, { status: 409 });
    }
    return NextResponse.json({
      success: true,
      message: "Your application has been received and will be reviewed shortly.",
      data: { id: application.id, status: application.status },
    });
  } catch (err) {
    console.error("Could not save application:", err);
    return NextResponse.json({ error: "Could not save your application. Please try again." }, { status: 500 });
  }
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

  try {
    const updated = await decideAdmission(id, status, clean(body.rejection_reason, 500));
    if (!updated) return NextResponse.json({ error: "Application not found." }, { status: 404 });
    return NextResponse.json({ success: true, data: updated });
  } catch (err) {
    console.error("Could not update application:", err);
    return NextResponse.json({ error: "Storage error." }, { status: 500 });
  }
}
