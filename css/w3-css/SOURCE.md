# W3.CSS in this theme

## Files

- `w3css-drupal.css` (was `w3-modern.css`): what the theme loads (library `w3-css`). Plain CSS in
  cascade layers, edited by hand, no build step.
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
  (`.w3-small` and so on) stay in px.
- **Columns:** flex, only in a row whose element children are all columns, where
  no child floats right and nothing follows a `.w3-rest`. Every other row keeps
  the float rules, which is why those rules stay. Clearfix stays too: it is inert
  in a flex row and needed if a site forces the row back to `display: block`.
- **Focus:** a `:focus-visible` ring replaces the removed outlines. The outline
  uses `!important` (in `w3css.locked`) because the theme's `w3css-base.css` sets `outline: none` on `a:focus`.
- **Keyboard:** dropdown, tooltip and display-hover open on keyboard focus, and
  Tab moves through the links inside them. A mouse click does not hold them open.
- **Motion:** `prefers-reduced-motion` stops library animations and transitions.
- **Removed:** the IE display reset, `-ms-`, `-khtml-` and `-moz-user-select`
  prefixes, `::-moz-focus-inner`, `:-moz-focusring`, the hover outline reset.
- **New classes:** `w3-row-equal`, `w3-start-align`, `w3-end-align`,
  `w3-margin-start`, `w3-margin-end`, `w3-flex`, `w3-flex-wrap`, `w3-grid`,
  `w3-grid-2`, `w3-grid-3`, `w3-grid-4`, `w3-grid-auto`, `w3-gap`.

## Left alone on purpose

- **Cascade layers (changed 2026-11-10):** the old reason, that a layer would let the library's
  `!important` beat the scheme overrides, is gone because `!important` is gone. Ladder:
  `css/base/w3css-layers.css`. Every rule is in a `w3css.*` layer; a color literal appears only
  in `w3css.tokens`.
- **No automatic dark mode:** it would change every existing site.
- **`!important` only in `w3css.locked`:** show/hide classes (`w3-hide`, `w3-show*`, `w3-hide-small/medium/large`,
  `w3-sidebar.w3-collapse`), the margins `w3-main` takes below 993px and the width `w3-animate-input` takes on focus (both beat
  inline styles), the focus ring outline, and reduced motion. Each has its reason in the file.
- **Color classes assign region variables:** `.w3-red` sets `--r-background` and `--r-text`, then paints from
  them, so content inside the region inherits them; `.w3-text-*` sets `--r-text`, `.w3-border-*` sets
  `--r-border`; `.w3-hover-*:hover` paints directly. The text color on each ground is `--w3-<color>-on`.

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

## Modern rebuild (2026-11-10)

Target: Baseline widely available, plus the current and previous major of Chrome
(155, 156), Edge (155, 156), Firefox (158, 159), Safari and iOS Safari (27.1, 27.2),
releases on or before 2026-11-10. Source: web-features 3.45.0 `data.json` (built from
MDN browser-compat-data), fetched 2026-11-10 from unpkg.com; lookup script
`planning/spike-tools/w3css-baseline.py` in the lane repository. Every feature used is
supported by all of those versions.

Removed from the reset because only Internet Explorer, old Edge or old Firefox needed
them: `audio, canvas, progress, video { display: inline-block }`, `audio:not([controls])`,
`template { display: none }`, `a { background-color: transparent }`, the `abbr` border,
`dfn`, `figure` margin (the browser default is the same), `img { border-style: none }`,
`button, input { overflow: visible }`, `optgroup` bold, `legend` table display,
`textarea { overflow: auto }`, checkbox and radio padding, `-webkit-appearance` (now
unprefixed `appearance`, Baseline since 2022), `::-webkit-file-upload-button` (now
`::file-selector-button`). From `w3css-base.css`: the second box-sizing rule (box sizing
now lives once, inherited from `html`), `a img { border: none }`, `svg:not(:root)
{ overflow: hidden }`, a doubled `border` declaration.

Kept on purpose: `-webkit-text-size-adjust` (Safari has no unprefixed name; the unprefixed
one is not Baseline, so it is not used), `-webkit-user-select` and `-webkit-touch-callout`
on buttons (Safari needs the prefix), the two `::-webkit-*` spinner and search rules (no
standard name), the float grid and the float bar (see below), the clearfix pseudo-elements
(display: flow-root would narrow every container beside an outside float).

Visible changes (check on a site):

| Class or element | Before | After | Why |
|---|---|---|---|
| `img`, `picture`, `video`, `canvas`, `svg` | could overflow their column | `max-width: 100%` (zero specificity, any class wins) | media never break a layout |
| `h1` to `h6` | browser line breaks | `text-wrap: balance` | even line lengths in headings |
| `.w3-top`, `.w3-bottom` | `width: 100%` from the static left edge | `inset-inline: 0` | a padded parent no longer pushes the bar sideways and past the right edge |
| `.w3-modal-content` | fixed width could exceed a narrow window between 601 and 992px only if the window was narrower than the width | `max-width: 100%` added | never wider than the modal |
| `.w3-animate-zoom`, `.w3-spin` on a `.w3-display-*` box | the animation replaced the centering transform, so the box jumped off center | `scale` and `rotate` properties, centering kept | combined classes work |
| `.w3-animate-top/left/right/bottom` | animated `top`/`left`/`right`/`bottom` | animate `translate` | same look; no layout work per frame, and a site `top` or `left` on the box is no longer overwritten during the animation |
| `.w3-code`, `.w3-codespan` | `Consolas, "courier new"` | adds `monospace` fallback | systems with neither font showed a serif font |
| `[type="search"]`, buttons | `-webkit-appearance` | `appearance` | same result |
| `legend` | `display: table`, `max-width`, `white-space: normal`, `color: inherit` | browser default | only Internet Explorer needed them |

Not changed, owner decision needed: `.w3-row` and `.w3-bar` stay on floats.
`.w3-row` already becomes flex only when every child is a column (rule in the layout
layer); making every row flex would put headings or text inside a row beside the
columns. `.w3-bar` on flex would reverse the order of several `.w3-right` items
(floats right lay out right to left, flex keeps source order).

Not added: line clamp (`-webkit-line-clamp` could not be confirmed in the data source:
needs checking), `overscroll-behavior` (not Baseline in the data source),
`text-wrap: pretty` (no Firefox), a visually-hidden class (Drupal core has
`.visually-hidden`), sticky (`.w3-sticky` exists), logical spacing classes beyond
`.w3-margin-start/-end` (`.w3-padding-16` and `.w3-section` already set block spacing).

New classes:

| Class | What it does | Example |
|---|---|---|
| `w3-flex-column` | flex, one under the other | `<div class="w3-flex-column w3-gap">` |
| `w3-items-center` | align flex or grid items on the cross axis center | `<div class="w3-flex w3-items-center">` |
| `w3-justify-center` | center items on the main axis | `<div class="w3-flex w3-justify-center">` |
| `w3-justify-between` | first and last item at the edges | `<div class="w3-flex w3-justify-between">` |
| `w3-gap-small`, `w3-gap-large` | gap 8px, 32px (`.w3-gap` is 16px) | `<div class="w3-grid-3 w3-gap-large">` |
| `w3-stack` | vertical stack with an even gap (`--w3-stack-gap`, default 16px) | `<div class="w3-stack"><p>..</p><p>..</p></div>` |
| `w3-cluster` | wrapping row with an even gap (`--w3-cluster-gap`, default 8px) | `<div class="w3-cluster"><a class="w3-tag">..</a></div>` |
| `w3-place-center` | content exactly in the middle | `<div class="w3-place-center w3-screen-height">` |
| `w3-ratio-square`, `w3-ratio-photo`, `w3-ratio-video` | 1:1, 4:3, 16:9 box | `<div class="w3-ratio-video"><iframe class="w3-cover">` |
| `w3-cover` | fill the box and crop | `<img class="w3-cover" ...>` |
| `w3-screen-height` | at least the visible screen height (`100vh`, then `100dvh`) | `<section class="w3-screen-height">` |
| `w3-truncate` | one line with an ellipsis | `<p class="w3-truncate">` |
| `w3-query-container` | the box answers `@container` rules | `<div class="w3-query-container"><div class="w3-card">` |
| `w3-snap-row` | sideways scrolling row that stops on each child | `<div class="w3-snap-row"><div class="w3-card">` |
| `w3-grid-auto` (setting) | `--w3-grid-min` sets the smallest column (default 16rem) | `<div class="w3-grid-auto" style="--w3-grid-min: 12rem">` |
