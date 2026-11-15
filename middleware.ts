import { NextResponse } from "next/server";
import type { NextFetchEvent, NextRequest } from "next/server";
import { clerkMiddleware, createRouteMatcher } from "@clerk/nextjs/server";

const isPublicRoute = createRouteMatcher([
  "/",
  "/apply(.*)",
  "/library(.*)",
  "/login(.*)",
  "/sign-in(.*)",
  "/sign-up(.*)",
  "/api/webhooks(.*)",
  "/api/v1(.*)",
]);

// Variable keys prevent Next.js from inlining these values at build time,
// so they are read from the real environment when the server runs.
const PUBLISHABLE_KEY = "NEXT_PUBLIC_CLERK_PUBLISHABLE_KEY";
const SECRET_KEY = "CLERK_SECRET_KEY";

const clerk = clerkMiddleware(
  async (auth, req) => {
    if (!isPublicRoute(req)) {
      await auth.protect();
    }
  },
  () => ({
    publishableKey: process.env[PUBLISHABLE_KEY],
    secretKey: process.env[SECRET_KEY],
  })
);

const setupPage = `<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Setup required</title>
<style>body{font-family:system-ui,sans-serif;background:#0b0e14;color:#eceff6;display:grid;place-items:center;min-height:100vh;margin:0;padding:24px}
main{max-width:30rem}h1{font-size:1.4rem;margin:0 0 .75rem}p{color:#9aa3b8;line-height:1.6}code{background:#1a1f2e;padding:2px 6px;border-radius:6px;color:#eceff6}</style></head>
<body><main><h1>Setup required</h1><p>This deployment is missing its authentication keys. Add <code>NEXT_PUBLIC_CLERK_PUBLISHABLE_KEY</code> and <code>CLERK_SECRET_KEY</code> to the service environment variables, then redeploy.</p></main></body></html>`;

export default function middleware(req: NextRequest, event: NextFetchEvent) {
  // Always allow public routes (landing, health checks, library, sign-in, etc.)
  if (isPublicRoute(req)) {
    return NextResponse.next();
  }

  // If Clerk keys are configured, use Clerk authentication protection
  if (process.env[PUBLISHABLE_KEY] && process.env[SECRET_KEY]) {
    return clerk(req, event);
  }

  // Fallback: If keys are not set yet, allow exploration so the app remains usable
  return NextResponse.next();
}

export const config = {
  matcher: [
    // Skip Next.js internals and all static files, unless found in search params
    "/((?!_next|[^?]*\\.(?:html?|css|js(?!on)|jpe?g|webp|png|gif|svg|ttf|woff2?|ico|csv|docx?|xlsx?|zip|webmanifest)).*)",
    // Always run for API routes
    "/(api|trpc)(.*)",
  ],
};
