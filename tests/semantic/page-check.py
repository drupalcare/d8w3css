#!/usr/bin/env python3
"""Drives Chrome over a real Drupal page using this theme.

Needs chromedriver on localhost:4444; never starts it. The site needs a main
menu holding a parent with no link (button toggle) and a parent with a link.
It also needs a block in the primary_menu_vertical region (side menu toggles).

Proves at 390, 768 and 1440: the skip link moves focus to main, Tab never falls
to the body, the mobile menu and dropdowns open and publish aria-expanded and
aria-controls, a wide dropdown opens by hover and by keyboard, sidebars keep
equal-height columns, nothing scrolls sideways, an error message is role=alert,
and the console is empty (capture proved with a deliberate console.error).

Usage: page-check.py --url http://d12a.test --page /node/2 --out DIR
Exit: 0 pass, 1 a check failed, 2 the harness broke.
"""

import argparse
import base64
import json
import sys
import time
import urllib.error
import urllib.request
from pathlib import Path

sys.dont_write_bytecode = True

DRIVER = "http://localhost:4444"
OPENER = urllib.request.build_opener(urllib.request.ProxyHandler({}))
WIDTHS = (390, 768, 1440)
TAB = ""
ENTER = ""
PROOF = "SEMANTIC-CHECK-PROOF"
TAB_STOPS = """const e=document.activeElement;return {tag:e.tagName,cls:(e.className||'').toString().slice(0,50),text:(e.getAttribute('aria-label')||e.textContent||'').trim().slice(0,30),w:e.getBoundingClientRect().width}"""


def call(method, path, body=None):
    data = None if body is None else json.dumps(body).encode()
    request = urllib.request.Request(DRIVER + path, method=method, data=data,
                                     headers={"Content-Type": "application/json"})
    with OPENER.open(request, timeout=60) as response:
        return json.load(response)["value"]


class Browser:

    def __init__(self):
        caps = {"browserName": "chrome", "goog:loggingPrefs": {"browser": "ALL"},
                "goog:chromeOptions": {"args": ["--headless=new", "--no-proxy-server"]}}
        self.base = "/session/" + call("POST", "/session", {"capabilities": {"alwaysMatch": caps}})["sessionId"]

    def js(self, script, *args):
        return call("POST", self.base + "/execute/sync", {"script": script, "args": list(args)})

    def width(self, width):
        call("POST", self.base + "/goog/cdp/execute",
             {"cmd": "Emulation.setDeviceMetricsOverride",
              "params": {"width": width, "height": 900, "deviceScaleFactor": 1, "mobile": False}})

    def go(self, url):
        call("POST", self.base + "/url", {"url": url})
        time.sleep(0.4)

    def find(self, selector):
        found = call("POST", self.base + "/element", {"using": "css selector", "value": selector})
        return next(iter(found.values()))

    def click(self, selector):
        call("POST", f"{self.base}/element/{self.find(selector)}/click", {})
        time.sleep(0.5)

    def type(self, selector, text):
        call("POST", f"{self.base}/element/{self.find(selector)}/value", {"text": text})

    def key(self, key):
        actions = [{"type": "key", "id": "k", "actions": [{"type": "keyDown", "value": key}, {"type": "keyUp", "value": key}]}]
        call("POST", self.base + "/actions", {"actions": actions})
        time.sleep(0.1)

    def hover(self, selector):
        box = self.js("const r=document.querySelector(arguments[0]).getBoundingClientRect();return [Math.round(r.x+r.width/2),Math.round(r.y+r.height/2)]", selector)
        actions = [{"type": "pointer", "id": "m", "actions": [{"type": "pointerMove", "duration": 0, "origin": "viewport", "x": box[0], "y": box[1]}]}]
        call("POST", self.base + "/actions", {"actions": actions})
        time.sleep(0.3)

    def shot(self, path):
        path.write_bytes(base64.b64decode(call("GET", self.base + "/screenshot")))

    def log(self):
        return [entry["message"] for entry in call("POST", self.base + "/se/log", {"type": "browser"})]

    def quit(self):
        call("DELETE", self.base)


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--url", required=True)
    parser.add_argument("--page", default="/node/2")
    parser.add_argument("--out", type=Path, required=True)
    args = parser.parse_args()
    args.out.mkdir(parents=True, exist_ok=True)
    errors = []
    record = {}

    def need(ok, message):
        if not ok:
            errors.append(message)

    try:
        browser = Browser()
    except (urllib.error.URLError, OSError) as error:
        print(f"harness: {error}", file=sys.stderr)
        return 2
    url = args.url + args.page
    visible = "const e=document.querySelector(arguments[0]);return !!e&&e.getClientRects().length>0&&getComputedStyle(e).display!=='none'"
    state = """const b=document.querySelector(arguments[0]);const id=b&&b.getAttribute('aria-controls');const s=id&&document.getElementById(id);return {expanded:b&&b.getAttribute('aria-expanded'),controls:!!s,shown:!!s&&s.getClientRects().length>0}"""
    try:
        for width in WIDTHS:
            tag = f"@{width}"
            browser.width(width)
            browser.go(url)
            need(browser.js("return innerWidth") == width, f"{tag}: the width did not apply")
            browser.js("console.error(arguments[0])", PROOF)
            log = browser.log()
            need(any(PROOF in line for line in log), f"{tag}: console capture is not live")
            need(all(PROOF in line for line in log), f"{tag}: console: {log}")
            need(browser.js("return document.documentElement.scrollWidth<=innerWidth"), f"{tag}: scrolls sideways")
            browser.shot(args.out / f"page-{width}.png")

            browser.key(TAB)
            first = browser.js(TAB_STOPS)
            need("skip-link" in first["cls"], f"{tag}: first Tab stop is {first}")
            browser.key(ENTER)
            need(browser.js("return document.activeElement.id") == "main-content",
                 f"{tag}: the skip link did not move focus to main")

            # The side menu toggles, by keyboard alone.
            browser.go(url)
            side = "return getComputedStyle(document.querySelector('.d8w3css-main-nav--vertical')).display"
            focused = "return document.activeElement.id"
            need(browser.js("return !!document.querySelector('button#open-nav-inner')"),
                 f"{tag}: no button#open-nav-inner; the site needs a block in primary_menu_vertical")
            browser.js("document.querySelector('#open-nav-inner').focus()")
            need(browser.js(focused) == "open-nav-inner", f"{tag}: the side menu opener takes no focus")
            browser.key(ENTER)
            time.sleep(0.3)
            need(browser.js(side) == "block", f"{tag}: Enter did not open the side menu")
            need(browser.js("return document.querySelector('#open-nav-inner').getAttribute('aria-expanded')") == "true",
                 f"{tag}: the opener does not report aria-expanded=true")
            need(browser.js(focused) == "close-nav", f"{tag}: focus did not move to the close button")
            browser.key(ENTER)
            time.sleep(0.3)
            need(browser.js(side) == "none", f"{tag}: Enter did not close the side menu")
            need(browser.js(focused) == "open-nav-inner", f"{tag}: focus did not return to the opener")

            browser.go(url)
            stops = []
            for _ in range(45):
                browser.key(TAB)
                stop = browser.js(TAB_STOPS)
                if stop["tag"] == "BODY":
                    break
                stops.append(stop)
            record[f"tab{tag}"] = stops
            need(len(stops) > 5, f"{tag}: only {len(stops)} Tab stops")
            hidden = [s for s in stops if not s["w"]]
            need(not hidden, f"{tag}: focus lands on hidden elements: {hidden[:3]}")

            toggle = ".d8w3css-main-nav--horizontal button.d8w3css-menu__link-toggle"
            arrow = ".d8w3css-main-nav--horizontal .d8w3css-menu__list > li > .d8w3css-menu__arrow-toggle"
            mobile = ".d8w3css-mobile-nav__toggle"
            browser.go(url)
            if width <= 992:
                browser.click(mobile)
                need(browser.js("return document.querySelector(arguments[0]).getAttribute('aria-expanded')", mobile) == "true",
                     f"{tag}: the mobile toggle did not report expanded")
                need(browser.js(visible, ".d8w3css-main-nav--horizontal .d8w3css-menu__list"), f"{tag}: the mobile menu did not open")
                browser.click(toggle)
                seen = browser.js(state, toggle)
                need(seen == {"expanded": "true", "controls": True, "shown": True}, f"{tag}: button dropdown {seen}")
                browser.shot(args.out / f"menu-open-{width}.png")
                browser.click(toggle)
                seen = browser.js(state, toggle)
                need(seen["expanded"] == "false" and not seen["shown"], f"{tag}: button dropdown did not close {seen}")
                browser.click(arrow)
                seen = browser.js(state, arrow)
                need(seen == {"expanded": "true", "controls": True, "shown": True}, f"{tag}: arrow dropdown {seen}")
            else:
                need(browser.js("return document.querySelectorAll('.d8w3css-main-region__inner > .w3-clear.w3-f-display').length") == 1,
                     f"{tag}: the columns lost the equal-height class")
                browser.hover(".d8w3css-main-nav--horizontal li.li-parent:has(> button)")
                sub = ".d8w3css-main-nav--horizontal li.li-parent:has(> button) > ul"
                need(browser.js(visible, sub), f"{tag}: hover did not open the dropdown")
                browser.shot(args.out / f"menu-open-{width}.png")
                browser.go(url)
                browser.js("document.querySelector(arguments[0]).focus()", toggle)
                browser.key(ENTER)
                time.sleep(0.5)
                seen = browser.js(state, toggle)
                need(seen == {"expanded": "true", "controls": True, "shown": True}, f"{tag}: keyboard dropdown {seen}")
                browser.key(TAB)
                inside = browser.js("return document.activeElement.closest('ul').id===document.querySelector(arguments[0]).getAttribute('aria-controls')", toggle)
                need(inside, f"{tag}: Tab did not enter the open dropdown")
            log = browser.log()
            need(not log, f"{tag}: console after menus: {log}")

            browser.go(args.url + "/user/login")
            browser.type("#edit-name", "semantic-check-no-such-user")
            browser.type("#edit-pass", "x")
            browser.click("#edit-submit")
            need(browser.js("return document.querySelectorAll('.messages--error[role=alert]').length") == 1, f"{tag}: no alert for the error")
            need(browser.js("return document.querySelectorAll('[role=contentinfo]').length") == 0, f"{tag}: role contentinfo present")
            browser.shot(args.out / f"error-{width}.png")
            record[f"console-end{tag}"] = browser.log()
    finally:
        browser.quit()
    (args.out / "record.json").write_text(json.dumps(record, indent=1))
    for error in errors:
        print(error)
    print(f"{len(errors)} errors; tab stops " + ", ".join(f"{k}={len(v)}" for k, v in record.items() if k.startswith("tab")))
    print("console at end of each width:", {k: v for k, v in record.items() if k.startswith("console")})
    return 1 if errors else 0


if __name__ == "__main__":
    sys.exit(main())
