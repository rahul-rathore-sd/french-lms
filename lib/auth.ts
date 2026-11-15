import "server-only";
import { timingSafeEqual } from "node:crypto";
import { auth, currentUser } from "@clerk/nextjs/server";

/** Constant-time string comparison that never throws on length mismatch. */
export function safeEqual(a: string, b: string): boolean {
  const ba = Buffer.from(a);
  const bb = Buffer.from(b);
  if (ba.length !== bb.length) return false;
  return timingSafeEqual(ba, bb);
}

/**
 * Verifies the `Authorization: Bearer <token>` header against HERMES_API_KEY.
 * Returns "ok", "unconfigured" (env var missing -> endpoint disabled) or "denied".
 */
export function checkHermesKey(req: Request): "ok" | "unconfigured" | "denied" {
  const expected = process.env.HERMES_API_KEY;
  if (!expected || expected.length < 16) return "unconfigured";
  const header = req.headers.get("authorization") ?? "";
  const token = header.startsWith("Bearer ") ? header.slice(7) : "";
  return token && safeEqual(token, expected) ? "ok" : "denied";
}

function adminEmails(): string[] {
  return (process.env.ADMIN_EMAILS ?? "")
    .split(",")
    .map((e) => e.trim().toLowerCase())
    .filter(Boolean);
}

/**
 * A signed-in user is an admin when their Clerk `publicMetadata.role` is "admin"
 * or their primary email is listed in the ADMIN_EMAILS environment variable.
 */
export async function isAdmin(): Promise<boolean> {
  const { userId } = await auth();
  if (!userId) return false;
  const user = await currentUser();
  if (!user) return false;
  if (user.publicMetadata?.role === "admin") return true;
  const primary = user.emailAddresses
    .find((e) => e.id === user.primaryEmailAddressId)
    ?.emailAddress?.toLowerCase();
  return !!primary && adminEmails().includes(primary);
}
