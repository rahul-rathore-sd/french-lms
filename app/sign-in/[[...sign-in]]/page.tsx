import { SignIn } from "@clerk/nextjs";
import AuthShell, { clerkAppearance } from "@/components/AuthShell";

export const metadata = { title: "Sign in" };

export default function SignInPage() {
  return (
    <AuthShell>
      <SignIn appearance={clerkAppearance} />
    </AuthShell>
  );
}
