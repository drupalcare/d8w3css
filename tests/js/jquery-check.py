#!/usr/bin/env python3
"""Fail when jQuery comes back anywhere in this theme or its starter sub-theme.

Scans every file except tests/ and prose (.md, .txt):
  NAME    A file named after jQuery, minified or not.
  JS      jQuery, or the $( and $. shorthands, in any .js file, comments included.
  CODE    jQuery in any .yml, .twig, .php, .theme, .inc, .module or .install file.
  DEPEND  A library dependency or attach_library() call on a core or contrib
          library that is not in ALLOWED or EXCEPTIONS. An unknown library fails,
          because core/drupal.ajax and others pull in core/jquery themselves.

Usage:
  jquery-check.py              check the theme, exit 1 on any failure
  jquery-check.py --self-test  prove each group goes red on a planted copy

Exit: 0 pass, 1 a check failed, 2 the harness broke.
"""
import argparse
import pathlib
import re
import shutil
import sys
import tempfile

sys.dont_write_bytecode = True

ROOT = pathlib.Path(__file__).resolve().parents[2]
OWN = {'drupal8_w3css_theme', 'drupal8_w3css_subtheme'}
# Measured against core.libraries.yml: none of these reaches core/jquery.
ALLOWED = {'core/drupal', 'core/drupalSettings', 'core/once'}
# Kept on purpose. Library to the only file allowed to use it, and why.
EXCEPTIONS = {
    # Core's own status report page needs it for its <details>. It does load jQuery, on that
    # admin page only, when this theme is the admin theme. Core's template does the same.
    'core/drupal.collapse': 'templates/admin/status-report-grouped.html.twig',
    # CSS only, no jQuery. Attached only where Claro exists: Drupal 12 core ships none.
    'claro/classy.search-results': 'src/Hook/Drupal8W3cssThemeHooks.php',
}
SKIP_DIRS = {'.git', 'tests', 'node_modules', '__pycache__', '.claude'}
CODE = {'.yml', '.twig', '.php', '.theme', '.inc', '.module', '.install'}
WORD = re.compile(r'jquery', re.I)
SHORTHAND = re.compile(r'(?<![\w$])\$(\(|\.)')


def walk(root):
    for path in root.rglob('*'):
        rel = path.relative_to(root)
        if path.is_file() and not SKIP_DIRS.intersection(rel.parts):
            yield path, rel


def check(root):
    errors = []
    for path, rel in walk(root):
        if WORD.search(path.name):
            errors.append(f'NAME {rel}')
        if path.suffix not in CODE and path.suffix != '.js':
            continue
        text = path.read_text(encoding='utf-8', errors='replace')
        for num, line in enumerate(text.split('\n'), 1):
            if path.suffix == '.js' and (WORD.search(line) or SHORTHAND.search(line)):
                errors.append(f'JS {rel}:{num}: {line.strip()[:80]}')
            elif path.suffix != '.js' and WORD.search(line):
                errors.append(f'CODE {rel}:{num}: {line.strip()[:80]}')
            found = re.findall(r"^\s*-\s*['\"]?([\w-]+/[\w.-]+)", line) if path.name.endswith('.libraries.yml') else []
            found += re.findall(r"attach_library\(\s*'([\w-]+/[\w.-]+)'", line)
            found += re.findall(r"\['library'\]\[\]\s*=\s*'([\w-]+/[\w.-]+)'", line)
            for name in found:
                if name.split('/')[0] in OWN or name in ALLOWED:
                    continue
                if EXCEPTIONS.get(name) != rel.as_posix():
                    errors.append(f'DEPEND {rel}:{num}: {name}')
    return errors


PLANTS = {
    'NAME a minified file': ('js/jquery.tools.min.js', None, 'var a=1;', 'NAME'),
    'JS the word, in code': ('js/custom.js', "'use strict';", "'use strict';\n  jQuery('a');", 'JS'),
    'JS the word, in a comment': ('js/full.opacity.on.scroll.js', "'use strict';", "'use strict';\n  // Needs jQuery.", 'JS'),
    'JS the shorthand': ('js/custom.js', "'use strict';", "'use strict';\n  $('a').hide();", 'JS'),
    'JS in a new minified file': ('js/tools.min.js', None, '!function(e){e.fn.x=1}($);$.ajax();', 'JS'),
    'JS in the sub-theme': ('drupal8_w3css_subtheme/js/d8w3css-subtheme-script.js', "'use strict';", "'use strict';\n  $.ajax();", 'JS'),
    'CODE library dependency': ('drupal8_w3css_theme.libraries.yml', '    - core/once\n', '    - core/once\n    - core/jquery\n', 'CODE'),
    'CODE sub-theme dependency': ('drupal8_w3css_subtheme/drupal8_w3css_subtheme.libraries.yml', '    - core/once\n', '    - core/once\n    - core/jquery\n', 'CODE'),
    'DEPEND a library that pulls it in': ('drupal8_w3css_theme.libraries.yml', '    - core/once\n', '    - core/once\n    - core/drupal.ajax\n', 'DEPEND'),
    'DEPEND attach_library in Twig': ('templates/layout/page.html.twig', '{#', "{{ attach_library('core/drupal.dialog') }}\n{#", 'DEPEND'),
    'DEPEND an exception used elsewhere': ('templates/layout/page.html.twig', '{#', "{{ attach_library('core/drupal.collapse') }}\n{#", 'DEPEND'),
    'DEPEND attached from PHP': ('theme-settings.php', '<?php', "<?php\n$a['#attached']['library'][] = 'core/drupal.ajax';", 'DEPEND'),
}


def self_test():
    failed = 0
    with tempfile.TemporaryDirectory() as tmp:
        base = pathlib.Path(tmp) / 'theme'
        shutil.copytree(ROOT, base, ignore=shutil.ignore_patterns('.git', 'tests', 'fonts', 'images', '*.css', '*.png', '*.ico'))
        clean = check(base)
        print('PASS' if not clean else 'FAIL', f'clean copy: {len(clean)} errors', clean[:3])
        failed += bool(clean)
        for name, (rel, old, new, group) in PLANTS.items():
            target = base / rel
            original = target.read_text() if target.exists() else None
            if old is None:
                target.write_text(new)
            else:
                if original is None or old not in original:
                    print('HARNESS: plant does not apply:', name)
                    return 2
                target.write_text(original.replace(old, new, 1))
            found = [e for e in check(base) if e.startswith(group)]
            if original is None:
                target.unlink()
            else:
                target.write_text(original)
            print('PASS' if found else 'FAIL', f'{name}: {found[:1]}')
            failed += not found
    return 1 if failed else 0


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--self-test', action='store_true')
    if parser.parse_args().self_test:
        return self_test()
    errors = check(ROOT)
    for error in errors:
        print(error)
    print(f'jQuery check: {len(errors)} errors')
    return 1 if errors else 0


if __name__ == '__main__':
    sys.exit(main())
