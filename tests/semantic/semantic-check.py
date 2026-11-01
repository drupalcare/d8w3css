#!/usr/bin/env python3
"""Checks the semantic HTML fixes in planning/semantic-html-audit.md, section 2.

Static rules read the templates, JS and CSS. With --url, the rendered pages are
read as well: one h1, one main, the skip link target, no nested nav, unique ids.

Each failure names the audit id (DD-D1 to DD-D10).

Usage:
  semantic-check.py                  check this theme's files
  semantic-check.py --root PATH      check another copy of the theme
  semantic-check.py --url http://d12a.test --page /node/2 --page /user/login
  semantic-check.py --self-test      prove every id goes red on the committed
                                     code before these fixes (git HEAD only
                                     while the fixes are uncommitted), and that
                                     the rendered rules go red on bad markup

Exit: 0 pass, 1 a check failed, 2 the harness broke.
"""

import argparse
import html.parser
import re
import subprocess
import sys
import tarfile
import tempfile
import urllib.request
from io import BytesIO
from pathlib import Path

sys.dont_write_bytecode = True

ROOT = Path(__file__).resolve().parents[2]
MENUS = ("menu.html.twig", "menu--responsive-h.html.twig", "menu--responsive-v.html.twig")
ALL_IDS = [f"DD-D{n}" for n in range(1, 12)]
OPENER = urllib.request.build_opener(urllib.request.ProxyHandler({}))


def static_errors(root):
    """Returns (id, message) for each static rule that fails."""
    errors = []

    def read(path):
        return (root / path).read_text(encoding="utf8")

    def need(audit_id, condition, message):
        if not condition:
            errors.append((audit_id, message))

    page = read("templates/layout/page.html.twig")
    html_twig = read("templates/layout/html.html.twig")
    need("DD-D1", 'href="#main-content"' in html_twig and 'id="main-content"' in page,
         "the skip link has no target in page.html.twig")

    for role in ("main", "complementary", "navigation", "banner", "contentinfo"):
        need("DD-D2", f'role="{role}"' not in page, f'page.html.twig still has role="{role}"')
    for tag in ("main", "aside", "footer", "header"):
        need("DD-D2", f"<{tag} " in page, f"page.html.twig has no <{tag}>")
    js = read("js/custom.js")
    need("DD-D2", "' > div').length" not in js,
         "custom.js counts only div columns, so main and aside lose the equal-height layout")

    need("DD-D3", "aria-label=\"{{ 'Main Navigation'|t }}\"" not in page,
         "the menu wrappers repeat the label of the nav the menu block prints")

    for name in MENUS:
        menu = read(f"templates/navigation/{name}")
        for role in ("menubar", "menu", "menuitem", "none"):
            need("DD-D4", f'"role", "{role}"' not in menu and f'role="{role}"' not in menu,
                 f'{name} still has role {role}')
        need("DD-D4", "aria-haspopup" not in menu, f"{name} still has aria-haspopup")
        need("DD-D5", 'href="#"' not in menu, f'{name} still has a <a href="#"> toggle')
        need("DD-D5", '<button type="button"' in menu, f"{name} has no button toggle")
    need("DD-D4", "aria-controls" in js, "custom.js does not set aria-controls on submenu toggles")
    need("DD-D5", "event.detail === 0" in js, "a wide-screen toggle does nothing from the keyboard")
    wide = read("css/theme/min-width-993.css")
    need("DD-D5", ".ul-parent li > button" in wide, "button toggles lost the padding of the links")

    branding = read("templates/block/block--system-branding-block.html.twig")
    need("DD-D6", "<h1" not in branding and "is_front" in branding,
         "the site name is an h1 on every page")
    need("DD-D6", "<h3" not in branding, "the slogan is a heading")
    hooks = read("src/Hook/Drupal8W3cssThemeHooks.php")
    need("DD-D6", "$variables['is_front']" in hooks and "url.path.is_front" in hooks,
         "the branding block has no is_front, or is cached with the h1 of the first page rendered")
    need("DD-D6", "p.site-name" in read("css/components/site-branding.css"),
         "the site name paragraph has no styling")

    messages = read("templates/misc/status-messages.html.twig")
    need("DD-D7", 'role="contentinfo"' not in messages, "messages are marked as contentinfo")
    need("DD-D7", 'role="status"' in messages, "messages have no status role")

    social = page[page.index("Start: Social Media"):page.index("End: Social Media")]
    need("DD-D8", 'class="d8w3css-social-media__list"' in social, "social links are not a list")
    links = social.count("<a ")
    for text, label in (('rel="noopener"', "rel"), ('class="visually-hidden"', "hidden name"),
                        ('aria-hidden="true"', "aria-hidden icon")):
        need("DD-D8", links > 0 and social.count(text) == links, f"a social link lacks its {label}")
    need("DD-D8", "'.d8w3css-social-media > a > i'" not in js,
         "custom.js cannot reach the icons inside the list")

    need("DD-D9", "'Developed & Designed by'|t" in page, "the credit text is not translatable")
    credit = page[page.index("Start: Credit Link"):page.index("End: Credit Link")]
    need("DD-D9", 'rel="noopener"' in credit, "the credit link has no rel")

    for toggle in ("close-nav", "open-nav-inner"):
        need("DD-D11", re.search(rf'<button type="button" id="{toggle}"[^>]*aria-label=', page) is not None,
             f"#{toggle} is not a named button, so the keyboard cannot reach it")
    need("DD-D11", 'aria-controls="main-navigation-v"' in page, "#open-nav-inner does not name the menu it opens")

    breadcrumb = read("templates/navigation/breadcrumb.html.twig")
    need("DD-D10", "<ol " in breadcrumb and "<ul" not in breadcrumb, "the breadcrumb is not an ol")
    need("DD-D10", "ol.breadcrumb" in read("css/components/breadcrumb.css"),
         "the ol breadcrumb has no styling")
    for path in sorted((root / "templates").rglob("*.twig")):
        text = path.read_text(encoding="utf8")
        relative = path.relative_to(root)
        need("DD-D10", not re.search(r'<nav\b[^>]*\brole="navigation"', text),
             f"{relative}: role on nav is redundant")
        need("DD-D10", not re.search(r'<main\b[^>]*\brole="main"', text),
             f"{relative}: role on main is redundant")
        need("DD-D10", not re.search(r'<li\b[^>]*\brole="presentation"', text),
             f"{relative}: presentation on li removes the list item")
    return errors


class Page(html.parser.HTMLParser):
    """Collects what the rendered rules need."""

    VOID = {"area", "base", "br", "col", "embed", "hr", "img", "input", "link", "meta", "source", "track", "wbr"}

    def __init__(self):
        super().__init__()
        self.h1 = 0
        self.main = 0
        self.footer = 0
        self.ids = {}
        self.roles = []
        self.nested_nav = 0
        self.skip_target = False
        self.stack = []

    def handle_starttag(self, tag, attrs):
        attrs = dict(attrs)
        if tag == "svg" or "svg" in self.stack:
            if tag not in self.VOID:
                self.stack.append(tag)
            return
        if tag == "h1":
            self.h1 += 1
        if tag == "main":
            self.main += 1
        if tag == "footer" and "article" not in self.stack:
            self.footer += 1
        if tag == "nav" and "nav" in self.stack:
            self.nested_nav += 1
        if attrs.get("id"):
            self.ids[attrs["id"]] = self.ids.get(attrs["id"], 0) + 1
        if attrs.get("id") == "main-content" and tag == "main":
            self.skip_target = True
        if attrs.get("role"):
            self.roles.append(attrs["role"])
        if tag not in self.VOID:
            self.stack.append(tag)

    def handle_startendtag(self, tag, attrs):
        if tag != "svg" and "svg" not in self.stack:
            self.handle_starttag(tag, attrs)
            if tag not in self.VOID and self.stack and self.stack[-1] == tag:
                self.stack.pop()

    def handle_endtag(self, tag):
        if tag in self.stack:
            while self.stack.pop() != tag:
                pass


def rendered_errors(markup, label):
    """Returns (id, message) for each rendered rule that fails."""
    parsed = Page()
    parsed.feed(markup)
    errors = []
    if not parsed.skip_target or parsed.main != 1:
        errors.append(("DD-D1", f"{label}: {parsed.main} main, skip target {parsed.skip_target}"))
    if parsed.footer != 1:
        errors.append(("DD-D2", f"{label}: {parsed.footer} page footers"))
    if parsed.nested_nav:
        errors.append(("DD-D3", f"{label}: {parsed.nested_nav} nav inside a nav"))
    for role in ("menubar", "menu", "menuitem", "none"):
        if role in parsed.roles:
            errors.append(("DD-D4", f"{label}: role {role} rendered"))
    if parsed.h1 != 1:
        errors.append(("DD-D6", f"{label}: {parsed.h1} h1 elements"))
    if "contentinfo" in parsed.roles:
        errors.append(("DD-D7", f"{label}: role contentinfo rendered"))
    for element_id, count in parsed.ids.items():
        if count > 1:
            errors.append(("HTML", f"{label}: id {element_id} appears {count} times"))
    return errors


def report(errors):
    for audit_id, message in errors:
        print(f"{audit_id}: {message}")


def self_test():
    with tempfile.TemporaryDirectory() as tmp:
        archive = subprocess.run(["git", "-C", str(ROOT), "archive", "HEAD", "templates", "js", "css", "src"],
                                 capture_output=True, check=False)
        if archive.returncode:
            print("harness: git archive failed", file=sys.stderr)
            return 2
        with tarfile.open(fileobj=BytesIO(archive.stdout)) as tar:
            tar.extractall(tmp, filter="data")
        red = {audit_id for audit_id, _ in static_errors(Path(tmp))}
    missing = [i for i in ALL_IDS if i not in red]
    print(f"committed code: {len(red & set(ALL_IDS))} of {len(ALL_IDS)} ids red")
    if missing:
        print(f"NOT red on the committed code (fixes committed already?): {missing}")
        return 1
    good = '<main id="main-content"><h1>x</h1><nav></nav></main><footer></footer>'
    bad = ('<div role="menubar"></div><main><nav><nav></nav></nav><h1>a</h1><h1>b</h1>'
           '<p role="contentinfo" id="x"></p><p id="x"></p></main>')
    if rendered_errors(good, "good"):
        print("rendered rules fail good markup: ", rendered_errors(good, "good"))
        return 1
    red_rendered = {i for i, _ in rendered_errors(bad, "bad")}
    expected = {"DD-D1", "DD-D2", "DD-D3", "DD-D4", "DD-D6", "DD-D7", "HTML"}
    if red_rendered != expected:
        print(f"rendered rules on bad markup: {sorted(red_rendered)}, expected {sorted(expected)}")
        return 1
    print(f"rendered rules: good markup passes, {len(expected)} of {len(expected)} planted groups red")
    return 0


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--root", type=Path, default=ROOT)
    parser.add_argument("--url")
    parser.add_argument("--page", action="append", default=[])
    parser.add_argument("--self-test", action="store_true")
    args = parser.parse_args()
    if args.self_test:
        return self_test()
    errors = static_errors(args.root)
    if args.url:
        for page in ["/"] + args.page:
            try:
                with OPENER.open(args.url + page, timeout=60) as response:
                    markup = response.read().decode("utf8", "replace")
            except OSError as error:
                print(f"harness: {page}: {error}", file=sys.stderr)
                return 2
            errors += rendered_errors(markup, page)
    report(errors)
    print(f"{len(errors)} errors")
    return 1 if errors else 0


if __name__ == "__main__":
    sys.exit(main())
