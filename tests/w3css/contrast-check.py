#!/usr/bin/env python3
"""Fails when a rule in the W3.CSS color files sets text below 4.5:1.

Reads rules that declare both color and background in hex or rgb(). Colors
through var() are outside it; compat-check.py guards w3-modern.css values.
Run from the theme root. --self-test proves it can fail.
"""
import glob
import re
import sys


def lum(value):
    """Returns relative luminance of a #rgb or #rrggbb color."""
    h = value.lstrip("#")
    if len(h) == 3:
        h = "".join(c * 2 for c in h)
    out = []
    for i in (0, 2, 4):
        x = int(h[i:i + 2], 16) / 255
        out.append(x / 12.92 if x <= .03928 else ((x + .055) / 1.055) ** 2.4)
    return .2126 * out[0] + .7152 * out[1] + .0722 * out[2]


def ratio(a, b):
    """Returns the WCAG contrast ratio of two colors."""
    a, b = lum(a), lum(b)
    return (max(a, b) + .05) / (min(a, b) + .05)


def to_hex(text):
    """Returns a hex color from a CSS value, or None when it cannot be read."""
    text = text.strip().lower()
    m = re.match(r"#([0-9a-f]{3,6})", text)
    if m:
        return "#" + m.group(1)
    m = re.match(r"rgba?\((\d+),\s*(\d+),\s*(\d+)", text)
    if m:
        return "#%02x%02x%02x" % tuple(int(x) for x in m.groups())
    return {"white": "#ffffff", "black": "#000000"}.get(text.split()[0] if text else "")


def failures(css):
    """Returns (selector, ratio) for each rule below 4.5:1."""
    css = re.sub(r"/\*.*?\*/", "", css, flags=re.S)
    found = []
    for sel, body in re.findall(r"([^{}]+)\{([^{}]*)\}", css):
        c = re.search(r"(?<![-\w])color:\s*([^;!}]+)", body)
        b = re.search(r"background(?:-color)?:\s*([^;!}]+)", body)
        if not (c and b):
            continue
        fg, bg = to_hex(c.group(1)), to_hex(b.group(1))
        if fg and bg and ratio(fg, bg) < 4.5:
            found.append((sel.strip(), round(ratio(fg, bg), 2)))
    return found


def main():
    if "--self-test" in sys.argv:
        ok = (round(ratio("#777", "#fff"), 2) == 4.48
              and failures(".a{color:#fff;background-color:#4caf50}")
              and not failures(".a{color:#000;background-color:#4caf50}")
              and failures(".a{color:rgb(255,255,255);background:rgb(76,175,80)}"))
        print("self-test:", "PASS" if ok else "FAIL")
        return 0 if ok else 1
    files = sorted(glob.glob("css/w3-css/w3css-color-libraries-*.css"))
    files.append("css/w3-css/w3css-extended-classes.css")
    bad = 0
    for path in files:
        with open(path, encoding="utf-8") as handle:
            for sel, value in failures(handle.read()):
                print(f"{path}: {sel} {value}")
                bad += 1
    print(f"contrast-check: {len(files)} files, {bad} below 4.5:1")
    return 1 if bad else 0


if __name__ == "__main__":
    sys.exit(main())
