import Link from "next/link";
import { SignUp } from "@clerk/nextjs";
import { Lock } from "lucide-react";
import AuthShell, { clerkAppearance } from "@/components/AuthShell";

export const metadata = { title: "Create account" };

export default function SignUpPage() {
  return (
    <AuthShell
      notice={
        <div className="flex w-full items-start gap-3 rounded-2xl border border-gold/30 bg-gold/10 p-4 text-sm">
          <Lock className="mt-0.5 h-4 w-4 shrink-0 text-gold" />
          <p className="text-muted">
            <span className="font-medium text-fg">Invitation only.</span> Accounts are created for approved applicants.{" "}
            <Link href="/apply" className="font-medium text-accent hover:underline">Request an invitation</Link>
          </p>
        </div>
      }
    >
      <SignUp appearance={clerkAppearance} />
    </AuthShell>
  );
}
