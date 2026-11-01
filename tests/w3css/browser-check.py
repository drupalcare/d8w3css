#!/usr/bin/env python3
"""Real-browser check of css/w3-css/w3-modern.css against the original w3.css.

Static checks (compat-check.py) cannot see geometry or focus, so this drives
Chrome through a chromedriver already listening on localhost:4444. It never
starts Chrome or chromedriver itself. Pages are temporary file:// fixtures.

Each failure names a group:
  D1-keyboard  Tab or Shift+Tab stops on BODY, or skips links in open content.
  D1-mouse     A mouse click leaves hover content stuck open, or hover stops working.
  D2           A row with a set height spreads its wrapped lines apart.
  D3           A column set to width:auto overflows, or the width cap is gone.
  D4           .w3-rest does not land where the old floats put it.
  CONSOLE      The browser logged something, or the log is not live.

Not covered: Firefox, Safari, screen readers, touch, a real Drupal page.

Usage:
  browser-check.py              check the real file
  browser-check.py --css PATH   check another copy of the new file
  browser-check.py --self-test  prove each group goes red on a broken copy

Exit: 0 pass, 1 a check failed, 2 the harness broke (no driver, bad mutation).
"""

import argparse
import json
import sys
import tempfile
import time
import urllib.error
import urllib.request
from pathlib import Path

sys.dont_write_bytecode = True

DRIVER = "http://localhost:4444"
ROOT = Path(__file__).resolve().parents[2]
OLD = ROOT / "css/w3-css/w3.css"
NEW = ROOT / "css/w3-css/w3-modern.css"
BASE = ROOT / "css/base/base.css"
WIDTHS = (390, 768, 1440)
TAB = ""
SHIFT = ""
TOLERANCE = 0.5
OPENER = urllib.request.build_opener(urllib.request.ProxyHandler({}))

LONG = "Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod. " * 3


def half(text):
    return f'<div class="w3-half"><div>{text}</div></div>'


# Group, name, row markup. New and old must put every child in the same box.
SAME_AS_OLD = [
    ("D2", "height-400", f'<div class="w3-row" id="r" style="height:400px">{half("a")}{half("b")}{half("c")}{half("d")}</div>'),
    ("D2", "min-height-300", f'<div class="w3-row" id="r" style="min-height:300px">{half("a")}{half("b")}{half("c")}</div>'),
    ("D3", "width-auto", f'<div class="w3-row" id="r"><div class="w3-col" style="width:auto"><p>{LONG}</p></div></div>'),
    ("D4", "rest-then-column", '<div class="w3-row" id="r"><div class="w3-rest"><div>rest</div></div><div class="w3-col" style="width:150px"><div>c</div></div></div>'),
    ("D4", "two-rests", '<div class="w3-row" id="r"><div class="w3-rest"><div>a</div></div><div class="w3-rest"><div>b</div></div></div>'),
    ("D4", "rest-wide-content", '<div class="w3-row" id="r"><div class="w3-col" style="width:300px"><div>c</div></div><div class="w3-rest"><div style="width:600px">wide</div></div></div>'),
    ("D4", "rest-beside-column", f'<div class="w3-row" id="r"><div class="w3-col" style="width:150px"><div>c</div></div><div class="w3-rest"><p>{LONG}</p></div></div>'),
]
WIDE_FIXED = '<div class="w3-row" id="r"><div class="w3-col" style="width:1500px"><div>c</div></div></div>'
REST_BELOW_FULL = f'<div class="w3-row" id="r">{half("a")}<div class="w3-rest"><div>rest</div></div></div>'

KEY_BODY = """
<div class="w3-bar w3-black"><a id="home" href="#x" class="w3-bar-item w3-button">Home</a>
<div class="w3-dropdown-hover"><button id="dd" class="w3-button">Dropdown</button>
<div id="ddc" class="w3-dropdown-content w3-bar-block w3-card-4"><a id="l1" href="#x" class="w3-bar-item w3-button">Link 1</a><a id="l2" href="#x" class="w3-bar-item w3-button">Link 2</a></div></div>
<a id="right" href="#x" class="w3-bar-item w3-button w3-right">Right</a></div>
<div id="dc" class="w3-display-container" style="height:90px" tabindex="0"><div id="dh" class="w3-display-middle w3-display-hover"><a id="dhlink" href="#x">link in display-hover</a></div></div>
<p id="tip" class="w3-tooltip" tabindex="0">tip <span id="tipt" class="w3-text"><a id="tiplink" href="#x">link in tooltip</a></span></p>
<a id="end" href="#x">end</a>
"""
TAB_ORDER = ["home", "dd", "l1", "l2", "right", "dc", "dhlink", "tip", "tiplink", "end"]
ACTIVE = "const a=document.activeElement;return a.id||a.tagName"


class HarnessError(Exception):
    """The instrument broke: not a finding about the CSS."""


class Session:
    """One chromedriver session. Use with `with`, so it is always deleted."""

    def __enter__(self):
        opts = {"args": ["--headless=new", "--disable-gpu", "--window-size=1500,1000", "--allow-file-access-from-files"]}
        caps = {"capabilities": {"alwaysMatch": {"browserName": "chrome", "goog:chromeOptions": opts, "goog:loggingPrefs": {"browser": "ALL"}}}}
        self.id = self.call("POST", "/session", caps)["sessionId"]
        return self

    def __exit__(self, *exc):
        self.call("DELETE", f"/session/{self.id}")

    def call(self, method, path, body=None):
        data = None if body is None else json.dumps(body).encode()
        req = urllib.request.Request(DRIVER + path, data=data, method=method, headers={"Content-Type": "application/json"})
        try:
            with OPENER.open(req, timeout=120) as resp:
                return json.loads(resp.read())["value"]
        except (urllib.error.URLError, OSError) as exc:
            raise HarnessError(f"{method} {path}: {exc}") from exc

    def s(self, method, path, body=None):
        return self.call(method, f"/session/{self.id}{path}", body)

    def js(self, script):
        return self.s("POST", "/execute/sync", {"script": script, "args": []})

    def go(self, path):
        self.s("POST", "/url", {"url": path.as_uri()})

    def width(self, width):
        self.s("POST", "/goog/cdp/execute", {"cmd": "Emulation.setDeviceMetricsOverride", "params": {"width": width, "height": 1000, "deviceScaleFactor": 1, "mobile": False}})
        got = self.js("return window.innerWidth")
        if got != width:
            raise HarnessError(f"viewport: asked {width}, innerWidth says {got}")

    def logs(self):
        return self.s("POST", "/se/log", {"type": "browser"})

    def key(self, shift=False):
        down = [{"type": "keyDown", "value": SHIFT}] if shift else []
        up = [{"type": "keyUp", "value": SHIFT}] if shift else []
        steps = down + [{"type": "keyDown", "value": TAB}, {"type": "keyUp", "value": TAB}] + up
        self.s("POST", "/actions", {"actions": [{"type": "key", "id": "k", "actions": steps}]})
        self.s("DELETE", "/actions")

    def pointer(self, x, y, click=False):
        steps = [{"type": "pointerMove", "duration": 0, "origin": "viewport", "x": int(x), "y": int(y)}]
        if click:
            steps += [{"type": "pointerDown", "button": 0}, {"type": "pointerUp", "button": 0}]
        self.s("POST", "/actions", {"actions": [{"type": "pointer", "id": "p", "actions": steps}]})

    def click_id(self, element):
        x, y = self.js(f"const r=document.getElementById('{element}').getBoundingClientRect();return [r.x+r.width/2,r.y+r.height/2]")
        self.pointer(x, y, click=True)

    def hover_id(self, element):
        x, y = self.js(f"const r=document.getElementById('{element}').getBoundingClientRect();return [r.x+r.width/2,r.y+r.height/2]")
        self.pointer(x, y)

    def display(self, element):
        return self.js(f"return getComputedStyle(document.getElementById('{element}')).display")


def page(tmp, name, css, body, extra=""):
    path = tmp / f"{name}.html"
    path.write_text(
        f'<!doctype html><html lang="en"><head><meta charset="utf-8"><title>{name}</title>'
        f'<link rel="stylesheet" href="{css.as_uri()}">{extra}</head><body>{body}</body></html>'
    )
    return path


def boxes(sess):
    return sess.js(
        "const r=document.getElementById('r');return {sw:document.documentElement.scrollWidth,"
        "h:r.getBoundingClientRect().height,kids:[...r.children].map(e=>{const b=e.getBoundingClientRect();"
        "return [b.x,b.y,b.width,b.height]})}"
    )


def differs(old, new):
    if abs(old["sw"] - new["sw"]) > TOLERANCE or abs(old["h"] - new["h"]) > TOLERANCE or len(old["kids"]) != len(new["kids"]):
        return True
    return any(abs(a - b) > TOLERANCE for x, y in zip(old["kids"], new["kids"]) for a, b in zip(x, y))


def check_layout(sess, tmp, css, fail):
    for group, name, body in SAME_AS_OLD:
        old_page = page(tmp, f"{name}-old", OLD, body)
        new_page = page(tmp, f"{name}-new", css, body)
        for width in WIDTHS:
            sess.width(width)
            sess.go(old_page)
            old = boxes(sess)
            sess.go(new_page)
            new = boxes(sess)
            if differs(old, new):
                fail(group, f"{name} at {width}: old {old} new {new}")
    # Known differences from SOURCE.md: asserted, so the document cannot drift.
    wide = page(tmp, "wide-fixed", css, WIDE_FIXED)
    below = page(tmp, "rest-below", css, REST_BELOW_FULL)
    sess.width(390)
    sess.go(wide)
    got = boxes(sess)
    if abs(got["kids"][0][2] - 390) > TOLERANCE or got["sw"] != 390:
        fail("D3", f"1500px column at 390 should be capped to the row: {got}")
    sess.go(below)
    got = boxes(sess)
    rest = got["kids"][1]
    if abs(rest[2] - 390) > TOLERANCE or rest[1] <= 0:
        fail("D4", f"rest beside a full column at 390 should drop below at full width: {got}")


def check_keyboard(sess, tmp, css, fail):
    variants = {"new": "", "new+base": f'<link rel="stylesheet" href="{BASE.as_uri()}">'}
    for label, extra in variants.items():
        path = page(tmp, f"keys-{label}", css, KEY_BODY, extra)
        for width in (1440, 390):
            sess.width(width)
            sess.go(path)
            forward = []
            for _ in TAB_ORDER:
                sess.key()
                forward.append(sess.js(ACTIVE))
            if forward != TAB_ORDER:
                fail("D1-keyboard", f"{label} at {width}, Tab: {forward}")
            sess.go(path)
            sess.js("document.getElementById('end').focus()")
            backward = []
            for _ in range(5):
                sess.key(shift=True)
                backward.append(sess.js(ACTIVE))
            if "BODY" in backward:
                fail("D1-keyboard", f"{label} at {width}, Shift+Tab: {backward}")
            # Back out of an open menu, one stop at a time.
            sess.go(path)
            sess.js("document.getElementById('home').focus()")
            for _ in range(3):
                sess.key()
            inside = []
            for _ in range(3):
                sess.key(shift=True)
                inside.append(sess.js(ACTIVE))
            if inside != ["l1", "dd", "home"]:
                fail("D1-keyboard", f"{label} at {width}, Shift+Tab out of the menu: {inside}")


def check_mouse(sess, tmp, css, fail):
    path = page(tmp, "mouse", css, KEY_BODY)
    # (what is clicked, its hover host, content that must close, how to reach it)
    cases = [
        ("dd", "dd", "ddc", "button"),
        ("l1", "dd", "ddc", "link in menu"),
        ("dc", "dc", "dh", "display container"),
        ("dhlink", "dc", "dh", "link in display-hover"),
        ("tip", "tip", "tipt", "tooltip host"),
        ("tiplink", "tip", "tipt", "link in tooltip"),
    ]
    for width in (1440, 390):
        sess.width(width)
        for target, host, content, label in cases:
            sess.go(path)
            sess.hover_id(host)
            if sess.display(content) == "none":
                fail("D1-mouse", f"at {width}: hover on {host} did not open {content}")
                continue
            sess.click_id(target)
            sess.pointer(width - 5, 900)
            shown = sess.display(content)
            if shown != "none":
                fail("D1-mouse", f"at {width}: click on {label}, pointer left, {content} stuck {shown}")
        # Keyboard focus inside, then a click on the empty page, closes it.
        sess.go(path)
        sess.js("document.getElementById('home').focus()")
        sess.key()
        sess.key()
        if sess.js(ACTIVE) != "l1":
            continue  # Already reported by the keyboard check.
        sess.pointer(width - 5, 900, click=True)
        shown = sess.display("ddc")
        if shown != "none":
            fail("D1-mouse", f"at {width}: Tab into menu, click on the page, ddc stuck {shown}")


def check_console(sess, tmp, css, fail):
    sess.go(page(tmp, "console", css, KEY_BODY))
    if sess.logs():
        fail("CONSOLE", "page logged before the proof")
    sess.js("console.error('D8W3CSS-CONSOLE-PROOF')")
    if not any("D8W3CSS-CONSOLE-PROOF" in e["message"] for e in sess.logs()):
        raise HarnessError("console capture is not live: the deliberate error was not logged")


def run(css):
    """Returns (failures as (group, detail), console entries seen across the checks)."""
    found = []

    def fail(group, detail):
        found.append((group, detail))

    with tempfile.TemporaryDirectory(prefix="w3-browser-") as tmp_name, Session() as sess:
        tmp = Path(tmp_name)
        check_console(sess, tmp, css, fail)
        sess.width(1440)
        sess.go(page(tmp, "proof", css, "<p>p</p>"))
        if not sess.js("return getComputedStyle(document.documentElement).getPropertyValue('--w3-amber').trim()"):
            raise HarnessError("the stylesheet did not load: --w3-amber is empty")
        check_layout(sess, tmp, css, fail)
        check_keyboard(sess, tmp, css, fail)
        check_mouse(sess, tmp, css, fail)
        for entry in sess.logs():
            fail("CONSOLE", entry["message"][:200])
    return found


OPEN_RULE = ":focus-within:not(:focus:not(:focus-visible), :has(:focus:not(:focus-visible)))"
REST_GATE = ", > .w3-rest ~ :where(.w3-col, .w3-half, .w3-third, .w3-twothird, .w3-threequarter, .w3-quarter, .w3-rest)"
# Name, text to find, replacement, the group that must go red.
MUTATIONS = [
    ("the old :has(:focus-visible) opener", OPEN_RULE, ":has(:focus-visible)", "D1-keyboard"),
    ("plain :focus-within", OPEN_RULE, ":focus-within", "D1-mouse"),
    ("no align-content", "  align-content: flex-start;\n", "", "D2"),
    ("no column max-width", "  max-width: 100%;\n}\n\n/* .w3-rest stays", "}\n\n/* .w3-rest stays", "D3"),
    ("min-content rest basis", "flex: 1 1 1px;", "flex: 1 1 min-content;", "D4"),
    ("rest may precede a column in flex", REST_GATE, "", "D4"),
]


def self_test():
    source = NEW.read_text(encoding="utf-8")
    bad = 0
    real = run(NEW)
    print(f"real file: {'PASS' if not real else 'FAIL ' + str(sorted({g for g, _ in real}))}")
    bad += bool(real)
    with tempfile.TemporaryDirectory(prefix="w3-mutate-") as tmp_name:
        for index, (name, find, replace, must) in enumerate(MUTATIONS):
            if find not in source:
                raise HarnessError(f"mutation '{name}': text not found in w3-modern.css")
            copy = Path(tmp_name) / f"m{index}.css"
            copy.write_text(source.replace(find, replace), encoding="utf-8")
            started = time.time()
            found = run(copy)
            groups = sorted({g for g, _ in found})
            ok = must in groups
            bad += not ok
            print(f"{'ok ' if ok else 'BAD'} {name}: expected {must}, got {groups} ({len(found)} failures, {time.time() - started:.0f}s)")
            for group, detail in found[:2]:
                print(f"      {group}: {detail[:170]}")
    print(f"self-test: {len(MUTATIONS) + 1} cases, {bad} failed")
    return 1 if bad else 0


def main():
    parser = argparse.ArgumentParser(description=__doc__.splitlines()[0])
    parser.add_argument("--css", type=Path, default=NEW)
    parser.add_argument("--self-test", action="store_true")
    args = parser.parse_args()
    try:
        if args.self_test:
            return self_test()
        found = run(args.css.resolve())
    except HarnessError as exc:
        print(f"HARNESS FAILED: {exc}")
        return 2
    for group, detail in found:
        print(f"FAIL {group}: {detail}")
    if found:
        print(f"browser-check: FAIL, {len(found)} failures")
        return 1
    print(f"browser-check: PASS. Layout {len(SAME_AS_OLD)} cases at {len(WIDTHS)} widths, Tab order {len(TAB_ORDER)} stops, mouse 6 clicks at 2 widths, console empty and proven live.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
