#!/usr/bin/env python3
"""Fail when the theme's BEM hooks and its old class names drift apart.

Source of truth for JS hooks: the LEGACY_HOOKS table in js/custom.js.
Source of truth for the other classes: COMPONENT_CLASSES and STATE_CLASSES below.

  1. JS: outside that table, the theme's JS selects no old name from it.
  2. Templates: wherever an old class or id is on a line, its BEM class is too,
     and the old one is still there.
  3. CSS: every selector naming an old class has a twin naming the BEM class, in
     the same selector list. Class for class, so the specificity is the same.

Usage:
  bem-hooks-check.py             check the theme, exit 1 on any failure
  bem-hooks-check.py --write     add the missing CSS twins and template classes
  bem-hooks-check.py --self-test prove each check can go red
"""
import argparse
import pathlib
import re
import shutil
import sys
import tempfile

sys.dont_write_bytecode = True

ROOT = pathlib.Path(__file__).resolve().parents[2]
JS_FILES = ['js/custom.js', 'js/full.opacity.on.scroll.js']
TEMPLATES = ['templates/layout/page.html.twig', 'templates/navigation/menu.html.twig',
             'templates/navigation/menu--responsive-h.html.twig', 'templates/navigation/menu--responsive-v.html.twig',
             'templates/navigation/breadcrumb.html.twig']
# The theme's own components, which the JS does not select. On the markup, and in the CSS.
COMPONENT_CLASSES = {
    'li-item': 'd8w3css-menu__item',
    'li-link': 'd8w3css-menu__link',
    'right-arrow': 'd8w3css-menu__right-arrow',
    'toggle-parent': 'd8w3css-menu__toggle-parent',
    'toggle-child': 'd8w3css-menu__toggle-child',
    'footer-menu-inner': 'd8w3css-footer-menu__inner',
    'ul-responsive-v': 'd8w3css-vertical-nav',
    'close-nav': 'd8w3css-main-nav__close',
    'open-nav-inner': 'd8w3css-main-nav__open',
    'd8-fade': 'd8w3css-fade',
}
# Added by the JS at runtime, so they are in the CSS but never in a template.
STATE_CLASSES = {
    'show': 'd8w3css-menu__submenu--open',
    'd8-has-image': 'd8w3css-link--has-image',
    'breadcrumb-found': 'd8w3css-main-box--breadcrumb-found',
}


CLASS_TEXT = re.compile(r"""class\s*=\s*"[^"]*"|'[^']*'""")


def legacy_hooks(root):
    text = (root / 'js/custom.js').read_text()
    block = re.search(r'const LEGACY_HOOKS = \{(.*?)\};', text, re.S)
    if not block:
        sys.exit('HARNESS: no LEGACY_HOOKS table in js/custom.js')
    return dict(re.findall(r"'([^']+)': '(d8w3css-[^']+)'", block.group(1)))


def class_map(root, with_state=True):
    out = {}
    for key, hook in legacy_hooks(root).items():
        if '.' in key:
            out[key.split('.')[-1]] = hook
    out.update(COMPONENT_CLASSES)
    if with_state:
        out.update(STATE_CLASSES)
    return out


def token(name):
    return re.compile(r'(?<![\w-])' + re.escape(name) + r'(?![\w-])')


def in_class_text(name, line):
    """The class values on a line: a class attribute, or a quoted Twig class list."""
    return any(token(name).search(m.group(0)) for m in CLASS_TEXT.finditer(line))


def css_files(root):
    for path in sorted((root / 'css').rglob('*.css')):
        # css/w3-css holds the W3.CSS library and its colour files: never renamed.
        if path.relative_to(root / 'css').parts[0] != 'w3-css':
            yield path


def preludes(text):
    """Yields (start, end) of every style rule's selector list."""
    i, start, size = 0, 0, len(text)
    while i < size:
        if text.startswith('/*', i):
            end = text.find('*/', i)
            i = size if end < 0 else end + 2
            start = i
        elif text[i] == ';' or text[i] == '}':
            i += 1
            start = i
        elif text[i] == '{':
            head = text[start:i].strip()
            if head.startswith('@') and re.match(r'@(media|supports|container|layer)\b', head):
                i += 1
                start = i
                continue
            if head and not head.startswith('@'):
                yield start, i
            depth, i = 1, i + 1
            while i < size and depth:
                depth += {'{': 1, '}': -1}.get(text[i], 0)
                i += 1
            start = i
        else:
            i += 1


def twin_of(selector, mapping):
    def swap(match):
        return '.' + mapping.get(match.group(1), match.group(1))
    return re.sub(r'\.(-?[A-Za-z_][\w-]*)', swap, selector)


def split_list(prelude):
    return [p.strip() for p in prelude.split(',') if p.strip()]


def missing_twins(text, mapping):
    out = []
    for start, end in preludes(text):
        parts = split_list(text[start:end])
        for part in parts:
            twin = twin_of(part, mapping)
            if twin != part and twin not in parts:
                out.append((start, end, part, twin))
    return out


def write_css(root):
    mapping = class_map(root)
    changed = 0
    for path in css_files(root):
        text = path.read_text(encoding='utf-8')
        found = missing_twins(text, mapping)
        if not found:
            continue
        by_end = {}
        for _, end, _, twin in found:
            by_end.setdefault(end, []).append(twin)
        for end in sorted(by_end, reverse=True):
            head = text[:end].rstrip()
            gap = text[len(head):end]
            line = head.rsplit('\n', 1)[-1]
            indent = line[:len(line) - len(line.lstrip())]
            text = head + ''.join(f',\n{indent}{t}' for t in by_end[end]) + gap + text[end:]
        path.write_text(text, encoding='utf-8')
        changed += 1
    return changed


def template_pairs(root):
    """Old name to hook, as (kind, name, hook). A template id gets its hook in the class attribute."""
    pairs = [('id', k[1:], h) for k, h in legacy_hooks(root).items() if k.startswith('#')]
    pairs += [('class', k, h) for k, h in class_map(root, with_state=False).items()]
    return pairs


def add_class(line, name, hook):
    done = []

    def swap(match):
        if done or not token(name).search(match.group(0)):
            return match.group(0)
        done.append(1)
        return token(name).sub(lambda t: f'{t.group(0)} {hook}', match.group(0), count=1)
    return CLASS_TEXT.sub(swap, line)


def write_templates(root):
    pairs = template_pairs(root)
    for rel in TEMPLATES:
        path = root / rel
        out = []
        for line in path.read_text().split('\n'):
            for kind, name, hook in pairs:
                if token(hook).search(line):
                    continue
                if kind == 'id':
                    line = re.sub(r'(id="' + re.escape(name) + r'"\s+class\s*=\s*")', lambda m: m.group(1) + hook + ' ', line)
                else:
                    line = add_class(line, name, hook)
            out.append(line)
        path.write_text('\n'.join(out))


def check(root):
    errors = []
    hooks = legacy_hooks(root)
    # 1. JS selects no old name outside the table.
    for rel in JS_FILES:
        text = (root / rel).read_text()
        text = re.sub(r'const LEGACY_HOOKS = \{.*?\};', '', text, flags=re.S)
        text = re.sub(r'/\*.*?\*/', '', text, flags=re.S)
        text = re.sub(r'^\s*//[^\n]*', '', text, flags=re.M)
        if rel != JS_FILES[0]:
            # The fade file adopts its one old class in one marked place.
            text = text.replace("'.d8-fade'", '', 1)
        for literal in re.findall(r"'[^']*'", text):
            for key in hooks:
                if re.search(re.escape(key) + r'(?![\w-])', literal):
                    errors.append(f'JS {rel}: selects old {key} in {literal}')
    # 2. Templates carry both names on one line.
    for rel in TEMPLATES:
        for num, line in enumerate((root / rel).read_text().split('\n'), 1):
            for kind, name, hook in template_pairs(root):
                found = re.search(r'id="' + re.escape(name) + r'"', line) if kind == 'id' else in_class_text(name, line)
                if found and not token(hook).search(line):
                    errors.append(f'TEMPLATE {rel}:{num}: {name} without {hook}')
    # 3. CSS twins.
    mapping = class_map(root)
    for path in css_files(root):
        for _, _, part, twin in missing_twins(path.read_text(encoding='utf-8'), mapping):
            errors.append(f'CSS {path.relative_to(root)}: {part!r} has no twin {twin!r}')
    return errors


def self_test():
    cases = {
        'JS selects an old name': ('js/custom.js', "'.d8w3css-menu__list'", "'.ul-parent'", 'JS'),
        'template loses a hook': ('templates/layout/page.html.twig', ' d8w3css-mobile-nav__toggle', '', 'TEMPLATE'),
        'CSS loses a twin': ('css/components/global-menu.css', 'd8w3css-menu__link-toggle', 'd8w3css-other', 'CSS'),
    }
    failed = 0
    with tempfile.TemporaryDirectory() as tmp:
        base = pathlib.Path(tmp) / 'clean'
        (base).mkdir()
        for part in ('js', 'templates', 'css'):
            shutil.copytree(ROOT / part, base / part)
        clean = check(base)
        print(('PASS' if not clean else 'FAIL'), f'clean copy: {len(clean)} errors')
        failed += bool(clean)
        for name, (rel, old, new, group) in cases.items():
            target = base / rel
            original = target.read_text(encoding='utf-8')
            if old not in original:
                print('HARNESS: mutation does not apply:', name)
                return 2
            target.write_text(original.replace(old, new, 1), encoding='utf-8')
            found = [e for e in check(base) if e.startswith(group)]
            target.write_text(original, encoding='utf-8')
            print(('PASS' if found else 'FAIL'), f'{name}: {len(found)} red')
            failed += not found
    return 1 if failed else 0


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--write', action='store_true')
    parser.add_argument('--self-test', action='store_true')
    args = parser.parse_args()
    if args.self_test:
        return self_test()
    if args.write:
        write_templates(ROOT)
        print('css files changed', write_css(ROOT))
    errors = check(ROOT)
    for error in errors:
        print(error)
    print(f'BEM hooks: {len(legacy_hooks(ROOT))} JS hooks; {len(errors)} errors')
    return 1 if errors else 0


if __name__ == '__main__':
    sys.exit(main())
