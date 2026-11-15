import { headers } from "next/headers";
import { NextResponse } from "next/server";
import { Webhook } from "svix";
import { Resend } from "resend";

interface ClerkEmailAddress {
  id: string;
  email_address: string;
}

interface ClerkUserCreatedData {
  id: string;
  first_name?: string | null;
  last_name?: string | null;
  username?: string | null;
  email_addresses: ClerkEmailAddress[];
  primary_email_address_id?: string | null;
}

interface ClerkWebhookEvent {
  type: string;
  data: ClerkUserCreatedData;
}

export async function POST(req: Request) {
  const WEBHOOK_SECRET = process.env.CLERK_WEBHOOK_SECRET;

  if (!WEBHOOK_SECRET) {
    console.error("Missing CLERK_WEBHOOK_SECRET in environment variables");
    return NextResponse.json(
      { error: "Webhook secret is not configured" },
      { status: 500 }
    );
  }

  // Next.js 15: headers() is asynchronous
  const headerPayload = await headers();
  const svix_id = headerPayload.get("svix-id");
  const svix_timestamp = headerPayload.get("svix-timestamp");
  const svix_signature = headerPayload.get("svix-signature");

  if (!svix_id || !svix_timestamp || !svix_signature) {
    return NextResponse.json(
      { error: "Missing svix verification headers" },
      { status: 400 }
    );
  }

  // Get raw payload string
  const payload = await req.text();
  const wh = new Webhook(WEBHOOK_SECRET);

  let evt: ClerkWebhookEvent;

  try {
    evt = wh.verify(payload, {
      "svix-id": svix_id,
      "svix-timestamp": svix_timestamp,
      "svix-signature": svix_signature,
    }) as ClerkWebhookEvent;
  } catch (err) {
    console.error("Webhook signature verification failed:", err);
    return NextResponse.json(
      { error: "Invalid webhook signature" },
      { status: 400 }
    );
  }

  const eventType = evt.type;

  if (eventType === "user.created") {
    const { id, first_name, last_name, email_addresses, primary_email_address_id } = evt.data;

    // Find primary email address
    const primaryEmail =
      email_addresses.find((e) => e.id === primary_email_address_id)?.email_address ||
      email_addresses[0]?.email_address;

    const scholarName = [first_name, last_name].filter(Boolean).join(" ") || "Cher Savant";

    console.log(`[Clerk Webhook] New user registered: ${scholarName} (${primaryEmail}), User ID: ${id}`);

    if (primaryEmail && process.env.RESEND_API_KEY) {
      try {
        const resend = new Resend(process.env.RESEND_API_KEY);
        const fromEmail = process.env.RESEND_FROM_EMAIL || "onboarding@resend.dev";

        const emailHtml = `
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Bienvenue à l'Académie de Français</title>
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #0f172a; color: #f8fafc; margin: 0; padding: 40px 20px; }
    .card { max-width: 600px; margin: 0 auto; background: #1e293b; border-radius: 16px; border: 1px solid #334155; padding: 40px; }
    .header { text-align: center; border-bottom: 2px solid #D52B1E; padding-bottom: 24px; margin-bottom: 30px; }
    .title { color: #ffffff; font-size: 26px; font-weight: 700; margin: 12px 0 4px 0; }
    .subtitle { color: #94a3b8; font-size: 14px; text-transform: uppercase; letter-spacing: 2px; }
    .badge { display: inline-block; background: #D52B1E; color: #ffffff; font-size: 12px; font-weight: 600; padding: 4px 12px; border-radius: 9999px; }
    .content { line-height: 1.7; color: #cbd5e1; font-size: 16px; }
    .highlight-box { background: rgba(213, 43, 30, 0.1); border-left: 4px solid #D52B1E; padding: 16px 20px; border-radius: 8px; margin: 24px 0; }
    .cta-button { display: inline-block; background: #D52B1E; color: #ffffff; font-weight: 600; font-size: 16px; padding: 14px 32px; border-radius: 10px; text-decoration: none; margin-top: 24px; }
    .footer { text-align: center; margin-top: 36px; padding-top: 20px; border-top: 1px solid #334155; font-size: 13px; color: #64748b; }
  </style>
</head>
<body>
  <div class="card">
    <div class="header">
      <div class="badge">🇨🇦 Académie de Français</div>
      <h1 class="title">Bienvenue, ${scholarName}!</h1>
      <p class="subtitle">CEFR Classical Immersion Sanctuary</p>
    </div>
    <div class="content">
      <p>Votre inscription au sanctuaire de l'Académie de Français a été confirmée avec succès.</p>
      
      <div class="highlight-box">
        <strong style="color: #ffffff;">Votre Voyage Linguistique (CEFR A1 &rarr; C2) :</strong><br>
        Vous avez désormais accès à tous les syllabi officiels, aux exercices acoustiques interactifs et au lexique d'immersion classique avec prononciation audio native et traductions multilingues (Français · Anglais · हिन्दी).
      </div>

      <p>Commencez dès aujourd'hui votre première leçon pour allumer votre flamme d'assiduité (Streak Tracker 7-Jours) :</p>
      
      <div style="text-align: center;">
        <a href="https://academie-francais.onrender.com/dashboard" class="cta-button">Accéder à mon Sanctuaire &rarr;</a>
      </div>
    </div>
    <div class="footer">
      Académie de Français · Classical Immersion & Research Sanctuary<br>
      Ottawa & Paris · Tous droits réservés.
    </div>
  </div>
</body>
</html>
        `;

        const { data, error } = await resend.emails.send({
          from: `Académie de Français <${fromEmail}>`,
          to: [primaryEmail],
          subject: "Bienvenue à l'Académie de Français 🇨🇦 | Votre Sanctuaire d'Immersion",
          html: emailHtml,
        });

        if (error) {
          console.error("Resend error sending welcome email:", error);
        } else {
          console.log(`Welcome email dispatched successfully to ${primaryEmail}:`, data?.id);
        }
      } catch (err) {
        console.error("Exception during email dispatch:", err);
      }
    } else {
      console.warn("RESEND_API_KEY not configured. Skipping welcome email dispatch.");
    }
  }

  return NextResponse.json({ success: true, event: eventType }, { status: 200 });
}
