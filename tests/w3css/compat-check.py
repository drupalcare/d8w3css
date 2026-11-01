#!/usr/bin/env python3
"""Fail when css/w3-css/w3-modern.css stops honoring the old W3.CSS 4.15 classes.

Static checks, against the frozen tests/w3css/baseline-4.15.json:
  1. The kept original css/w3-css/w3.css is byte-identical (sha256).
  2. Names: every class of the original exists in the new file, outside
     :where(), :is(), :has() and :not(), so a mention in a gate does not count.
  3. Effect: every old selector still declares the same value for every
     property, with var(), calc() and rem resolved at default settings. The only
     exceptions are REMOVED_SELECTORS, REMOVED_DECLARATIONS and vendor prefixes
     below, each with its reason.
  4. Order: old rules keep their relative order, so the cascade between them
     is unchanged.
  5. Decisions: no @layer, no @import, no auto dark mode, no remote URL.
  6. Library: key w3-css loads the new file, not the old one, and its version
     matches the new file's header; SOURCE.md names the original's sha256.

It does not run a browser, so it cannot see geometry or the flex rows.

Usage:
  compat-check.py              check the real files, exit 1 on any failure
  compat-check.py --css PATH   check another copy of the new file
  compat-check.py --self-test  prove each check can go red
  compat-check.py --write-baseline  create the baseline; refuses if it exists
"""

import argparse
import hashlib
import json
import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
ORIGINAL = ROOT / "css/w3-css/w3.css"
MODERN = ROOT / "css/w3-css/w3-modern.css"
SOURCE_MD = ROOT / "css/w3-css/SOURCE.md"
LIBRARIES = ROOT / "drupal8_w3css_theme.libraries.yml"
BASELINE = Path(__file__).resolve().parent / "baseline-4.15.json"

ROOT_PX = 15.0
BROWSER_DEFAULT_PX = 16.0

# Selector: reason. Rules deleted on purpose.
_IE_RESET = "browsers already display these as block; the rule only served IE"
_MOZ_INNER = "dead Firefox hack, no effect in supported versions"
_MOZ_RING = "replaced by the :focus-visible ring"
_HOVER_OUTLINE = "would remove the ring from a hovered link with keyboard focus"
REMOVED_SELECTORS = {
    **{s: _IE_RESET for s in (
        "article", "aside", "details", "figcaption", "footer",
        "header", "main", "menu", "nav", "section",
    )},
    **{s + "::-moz-focus-inner": _MOZ_INNER for s in (
        "button", "[type=button]", "[type=reset]", "[type=submit]",
    )},
    **{s + ":-moz-focusring": _MOZ_RING for s in (
        "button", "[type=button]", "[type=reset]", "[type=submit]",
    )},
    "a:active": _HOVER_OUTLINE,
    "a:hover": _HOVER_OUTLINE,
}

# (selector, property): reason. Declarations deleted on purpose.
_OUTLINE = "outline: 0 removed the focus ring; :focus-visible replaces it"
REMOVED_DECLARATIONS = {
    (".w3-bar .w3-bar-item", "outline"): _OUTLINE,
    (".w3-bar-block .w3-bar-item", "outline"): _OUTLINE,
    (".w3-modal-content", "outline"): _OUTLINE,
    ("figure", "display"): _IE_RESET,
}
# Selector: reason. Owner, 2026-11-01: accessibility fixes approved ("yes, please fix all").
_CONTRAST = "white text failed 4.5:1 on this background; black text passes"
_UNDERLINE = "#ccc on white is about 1.6:1; #8a8a8a is about 3.45:1"
CHANGED_VALUES = {
    **{(s, "color"): ("#000!important", _CONTRAST) for s in ('.w3-blue', '.w3-hover-blue:hover', '.w3-blue-grey', '.w3-hover-blue-grey:hover', '.w3-blue-gray', '.w3-hover-blue-gray:hover', '.w3-green', '.w3-hover-green:hover', '.w3-deep-orange', '.w3-hover-deep-orange:hover', '.w3-pink', '.w3-hover-pink:hover', '.w3-red', '.w3-hover-red:hover', '.w3-teal', '.w3-hover-teal:hover')},
    (".w3-input", "border-bottom"): ("1px solid #8a8a8a", _UNDERLINE),
    (".w3-select", "border-bottom"): ("1px solid #8a8a8a", _UNDERLINE),
}
DEAD_VENDOR_PROPERTIES = {
    "-ms-text-size-adjust", "-ms-transform", "-ms-user-select",
    "-khtml-user-select", "-moz-user-select",
}
# Its first rule was the removed reset, so its first position moved by design.
ORDER_SKIP = {"figure"}
FORBIDDEN = {
    "@layer": "cascade layers reverse !important against the scheme files",
    "@import": "plain single file",
    "prefers-color-scheme": "no auto dark mode",
    "http://": "no remote resource",
    "https://": "no remote resource",
}


def strip_comments(css):
    # The original starts with a byte order mark, which is not part of a selector.
    return re.sub(r"/\*.*?\*/", "", css.lstrip("\ufeff"), flags=re.S)


def split_top(text, sep):
    """Split on sep outside parentheses, brackets and quotes."""
    parts, depth, quote, cur = [], 0, None, []
    for ch in text:
        if quote:
            quote = None if ch == quote else quote
        elif ch in "\"'":
            quote = ch
        elif ch in "([":
            depth += 1
        elif ch in ")]":
            depth -= 1
        elif ch == sep and depth == 0:
            parts.append("".join(cur))
            cur = []
            continue
        cur.append(ch)
    parts.append("".join(cur))
    return [p.strip() for p in parts if p.strip()]


def parse(css):
    """Return ordered rules: (context, selector, {property: value})."""
    rules = []

    def walk(text, context):
        i, n = 0, len(text)
        while i < n:
            brace = text.find("{", i)
            if brace < 0:
                return
            prelude = " ".join(text[i:brace].split())
            depth, j = 1, brace + 1
            while depth:
                if j >= n:
                    raise ValueError("unbalanced braces near: " + prelude)
                depth += {"{": 1, "}": -1}.get(text[j], 0)
                j += 1
            body = text[brace + 1:j - 1]
            if prelude.startswith("@"):
                walk(body, prelude)
            else:
                decls = {}
                for d in split_top(body, ";"):
                    prop, _, val = d.partition(":")
                    decls[prop.strip()] = " ".join(val.split())
                for sel in split_top(prelude, ","):
                    rules.append((context, sel, decls))
            i = j

    walk(strip_comments(css), "")
    return rules


def strip_functional(selector):
    """Drop :where(), :is(), :has() and :not() with their arguments."""
    out, i = [], 0
    while i < len(selector):
        m = re.compile(r":(where|is|has|not)\(").match(selector, i)
        if not m:
            out.append(selector[i])
            i += 1
            continue
        depth, i = 1, m.end()
        while depth and i < len(selector):
            depth += {"(": 1, ")": -1}.get(selector[i], 0)
            i += 1
    return "".join(out)


def classes_of(rules):
    """Classes that a rule defines; a mention inside a gate does not count."""
    found = set()
    for _context, sel, _decls in rules:
        clean = re.sub(r"\"[^\"]*\"|'[^']*'", "", strip_functional(sel))
        found.update(re.findall(r"\.(-?[_a-zA-Z][\w-]*)", clean))
    return found


def merged(rules):
    """Map (context, selector) to its final declarations, keeping first order."""
    table = {}
    for context, sel, decls in rules:
        table.setdefault((context, sel), {}).update(decls)
    return table


def tokens_of(rules):
    out = {}
    for _context, sel, decls in rules:
        if sel == ":root":
            out.update({k: v for k, v in decls.items() if k.startswith("--")})
    return out


def resolve(value, tokens):
    """Reduce a value to its default-settings form."""
    for _ in range(5):
        value, n = re.subn(
            r"var\((--[\w-]+)\)",
            lambda m: tokens.get(m.group(1), "UNDEFINED" + m.group(1)),
            value,
        )
        if not n:
            break
    important = value.endswith("!important")
    value = value.replace("!important", "").strip()
    value = re.sub(r"\s*,\s*", ",", value)
    value = re.sub(r"calc\(([\d.]+)px \* -1\)", r"-\1px", value)

    def px(number):
        return f"{round(number, 4):g}px"

    value = re.sub(
        r"calc\(([\d.]+)rem / ([\d.]+)\)",
        lambda m: px(float(m.group(1)) / float(m.group(2)) * ROOT_PX),
        value,
    )
    value = re.sub(
        r"(?<![\w.])([\d.]+)rem\b",
        lambda m: px(float(m.group(1)) * ROOT_PX),
        value,
    )
    return value + (" !important" if important else "")


def build_baseline():
    text = ORIGINAL.read_text()
    rules = parse(text)
    table = merged(rules)
    return {
        "source": "css/w3-css/w3.css",
        "header": text.lstrip("\ufeff").splitlines()[0],
        "sha256": hashlib.sha256(ORIGINAL.read_bytes()).hexdigest(),
        "classes": sorted(classes_of(rules)),
        "rules": [
            {"context": c, "selector": s, "declarations": d}
            for (c, s), d in table.items()
        ],
    }


def library_block(yml):
    m = re.search(r"^w3-css:\n((?:[ \t]+.*\n|\n)+)", yml, flags=re.M)
    return m.group(1) if m else ""


def check(css_text, baseline, original_bytes, yml, source_md):
    errors = []
    if hashlib.sha256(original_bytes).hexdigest() != baseline["sha256"]:
        errors.append("ORIGINAL: css/w3-css/w3.css changed; it must stay byte-identical")

    rules = parse(css_text)
    missing = sorted(set(baseline["classes"]) - classes_of(rules))
    errors += [f"NAME missing: .{c}" for c in missing]

    tokens = tokens_of(rules)
    table = merged(rules)
    keys = list(table)
    for rule in baseline["rules"]:
        key = (rule["context"], rule["selector"])
        if key not in table:
            if rule["selector"] not in REMOVED_SELECTORS:
                errors.append(f"EFFECT selector gone: {key}")
            continue
        new = table[key]
        for prop, old_value in rule["declarations"].items():
            if prop in DEAD_VENDOR_PROPERTIES:
                continue
            if (rule["selector"], prop) in REMOVED_DECLARATIONS:
                continue
            old = resolve(old_value, {})
            now = resolve(new.get(prop, "MISSING"), tokens)
            if key == ("", "html") and prop == "font-size":
                pct = re.fullmatch(r"([\d.]+)%", now)
                if pct:
                    now = f"{float(pct.group(1)) / 100 * BROWSER_DEFAULT_PX:g}px"
            allowed = CHANGED_VALUES.get((rule["selector"], prop))
            if allowed:
                # An approved fix is required, so undoing it goes red as well.
                if now != resolve(allowed[0], {}):
                    errors.append(f"EFFECT {key} {prop}: fix lost, now {now!r}")
                continue
            if old != now:
                errors.append(f"EFFECT {key} {prop}: was {old!r}, now {now!r}")

    positions = {k: i for i, k in enumerate(keys)}
    last = -1
    for rule in baseline["rules"]:
        pos = positions.get((rule["context"], rule["selector"]))
        if pos is None or rule["selector"] in ORDER_SKIP:
            continue
        if pos < last:
            errors.append(f"ORDER moved: {rule['context']} {rule['selector']}")
        last = max(last, pos)

    lowered = strip_comments(css_text).lower()
    for needle, why in FORBIDDEN.items():
        if needle in lowered:
            errors.append(f"DECISION {needle} found: {why}")

    block = library_block(yml)
    if "css/w3-css/w3-modern.css" not in block:
        errors.append("LIBRARY: w3-css does not load css/w3-css/w3-modern.css")
    if re.search(r"css/w3-css/w3\.css", block):
        errors.append("LIBRARY: w3-css still loads the old css/w3-css/w3.css")
    version = re.search(r"^\s*version:\s*(\S+)", block, flags=re.M)
    header = re.match(r"/\* W3\.CSS (\S+?):", css_text)
    if not version or not header or version.group(1) != header.group(1):
        errors.append("LIBRARY: version does not match the new file's header")
    if baseline["sha256"] not in source_md:
        errors.append("SOURCE: SOURCE.md does not name the original's sha256")
    return errors


def remove_class(css_text, name):
    """Drop every selector that defines a class, as a careless edit would."""
    token = re.compile(r"\." + re.escape(name) + r"(?![\w-])")

    def fix(match):
        kept = [s for s in split_top(match.group(1), ",")
                if not token.search(strip_functional(s))]
        return (",\n".join(kept) + " {") if kept else "@media not all {"

    return re.sub(r"(?m)^[ \t]*([^@{}/\n][^{}]*?)\s*\{", fix, css_text)


def self_test():
    results = []

    def case(label, ok):
        results.append(ok)
        print(("ok   " if ok else "FAIL ") + label)

    baseline = json.loads(BASELINE.read_text())
    css = MODERN.read_text()
    original = ORIGINAL.read_bytes()
    yml = LIBRARIES.read_text()
    src = SOURCE_MD.read_text()

    # The instrument: known positives, a known negative, the expected count.
    old = classes_of(parse(original.decode()))
    case("parser finds w3-half, s1, m12", {"w3-half", "s1", "m12"} <= old)
    case("parser does not find w3-not-a-class", "w3-not-a-class" not in old)
    case("parser counts 398 classes in the original", len(old) == 398)
    case("value resolver: 2.4rem is 36px", resolve("2.4rem", {}) == "36px")
    case("value resolver: calc(20rem / 15) is 20px",
         resolve("calc(20rem / 15)", {}) == "20px")
    case("value resolver: a dotted number is not rem", resolve(".35em", {}) == ".35em")

    def red(mutated, needle, label, **over):
        args = dict(css_text=mutated, baseline=baseline, original_bytes=original,
                    yml=yml, source_md=src)
        args.update(over)
        errors = check(**args)
        case(f"red: {label}", any(needle in e for e in errors))

    case("green: the real files",
         check(css, baseline, original, yml, src) == [])
    red(remove_class(css, "w3-half"), "NAME missing: .w3-half", "class removed")
    gate_only = remove_class(css, "w3-half") + "\n:where(.w3-half) { color: red; }\n"
    red(gate_only, "NAME missing: .w3-half", "class left only inside :where()")
    red(css.replace("  .w3-col.m6,\n  .w3-half {", "  .w3-col.m6 {"),
        "EFFECT selector gone", "one media selector removed, class still defined")
    red(css.replace("--w3-input-border: #8a8a8a", "--w3-input-border: #ccc"),
        "EFFECT", "input underline back to low contrast")
    red(css.replace("--w3-black: #000", "--w3-black: #fff"),
        "EFFECT", "contrast fix undone through the token")
    red(css.replace("--w3-amber: #ffc107", "--w3-amber: #ffc108"),
        "EFFECT", "palette value changed")
    red(css.replace(".w3-hide {\n  display: none !important;",
                    ".w3-hide {\n  display: none;"),
        "EFFECT", "!important dropped from a class")
    red(css.replace("  padding: var(--w3-space-3) 0;", "  padding: var(--w3-space-2) 0;"),
        "EFFECT", "local .w3-select padding lost")
    red(css.replace(".w3-hover-over:hover,\n", ""),
        "NAME missing: .w3-hover-over", "local .w3-hover-over lost")
    red(css.replace("2.4rem", "2.5rem"), "EFFECT", "h1 default size changed")
    block = ".w3-hide {\n  display: none !important;\n}\n\n"
    red(block + css.replace(block, "", 1), "ORDER", "old rule moved")
    red(css + "\n@layer w3 { .x { color: red; } }\n", "DECISION @layer", "cascade layer added")
    red(css + "\n@media (prefers-color-scheme: dark) { .x { color: red; } }\n",
        "DECISION prefers-color-scheme", "auto dark mode added")
    red(css, "ORIGINAL", "original edited", original_bytes=original + b" ")
    red(css, "LIBRARY: w3-css does not load", "library back on the old file",
        yml=yml.replace("css/w3-css/w3-modern.css", "css/w3-css/w3.css"))
    red(css, "LIBRARY: version", "library version drifted",
        yml=yml.replace("version: 4.15-modern", "version: 4.13"))
    red(css, "SOURCE", "SOURCE.md lost the sha256", source_md="nothing")

    failed = results.count(False)
    print(f"self-test: {len(results)} cases, {failed} failed")
    return 1 if failed else 0


def main():
    ap = argparse.ArgumentParser(description=__doc__.splitlines()[0])
    ap.add_argument("--css", type=Path, default=MODERN)
    ap.add_argument("--self-test", action="store_true")
    ap.add_argument("--write-baseline", action="store_true")
    args = ap.parse_args()

    if args.write_baseline:
        if BASELINE.exists():
            print("Baseline exists. It is frozen: never rewrite it to go green.")
            return 2
        BASELINE.write_text(json.dumps(build_baseline(), indent=1) + "\n")
        print(f"wrote {BASELINE}")
        return 0
    if args.self_test:
        return self_test()

    baseline = json.loads(BASELINE.read_text())
    css = args.css.read_text()
    errors = check(css, baseline, ORIGINAL.read_bytes(), LIBRARIES.read_text(),
                   SOURCE_MD.read_text() if SOURCE_MD.exists() else "")
    new = classes_of(parse(css))
    print(f"baseline classes: {len(baseline['classes'])}; "
          f"new file classes: {len(new)}; added: {len(new - set(baseline['classes']))}")
    print(f"baseline selectors: {len(baseline['rules'])}")
    for e in errors:
        print("RED  " + e)
    print("compat-check: " + ("FAIL, " + str(len(errors)) + " problems" if errors else "PASS"))
    return 1 if errors else 0


if __name__ == "__main__":
    sys.exit(main())
