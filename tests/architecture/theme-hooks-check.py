#!/usr/bin/env python3
"""Fails if the theme goes back to a .theme file or uses an unsupported hook.

Rules: no *.theme in the theme or the sub-theme starter; every #[Hook] in
src/Hook is one core lets a theme implement from 11.3 (change record 3551652);
no static service calls or theme_get_setting() in src/Hook.

Usage: theme-hooks-check.py [--self-test]
Exit: 0 pass, 1 a rule failed.
"""

import re
import shutil
import sys
import tempfile
from pathlib import Path

sys.dont_write_bytecode = True

ROOT = Path(__file__).resolve().parents[2]
SUPPORTED = re.compile(
    r"^(preprocess(_\w+)?|theme|theme_suggestions(_\w+)?_alter|form(_\w+)?_alter"
    r"|css_alter|js_alter|js_settings_alter|library_info_alter|element_info_alter"
    r"|page_attachments_alter|theme_registry_alter|views_pre_render|views_post_render"
    r"|views_ui_display_tab_alter|views_ui_display_top_alter"
    r"|plugin_filter_layout_alter|plugin_filter_layout__layout_alter)$"
)


def errors_in(root):
    errors = []
    for found in (*root.glob("*.theme"), *root.glob("drupal8_w3css_subtheme/*.theme")):
        errors.append(f"{found.relative_to(root)}: themes ship no .theme file")
    hook_files = sorted((root / "src/Hook").glob("*.php"))
    if not hook_files:
        errors.append("src/Hook has no classes")
    for path in hook_files:
        text = path.read_text(encoding="utf8")
        relative = path.relative_to(root)
        hooks = re.findall(r"#\[Hook\('([^']+)'", text)
        if path.name == "Drupal8W3cssThemeHooks.php" and len(hooks) < 20:
            errors.append(f"{relative}: only {len(hooks)} hooks, expected at least 20")
        for hook in hooks:
            if not SUPPORTED.match(hook):
                errors.append(f"{relative}: themes cannot implement {hook}")
        for banned in (r"\Drupal::", "theme_get_setting(", "DeprecationHelper"):
            if banned in text:
                errors.append(f"{relative}: uses {banned}")
    return errors


def self_test():
    if errors_in(ROOT):
        print("the real tree must pass first:", errors_in(ROOT))
        return 1
    hooks = "src/Hook/Drupal8W3cssThemeHooks.php"
    plants = {
        "a .theme file": ("drupal8_w3css_theme.theme", None, "<?php\n"),
        "a sub-theme .theme file": ("drupal8_w3css_subtheme/x.theme", None, "<?php\n"),
        "an unsupported hook": (hooks, "#[Hook('preprocess_html')]", "#[Hook('entity_presave')]"),
        "a static service call": (hooks, "$this->pathMatcher->isFrontPage()", "\\Drupal::service('path.matcher')->isFrontPage()"),
    }
    failed = 0
    for name, (rel, old, new) in plants.items():
        with tempfile.TemporaryDirectory() as tmp:
            copy = Path(tmp)
            (copy / "src/Hook").mkdir(parents=True)
            (copy / "drupal8_w3css_subtheme").mkdir()
            shutil.copyfile(ROOT / hooks, copy / hooks)
            target = copy / rel
            if old is None:
                target.write_text(new, encoding="utf8")
            else:
                text = target.read_text(encoding="utf8")
                assert old in text, old
                target.write_text(text.replace(old, new), encoding="utf8")
            red = bool(errors_in(copy))
        print(f"{'red' if red else 'NOT RED'}: {name}")
        failed += 0 if red else 1
    return 1 if failed else 0


def main():
    if "--self-test" in sys.argv:
        return self_test()
    errors = errors_in(ROOT)
    for error in errors:
        print(error)
    print(f"{len(errors)} errors")
    return 1 if errors else 0


if __name__ == "__main__":
    sys.exit(main())
