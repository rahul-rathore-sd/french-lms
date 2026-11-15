"""Scan ../books for PDFs, render each cover to public/books/*.jpg and write lib/library/books.json.
Run: python -W ignore scripts/make-books.py   (needs PyMuPDF: pip install pymupdf)"""
import fitz, json, re, pathlib
root = pathlib.Path(__file__).resolve().parent.parent
src = root.parent / "books"
out = root / "public" / "books"
out.mkdir(parents=True, exist_ok=True)
meta = json.loads((root / "lib/library/books-meta.json").read_text(encoding="utf8")) if (root / "lib/library/books-meta.json").exists() else {}
items = []
for pdf in sorted(src.glob("*.pdf")):
    d = fitz.open(pdf)
    slug = re.sub(r"[^a-z0-9]+", "-", pdf.stem.lower()).strip("-")[:40]
    pm = d[0].get_pixmap(dpi=90)
    # A cover that is almost uniform (blank page) is treated as "no cover".
    samples = pm.samples[:: max(1, len(pm.samples) // 4000)]
    spread = max(samples) - min(samples) if samples else 0
    cover = None
    if spread > 40:
        pm.save(out / f"{slug}.jpg", jpg_quality=70)
        cover = f"/books/{slug}.jpg"
    m = meta.get(pdf.name, {})
    items.append({"slug": slug, "file": pdf.name, "pages": len(d), "cover": cover,
                  "title": m.get("title", pdf.stem), "author": m.get("author", ""), "level": m.get("level", ""),
                  "blurb": m.get("blurb", ""), "blurb_hi": m.get("blurb_hi", "")})
(root / "lib/library/books.json").write_text(json.dumps(items, ensure_ascii=False, indent=1), encoding="utf8")
print(len(items), "books")
