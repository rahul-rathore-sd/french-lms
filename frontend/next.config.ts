import type { NextConfig } from "next";

// Static export: `next build` writes plain HTML/JS/CSS to ./out, which is uploaded
// to shared hosting together with the PHP API in ./server (see build-deploy.py).
const nextConfig: NextConfig = {
  output: "export",
  trailingSlash: true,
  reactStrictMode: true,
  poweredByHeader: false,
  images: { unoptimized: true },
  eslint: { ignoreDuringBuilds: true },
};

export default nextConfig;
