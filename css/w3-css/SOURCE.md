# W3.CSS in this theme

## Files

- `w3-modern.css`: what the theme loads (library `w3-css`). Plain CSS, edited by
  hand, no build step.
- `w3.css`: the original, kept byte-identical and no longer loaded. Do not edit.

## Base

W3.CSS 4.15, December 2020, by Jan Egil and Borge Refsnes, as kept in this theme.
Nothing was downloaded. It differs from upstream 4.15 in two places, both kept:
`.w3-hover-over:hover` is added, and `.w3-select` has `padding: 12px 0` (upstream
9px).

sha256 of `w3.css`:
`97c43864102c4b82f16a2bda4fd00f014c619943ab6fd6e93cca168e080fd6c9`

## What changed

Every old class stays, with the same effect at default settings.
`tests/w3css/compat-check.py` enforces this against the frozen
`tests/w3css/baseline-4.15.json`. Its header lists what it checks and what it
cannot see.

- **Tokens:** colors and spacing are custom properties on `:root`.
- **Type:** the root is 93.75% (15px at the browser's default) and headings are
  in rem, so the browser's font-size preference now works. The size utilities
  (`.w3-small` and so on) stay in px, with `!important`.
- **Columns:** flex, only in a row whose element children are all columns, where
  no child floats right and nothing follows a `.w3-rest`. Every other row keeps
  the float rules, which is why those rules stay. Clearfix stays too: it is inert
  in a flex row and needed if a site forces the row back to `display: block`.
- **Focus:** a `:focus-visible` ring replaces the removed outlines. It uses
  `!important` because the theme's `base.css` sets `outline: none` on `a:focus`.
- **Keyboard:** dropdown, tooltip and display-hover open on keyboard focus, and
  Tab moves through the links inside them. A mouse click does not hold them open.
- **Motion:** `prefers-reduced-motion` stops library animations and transitions.
- **Removed:** the IE display reset, `-ms-`, `-khtml-` and `-moz-user-select`
  prefixes, `::-moz-focus-inner`, `:-moz-focusring`, the hover outline reset.
- **New classes:** `w3-row-equal`, `w3-start-align`, `w3-end-align`,
  `w3-margin-start`, `w3-margin-end`, `w3-flex`, `w3-flex-wrap`, `w3-grid`,
  `w3-grid-2`, `w3-grid-3`, `w3-grid-4`, `w3-grid-auto`, `w3-gap`.

## Left alone on purpose

- **No cascade layers:** a layer would let the library's `!important` beat the
  scheme overrides in `css/w3-css-theme-custom/`.
- **No automatic dark mode:** it would change every existing site.
- **`!important` stays:** the scheme files depend on it.

## Accessibility value changes (owner, 2026-11-01: "yes, please fix all")

- Text on `w3-green`, `w3-blue`, `w3-deep-orange`, `w3-teal`, `w3-red`, `w3-pink`, `w3-blue-grey`, its alias `w3-blue-gray` and their hover classes: white to black. Backgrounds unchanged. This visibly changes existing sites and needs a release note.
- `w3-input` and `w3-select` underline: `#ccc` to `#8a8a8a`, about 3.45:1 on white.

`tests/w3css/compat-check.py` lists these in `CHANGED_VALUES` and fails if one is undone.

## Known differences from the old layout

`tests/w3css/browser-check.py` measures columns and hover content against `w3.css`
in Chrome. It asserts the last two differences below, so a change to either goes red.

- A short column starts on a clean line instead of hooking under a taller neighbor.
- Under `dir="rtl"`, flex rows run right to left.
- Bare text directly in an all-column row becomes a flex item. A selector cannot see text.
- A column with a fixed width wider than its row is capped at the row's width.
  It used to overflow and be cut off by `overflow-x: hidden` on `html`.
- `.w3-rest` beside a column that fills the line drops below it at full width. It
  used to have zero width.

## Keyboard limits

- Shift+Tab reaches a closed menu's button, not its links: the links have no box
  until the menu opens. Tab forward from the button to reach them.
- `.w3-display-hover` links can take focus only if the container can: it needs
  `tabindex` or a focusable child outside the hidden content.
- Escape to dismiss (WCAG 1.4.13) is not possible in CSS.
