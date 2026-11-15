import Link from "next/link";
import { ShieldAlert } from "lucide-react";
import SiteHeader from "@/components/SiteHeader";
import { isAdmin } from "@/lib/auth";

export const metadata = { title: "Admin" };
export const dynamic = "force-dynamic";

export default async function AdminLayout({ children }: { children: React.ReactNode }) {
  if (!(await isAdmin())) {
    return (
      <div className="min-h-screen">
        <SiteHeader />
        <main className="mx-auto max-w-md px-4 py-24 text-center">
          <span className="mx-auto grid h-14 w-14 place-items-center rounded-full bg-danger/10 text-danger">
            <ShieldAlert className="h-7 w-7" />
          </span>
          <h1 className="mt-5 font-display text-3xl font-medium">Admins only</h1>
          <p className="mt-2 text-sm text-muted">
            Your account does not have access to the admin console. Add your email to <code className="rounded bg-raised px-1.5 py-0.5 text-xs">ADMIN_EMAILS</code> or set the Clerk role to admin.
          </p>
          <Link href="/dashboard" className="mt-6 inline-flex h-11 items-center rounded-full bg-accent px-6 text-sm font-medium text-accent-fg">
            Back to dashboard
          </Link>
        </main>
      </div>
    );
  }
  return <>{children}</>;
}
