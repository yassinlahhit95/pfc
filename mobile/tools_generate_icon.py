"""
Generates the AulaPro app icon at every Android launcher density plus the
512x512 Play Store hi-res listing icon. Reuses the exact same visual
language already established elsewhere in the app rather than inventing a
new identity:
  - The gradient (linear, ~145deg, --accent -> a darkened mix of --accent)
    is the same one .brand-mark uses for the sidebar logo mark on every
    web page (public/css/dashboard.css).
  - The bold white "A" on that gradient is the same mark used on the
    mobile app's own splash screen (mobile/lib/core/router/app_router.dart).
Run once locally (not part of the Flutter build): `python tools_generate_icon.py`
Requires Pillow (pip install pillow) — not a project dependency, just a
one-off generation tool, hence living outside pubspec.yaml/lib/.
"""
import math
from pathlib import Path
from PIL import Image, ImageDraw, ImageFont

ACCENT = (79, 70, 229)          # #4F46E5 — var(--accent)
ACCENT_DARK = (34, 30, 101)     # --accent mixed ~55% toward black, per .brand-mark's gradient
WHITE = (255, 255, 255, 255)

ROOT = Path(__file__).parent
FONT_PATH = "C:/Windows/Fonts/arialbd.ttf"

SIZES = {
    "android/app/src/main/res/mipmap-mdpi/ic_launcher.png": 48,
    "android/app/src/main/res/mipmap-hdpi/ic_launcher.png": 72,
    "android/app/src/main/res/mipmap-xhdpi/ic_launcher.png": 96,
    "android/app/src/main/res/mipmap-xxhdpi/ic_launcher.png": 144,
    "android/app/src/main/res/mipmap-xxxhdpi/ic_launcher.png": 192,
}
STORE_ICON_SIZE = 512
MASTER_SIZE = 1024  # render big, downsample for clean anti-aliasing


def build_master() -> Image.Image:
    size = MASTER_SIZE
    img = Image.new("RGBA", (size, size), (0, 0, 0, 0))

    # Diagonal gradient background (approximates linear-gradient(145deg, accent, accent-dark))
    grad = Image.new("RGBA", (size, size), (0, 0, 0, 0))
    gpix = grad.load()
    angle = math.radians(145)
    dx, dy = math.cos(angle), math.sin(angle)
    for y in range(size):
        for x in range(size):
            # project (x,y) onto the gradient direction, normalize 0..1
            t = ((x / size) * dx + (y / size) * dy + 1) / 2
            t = min(1, max(0, t))
            r = int(ACCENT[0] + (ACCENT_DARK[0] - ACCENT[0]) * t)
            g = int(ACCENT[1] + (ACCENT_DARK[1] - ACCENT[1]) * t)
            b = int(ACCENT[2] + (ACCENT_DARK[2] - ACCENT[2]) * t)
            gpix[x, y] = (r, g, b, 255)

    # Rounded-square mask (matches .brand-mark's border-radius proportion)
    radius = int(size * 0.22)
    mask = Image.new("L", (size, size), 0)
    mdraw = ImageDraw.Draw(mask)
    mdraw.rounded_rectangle([(0, 0), (size - 1, size - 1)], radius=radius, fill=255)

    img.paste(grad, (0, 0), mask)

    # Bold white "A"
    draw = ImageDraw.Draw(img)
    font = ImageFont.truetype(FONT_PATH, int(size * 0.56))
    letter = "A"
    bbox = draw.textbbox((0, 0), letter, font=font)
    tw, th = bbox[2] - bbox[0], bbox[3] - bbox[1]
    pos = ((size - tw) / 2 - bbox[0], (size - th) / 2 - bbox[1])
    draw.text(pos, letter, font=font, fill=WHITE)

    return img


def main():
    master = build_master()

    for rel_path, px in SIZES.items():
        out = ROOT / rel_path
        out.parent.mkdir(parents=True, exist_ok=True)
        resized = master.resize((px, px), Image.LANCZOS)
        resized.save(out)
        print(f"wrote {out} ({px}x{px})")

    store_out = ROOT / "store_icon_512.png"
    master.resize((STORE_ICON_SIZE, STORE_ICON_SIZE), Image.LANCZOS).save(store_out)
    print(f"wrote {store_out} ({STORE_ICON_SIZE}x{STORE_ICON_SIZE}) — upload this one in Play Console's store listing")


if __name__ == "__main__":
    main()
