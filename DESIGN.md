# Foodly — Design System

Editorial, appetite-forward. **Committed** color strategy: a warm cream surface
carries the page, charcoal ink for text, one deep-terracotta accent for action and
emphasis. Drama from type scale and full-bleed food photography, not chrome.

## Theme
Light. Physical scene: a customer at a kitchen table mid-afternoon, warm daylight,
relaxed and a little hungry, browsing on a phone or laptop. Cream paper, not screen
glare. Dark mode would fight the appetite-forward, magazine feel.

## Color (OKLCH)
Neutrals tinted warm (toward the terracotta hue). Never `#fff` / `#000`.

| Token | OKLCH | Role |
|---|---|---|
| `--bg` | `oklch(0.972 0.012 75)` | page — warm cream paper |
| `--surface` | `oklch(0.945 0.015 72)` | raised panels, cards |
| `--surface-2` | `oklch(0.915 0.016 70)` | insets, hover wells |
| `--ink` | `oklch(0.235 0.014 55)` | primary text — warm charcoal |
| `--ink-muted` | `oklch(0.48 0.018 55)` | secondary text |
| `--ink-faint` | `oklch(0.66 0.015 60)` | captions, placeholders |
| `--border` | `oklch(0.875 0.012 72)` | hairlines |
| `--accent` | `oklch(0.575 0.135 38)` | deep terracotta — primary action |
| `--accent-hover` | `oklch(0.515 0.135 38)` | pressed/hover |
| `--accent-soft` | `oklch(0.93 0.04 45)` | accent-tinted wash |
| `--herb` | `oklch(0.55 0.07 135)` | sparing secondary (fresh/status ok) |
| `--success` | `oklch(0.58 0.10 150)` | confirmations |
| `--danger` | `oklch(0.55 0.16 27)` | errors |

## Typography
Reflex defaults (Fraunces, Inter, Playfair, DM, etc.) are rejected on purpose.
Chosen for warmth + appetite, not training-data familiarity:
- **Display:** `Young Serif` (single weight 400) — wordmark, hero, section titles.
  Chunky old-style serif with organic terminals; reads warm and edible, not
  corporate. Hierarchy from size, not weight.
- **Body/UI:** `Hanken Grotesk` (variable 300–800) — everything else. Quiet,
  humanist, gives full weight range for hierarchy.
- Load via Google Fonts. Fallbacks: `Georgia, serif` and `system-ui, sans-serif`.

Type scale (ratio ~1.25, larger jump for display):
`--step--1: 0.833rem` · `--step-0: 1rem` · `--step-1: 1.25rem` ·
`--step-2: 1.6rem` · `--step-3: 2.2rem` · `--step-4: 3.2rem` · `--step-5: 4.8rem`
Body line-length capped 65–72ch. Display headings tight leading (~1.05), body ~1.6.

## Spacing & Radius
Spacing scale (rem): `0.25 0.5 0.75 1 1.5 2 3 4 6 8`. Vary it for rhythm — do not
pad everything equally. Radius: `--r-sm: 6px`, `--r-md: 12px`, `--r-lg: 20px`,
`--r-pill: 999px`. Imagery uses `--r-lg`.

## Elevation
Soft, warm-tinted, low. Shadows tint toward ink, never pure black.
- `--shadow-sm: 0 1px 2px oklch(0.235 0.014 55 / 0.06)`
- `--shadow-md: 0 8px 24px -8px oklch(0.235 0.014 55 / 0.14)`
- `--shadow-lg: 0 24px 60px -20px oklch(0.235 0.014 55 / 0.22)`

## Components
- **Button (primary):** terracotta fill, cream text, pill or `--r-md`, weight 550,
  subtle lift on hover (`translateY(-1px)` + shadow-md). No gradient.
- **Button (ghost):** transparent, ink text, 1px border, fills `--surface-2` on hover.
- **Input:** `--surface`, 1px `--border`, focus ring in `--accent` (2px, soft).
  Generous padding (0.75rem 1rem).
- **Card:** only when truly the best affordance; `--surface`, 1px border, `--r-lg`,
  `--shadow-sm`. Never nested. Food cards lead with the photo.
- **Nav:** transparent over hero, cream + hairline once scrolled. Wordmark in Fraunces.
- **Modal (auth):** used here because login/signup genuinely overlays the landing;
  cream sheet, `--r-lg`, `--shadow-lg`, backdrop is ink at low opacity.

## Motion
Ease-out only (`cubic-bezier(0.22, 1, 0.36, 1)`). 150–260ms. Animate transform/
opacity, never layout. No bounce. Reveal-on-scroll for food sections, subtle.

## Banned (project-specific)
Orange/red delivery-app gradients · gradient text · side-stripe borders ·
glassmorphism · identical icon-card grids · hero-metric template · em dashes in copy.
