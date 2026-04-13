```markdown
# Design System Document: The Luminescent Noir Directive

## 1. Overview & Creative North Star: "The Digital Sanctuary"
The Creative North Star for this design system is **"The Digital Sanctuary."** Unlike traditional SaaS platforms that feel like rigid spreadsheets, this system is designed to feel like a premium, quiet workspace. It moves away from the "boxy" nature of web software by utilizing deep tonal layering, intentional asymmetry, and "light-as-border" physics.

We reject the standard "flat" dashboard. Instead, we embrace **Atmospheric Depth**. By leveraging a dark, nocturnal palette punctuated by emerald luminescence, we create an environment that reduces cognitive load and directs focus through soft glows rather than harsh lines. The goal is an editorial-grade experience that feels "curated" rather than "templated."

---

## 2. Colors & Surface Philosophy

### The "No-Line" Rule
**Explicit Instruction:** Designers are prohibited from using 1px solid borders for sectioning or layout containment. 
Structure must be achieved through:
- **Tonal Shifts:** Placing a `surface_container_low` card against a `surface` background.
- **Negative Space:** Using the spacing scale to create psychological boundaries.
- **Elevation:** Using subtle shifts in luminosity to indicate hierarchy.

### Surface Hierarchy & Nesting
To create "nested" depth, use the following container tokens as physical layers:
1.  **Base Layer:** `surface` (#0b1326) – The canvas.
2.  **Sectioning:** `surface_container_low` (#131b2e) – Large structural areas (e.g., Sidebar, Main Content area).
3.  **Component Level:** `surface_container` (#171f33) – Cards, modules, and widgets.
4.  **Interaction Level:** `surface_container_high` (#222a3d) – Hover states, active tabs.
5.  **Overlay Level:** `surface_container_highest` (#2d3449) – Modals and popovers.

### The "Glass & Gradient" Rule
To elevate the experience, apply **Glassmorphism** to floating elements (tooltips, dropdowns). Use the `surface_variant` token at 60% opacity with a `20px` backdrop blur. 
**Signature Texture:** Primary CTAs should not be flat. Use a linear gradient: `primary_container` (#10b981) to `primary` (#4edea3) at a 135-degree angle to provide a "lit-from-within" polish.

---

## 3. Typography: Editorial Authority
We use **Inter** not as a generic sans-serif, but as a precision instrument. 

*   **Display & Headlines:** Use `display-md` for high-impact data points. Tighten letter-spacing (-0.02em) to give it a "technical-chic" look.
*   **Titles:** `title-lg` and `title-md` serve as the primary anchors for modules. Use `on_surface` for maximum readability.
*   **The Body-Label Contrast:** Use `body-md` in `on_surface_variant` (#bbcabf) for general descriptions, but use `label-md` in **all-caps with 0.05em tracking** for secondary metadata to create an editorial, "architectural" feel.

---

## 4. Elevation & Depth

### The Layering Principle
Depth is achieved by "stacking" luminosity. A `surface_container_lowest` element sitting on a `surface_container` background creates an "inset" feel (perfect for search bars), while the reverse creates a "lifted" feel.

### Ambient Shadows
For floating elements (Modals/Active Cards), use a **Triple-Stacked Shadow**:
1.  `0px 4px 12px rgba(0, 0, 0, 0.1)`
2.  `0px 12px 32px rgba(0, 0, 0, 0.2)`
3.  `0px 0px 8px rgba(78, 222, 163, 0.05)` (A subtle emerald tint to mimic the primary glow).

### The "Ghost Border" Fallback
If a visual divider is required for accessibility, use the **Ghost Border**: `outline_variant` (#3c4a42) at **15% opacity**. Never use a 100% opaque border.

---

## 5. Components

### Buttons: The Kinetic Glow
*   **Primary:** High-radius (`xl`: 1.5rem). Background: Emerald Gradient. Box-shadow: `0px 0px 15px rgba(16, 185, 129, 0.3)`. On hover, the glow intensity increases.
*   **Secondary:** `surface_container_highest` background with `primary` text. No border.
*   **Tertiary:** Transparent background, `on_surface_variant` text. Underline only on hover.

### Cards: The Borderless Container
*   **Rule:** Forbid divider lines within cards. 
*   **Structure:** Use `1.5rem` (xl) padding. Use `title-sm` for headers. Use a background shift (`surface_container_low`) to separate a card's footer from its body instead of a line.

### Input Fields: The Inset Depth
*   **Base:** `surface_container_lowest` (#060e20). 
*   **Focus State:** The "Ghost Border" becomes 100% `primary` opacity, but only at 1px thickness, accompanied by a subtle `primary` outer glow.

### Chips: The Subtle Indicator
*   **Style:** Pill-shaped (`full`). 
*   **Status:** Success use `primary_container` (low opacity) with `primary` text. Warning use `tertiary_container` with `tertiary` text.

---

## 6. Do’s and Don’ts

### Do:
*   **Do** use asymmetrical margins (e.g., more padding at the bottom of a header than the top) to create an organic, high-end feel.
*   **Do** use `primary_fixed_dim` for icons to give them a soft, neon-on-dark appearance.
*   **Do** allow content to "breathe" with generous `xl` (1.5rem) spacing between major modules.

### Don’t:
*   **Don't** use pure black (#000000) or pure white (#FFFFFF). Only use the defined surface and text tokens.
*   **Don't** use 1px lines to separate list items. Use a 4px gap and a 2% background shift on hover.
*   **Don't** use standard "drop shadows." All shadows must be tinted with the `surface_tint` or `on_surface` color to maintain the nocturnal atmosphere.

---

## 7. Designer Note: Intentionality
Every element in this design system must feel like it was placed with surgical precision. If a component doesn't serve a clear functional or aesthetic purpose, remove it. We are building a sanctuary of productivity, not a cluttered dashboard. Avoid "default" thinking; every pixel should feel intentional.```