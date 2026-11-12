#!/usr/bin/env python3
"""Writes the W3CSS Theme 3 rename list: docs/CLASS-MIGRATION.md and docs/class-migration.html.

Sources, each owned once:
  src/Upgrade/ClassMap.php            2.x classes and ids (the Drush tool reads it)
  src/Upgrade/w3css-rename-map.json   every other kind: variables, JavaScript, library ids, files

Usage:
  python3 scripts/class-list.py            write both documents
  python3 scripts/class-list.py --check    exit 1 when a document differs from what the sources give,
                                           or when the data file differs from the rename map JSON
  --root DIR          theme folder (default: the folder above scripts/)
  --rename-map FILE   rename map JSON to compare with (default: the newest planning/rename-map-w3css-*.json
                      next to the theme folder; skipped when none exists, as in the theme repository)

Exit: 0 in step, 1 drift, 2 the sources are not valid.
"""
import argparse
import collections
import glob
import html
import json
import pathlib
import re
import sys

SCRIPT = 'drush php:script themes/contrib/drupal8_w3css_theme/scripts/migrate-classes.php --'
HANDLING = {
    'rewrite': 'Rewritten',
    'rewrite-yml': 'Rewritten with --yml',
    'report': 'Reported, change by hand',
    'stored': 'Saved by drush updb (setting values) or by the script with --stored (classes in configuration)',
    'unreleased': 'Not needed: this name never shipped',
    'none': 'Nothing to do',
}
KINDS = [
    ('css_variable', 'CSS variables'),
    ('js_behavior', 'JavaScript behaviors'),
    ('once_id', 'Once ids (data-once)'),
    ('data_attribute', 'Data attributes'),
    ('drupal_settings', 'drupalSettings keys'),
    ('storage_key', 'Browser storage keys'),
    ('library', 'Library ids'),
    ('css_file', 'CSS files'),
    ('js_file', 'JavaScript files'),
    ('stored_value', 'Stored values'),
    ('class', 'Classes of the 3.0 development branch'),
    ('id', 'Ids of the 3.0 development branch'),
    ('framework_keep', 'Framework names kept'),
]
CLASSMAP_SECTIONS = ['structural', 'ids', 'state', 'generic', 'context', 'removed', 'vocabulary', 'frozen']
REQUIRED = ('kind', 'old', 'new', 'action', 'handling', 'source')


def fail(code, message):
    print(message)
    sys.exit(code)


def parse_classmap(path):
    """Reads the sections of ClassMap::MAP without PHP (the CI image has none)."""
    sections = {}
    current = None
    in_map = False
    pair = re.compile(r"^      '((?:[^'\\]|\\.)*)' => (?:'((?:[^'\\]|\\.)*)'|NULL),$")
    item = re.compile(r"^      '((?:[^'\\]|\\.)*)',$")
    unescape = lambda s: re.sub(r"\\(['\\])", r'\1', s)
    for number, line in enumerate(path.read_text().splitlines(), 1):
        if line.startswith('  public const MAP = ['):
            in_map = True
            continue
        if not in_map:
            continue
        if line == '  ];':
            break
        head = re.match(r"^    '([a-z]+)' => \[(\],)?$", line)
        if head:
            current = head.group(1)
            sections[current] = {} if current != 'frozen' else []
            if head.group(2):
                current = None
            continue
        if current is None or line.strip() == '' or line.strip().startswith('//'):
            continue
        if line == '    ],':
            current = None
            continue
        match = pair.match(line)
        if match and current != 'frozen':
            sections[current][unescape(match.group(1))] = unescape(match.group(2)) if match.group(2) is not None else None
            continue
        match = item.match(line)
        if match and current == 'frozen':
            sections[current].append(unescape(match.group(1)))
            continue
        fail(2, f'MAP PROBLEM: cannot read {path.name} line {number}: {line.strip()}')
    for name in CLASSMAP_SECTIONS:
        if name not in sections:
            fail(2, f'MAP PROBLEM: section {name} not found in {path.name}')
    return sections


def load_data(path):
    try:
        data = json.loads(path.read_text())
    except (OSError, ValueError) as error:
        fail(2, f'MAP PROBLEM: cannot read {path}: {error}')
    rows = data.get('rows')
    if not isinstance(rows, list) or not rows:
        fail(2, f'MAP PROBLEM: {path.name} has no rows')
    for number, row in enumerate(rows, 1):
        for key in REQUIRED:
            if not isinstance(row.get(key), str):
                fail(2, f'MAP PROBLEM: row {number} has no text for {key}: {row}')
        if row['handling'] not in HANDLING:
            fail(2, f"MAP PROBLEM: row {number} has an unknown handling {row['handling']}")
        if row['kind'] not in dict(KINDS):
            fail(2, f"MAP PROBLEM: row {number} has an unknown kind {row['kind']}")
    counted = collections.Counter(r['kind'] for r in rows if r['source'] == 'rename-map')
    if dict(counted) != data.get('counts'):
        fail(2, f"MAP PROBLEM: counts in the file {data.get('counts')} differ from its rename-map rows {dict(counted)}")
    return data


def code(value):
    return f'`{value}`' if re.fullmatch(r'[\w.\-/:<>*{}\[\]$@+=]+', value) else value


def cell(value):
    return value.replace('|', '\\|').replace('\n', ' ')


def rows_by_kind(data):
    grouped = collections.defaultdict(list)
    for row in data['rows']:
        grouped[row['kind']].append(row)
    return grouped


def handling_text(row):
    text = HANDLING[row['handling']]
    if row.get('alias'):
        text += '; the old id keeps working as a deprecated alias'
    return text


def build_md(sections, data):
    s = SCRIPT
    out = [
        '# CSS class migration: W3CSS Theme 2 to W3CSS Theme 3', '',
        'Generated from `src/Upgrade/ClassMap.php` and `src/Upgrade/w3css-rename-map.json` by `scripts/class-list.py`. Do not edit by hand. A readable copy for site builders is `docs/class-migration.html`.', '',
        'W3CSS Theme 3 renames its own classes, ids, CSS variables, JavaScript names, library ids and files to names with the `w3css-` prefix. W3.CSS library names (`w3-*`, `--w3-*`) do not change. A sub-theme or custom module that uses the old names stops matching until it is migrated. The theme ships a script that does the renaming for you; change the script path below if the theme is not in `themes/contrib`.', '',
        '## Before you start: let Drush do the renaming', '',
        '1. Run `drush updb` first, so the theme settings are in their new places.',
        '2. Save a copy of your theme (or commit it to Git).',
        '3. Dry run, with a report written outside the web root (run from the project folder):', '',
        '   ```', f'   {s} mytheme --stored --content --report=w3css-report.md', '   ```', '',
        '   Nothing is written. The target is a theme machine name or a folder inside the Drupal root (`web/themes/custom/mytheme`, `web/modules/custom/my_module`). Drupal core, the W3CSS Theme itself and the public files folder are refused, and symbolic links are never followed.',
        '4. Read the report. Configuration and content come first, then each file and its changed lines, then the items to check by hand.',
        f'5. Apply: `{s} mytheme --apply`. Add `--stored` to save the configuration changes, `--content` to save id changes in content (see how `--content` saves, below), and `--yml` to rewrite library ids in `*.info.yml` and `*.libraries.yml`.',
        '6. Clear the caches: `drush cr`, then check your pages.', '',
        'What the script changes: `.css .scss .sass .less .twig .js` files are rewritten; `.theme .php .module .inc` files are only reported (change the PHP by hand). `--ext=css,js` narrows the file types. `*.min.*` files, `node_modules`, `vendor`, `dist` and `.git` are never read.', '',
        'YAML files: unless `--ext` is given, `.yml` files are read for library ids and moved file paths. They are reported. Library ids in `*.info.yml` and `*.libraries.yml` are rewritten only with `--apply --yml`. The old library ids keep working as deprecated aliases for one major version, so a site that does not migrate them still loads.', '',
        'What `--stored` covers: the region color class settings of this theme and its sub-themes, Block Class classes, Views CSS, row and field classes, and text format allowed tags with the matching CKEditor 5 settings. `--content` renames only ids, in formatted text fields and menu link attributes.', '',
        'How `--content` saves: a node, term, reusable block or menu link becomes a new revision, so the old text stays in its history. A paragraph, or a Layout Builder inline block, is saved in place on the revision its page already points at, with no new revision: its page stores that revision number, so a new revision would never be shown. The report marks those rows `(in place, no new revision)`; the report\'s old and new values are the record of what changed, so take a database backup before `--apply --content`. Only the current (default) revision is rewritten; a pending draft keeps the old ids.', '',
        'Links: only a link to the same page (`href="#header"`) is renamed. A link that names another page or site before the `#` (`href="/about#header"`, `href="https://example.org/#header"`) is never changed, because that page may not use the theme; the report lists it as `other-page-link` with the whole address, to check by hand.', '',
        'Report-only rules: a name that is one plain word, or is both a class and an id, is renamed only where the line plainly sets classes (or ids). State classes and generic words (`show`) are renamed only when chained to a `w3css-` selector in a stylesheet; elsewhere they are reported. A once id is rewritten in `once()` calls and `data-once` selectors only. A data attribute read through `dataset` is reported, because its spelling differs.', '',
    ]

    def table(title, rows, cols=('2.x', '3.x')):
        if not rows:
            return
        out.extend([f'## {title} ({len(rows)})', '', f'| {cols[0]} | {cols[1]} |', '|---|---|'])
        for a, b in rows:
            out.append(f'| `{a}` | {b} |')
        out.append('')

    sec = sections
    table('Classes', [(k, code(v)) for k, v in sec['structural'].items()])
    table('Ids', [(k, code(v)) for k, v in sec['ids'].items()])
    table('State classes (set by the theme JavaScript)', [(k, code(v)) for k, v in sec['state'].items()])
    table('Generic words (chained to a theme selector only)', [(k, code(v)) for k, v in sec['generic'].items()])
    table('Context-dependent (pick by hand)', list(sec['context'].items()), ('2.x', 'Use'))
    table('Removed (no 3.x element)', list(sec['removed'].items()), ('2.x', 'Why'))
    if sec['vocabulary']:
        out.extend([f"## Core names kept ({len(sec['vocabulary'])})", '', "These core or Classy names are still printed beside the theme's own name, so selectors that use them keep working: " + ', '.join(code(x) for x in sec['vocabulary']) + '.', ''])
    if sec['frozen']:
        out.extend([f"## Unchanged on purpose ({len(sec['frozen'])})", '', 'Font and animation value classes are public names and keep their spelling: ' + ', '.join(code(x) for x in sec['frozen']) + '.', ''])

    grouped = rows_by_kind(data)
    out.extend([
        '## Other renames: variables, JavaScript, library ids and files', '',
        'Each table lists the rows that change. The last columns say what the script does with them: `Rewritten` (the script edits the file), `Rewritten with --yml` (only in info and libraries YAML files), `Reported, change by hand`, `Saved by drush updb (setting values) or by the script with --stored (classes in configuration)`, or `Not needed: this name never shipped` (a name that only existed on the 3.0 development branch; the 2.x names above already go straight to the final name). Rows that keep their name are counted, not listed; `docs/class-migration.html` shows them all.', '',
    ])
    for kind, title in KINDS:
        rows = grouped.get(kind, [])
        if not rows:
            continue
        changing = [r for r in rows if r['action'] != 'keep']
        kept = len(rows) - len(changing)
        out.extend([f'### {title} ({len(rows)})', ''])
        if changing:
            out.extend(['| Old | New | Action | What the script does | Note |', '|---|---|---|---|---|'])
            for r in changing:
                out.append(f"| {cell(code(r['old']))} | {cell(code(r['new']))} | {r['action']} | {handling_text(r)} | {cell(r.get('note', ''))} |")
            out.append('')
        if kept:
            out.extend([f'{kept} more row(s) keep their name.', ''])
    out.append('A name in braces is a pattern: `{n}` is a number, `{menu}` a cleaned menu name, `{size}` small, medium or large.')
    return '\n'.join(out) + '\n'


def build_html(sections, data):
    esc = html.escape
    grouped = rows_by_kind(data)
    parts = [
        '<!doctype html>', '<html lang="en">', '<head>', '<meta charset="utf-8">',
        '<meta name="viewport" content="width=device-width, initial-scale=1">',
        '<title>W3CSS Theme 3 rename list</title>',
        '<style>',
        'body{font:16px/1.5 system-ui,sans-serif;margin:0 auto;max-width:72rem;padding:1rem;color:#1b1f23;background:#fff}',
        'h1,h2{line-height:1.25}table{border-collapse:collapse;width:100%;margin:.5rem 0 1rem}',
        'th,td{border:1px solid #c9d1d9;padding:.35rem .5rem;text-align:left;vertical-align:top;overflow-wrap:anywhere}',
        'th{background:#eef2f6}code{font-family:ui-monospace,monospace;font-size:.9em}',
        'details{margin:1rem 0}summary{cursor:pointer;font-weight:600;font-size:1.1rem}',
        'a{color:#0b4f9c}a:focus,summary:focus{outline:3px solid #0b4f9c;outline-offset:2px}',
        '.keep{color:#57606a}',
        '</style>', '</head>', '<body>', '<main>',
        '<h1>W3CSS Theme 3 rename list</h1>',
        '<p>Generated from <code>src/Upgrade/ClassMap.php</code> and <code>src/Upgrade/w3css-rename-map.json</code> by <code>scripts/class-list.py</code>. '
        'Do not edit by hand. The same list as a Markdown file is <code>docs/CLASS-MIGRATION.md</code>, with the command lines.</p>',
        '<p>Run the tool as a dry run first: <code>' + esc(f'{SCRIPT} mytheme --stored --content --report=w3css-report.md') + '</code>. '
        'Add <code>--apply</code> to write, and <code>--yml</code> to rewrite library ids in <code>*.info.yml</code> and <code>*.libraries.yml</code>.</p>',
        '<h2 id="summary">Counts</h2>',
        '<table><caption>Names by kind</caption><thead><tr><th scope="col">Kind</th><th scope="col">Rows</th><th scope="col">Rewritten</th>'
        '<th scope="col">Reported</th><th scope="col">Other</th></tr></thead><tbody>',
    ]
    class_rows = [
        ('Classes (2.x to 3.x)', len(sections['structural']), 'classes-2x'),
        ('Ids (2.x to 3.x)', len(sections['ids']), 'ids-2x'),
        ('State classes', len(sections['state']), 'state-2x'),
        ('Generic words', len(sections['generic']), 'generic-2x'),
        ('Context-dependent', len(sections['context']), 'context-2x'),
        ('Removed', len(sections['removed']), 'removed-2x'),
        ('Core names kept', len(sections['vocabulary']), 'vocabulary-2x'),
        ('Unchanged on purpose', len(sections['frozen']), 'frozen-2x'),
    ]
    for label, count, anchor in class_rows:
        parts.append(f'<tr><th scope="row"><a href="#{anchor}">{esc(label)}</a></th><td>{count}</td><td colspan="3">listed below</td></tr>')
    for kind, title in KINDS:
        rows = grouped.get(kind, [])
        if not rows:
            continue
        rewritten = sum(1 for r in rows if r['handling'] in ('rewrite', 'rewrite-yml'))
        reported = sum(1 for r in rows if r['handling'] == 'report')
        parts.append(f'<tr><th scope="row"><a href="#{kind}">{esc(title)}</a></th><td>{len(rows)}</td><td>{rewritten}</td><td>{reported}</td><td>{len(rows) - rewritten - reported}</td></tr>')
    parts.extend(['</tbody></table>'])

    def simple(anchor, title, rows, cols):
        if not rows:
            return
        parts.append(f'<details id="{anchor}" open><summary>{esc(title)} ({len(rows)})</summary>')
        parts.append(f'<table><thead><tr><th scope="col">{esc(cols[0])}</th><th scope="col">{esc(cols[1])}</th></tr></thead><tbody>')
        for a, b in rows:
            parts.append(f'<tr><td><code>{esc(a)}</code></td><td>{esc(b) if cols[1] != "3.x" else "<code>" + esc(b) + "</code>"}</td></tr>')
        parts.append('</tbody></table></details>')

    parts.append('<h2>From 2.x</h2>')
    simple('classes-2x', 'Classes', list(sections['structural'].items()), ('2.x', '3.x'))
    simple('ids-2x', 'Ids', list(sections['ids'].items()), ('2.x', '3.x'))
    simple('state-2x', 'State classes (set by the theme JavaScript)', list(sections['state'].items()), ('2.x', '3.x'))
    simple('generic-2x', 'Generic words (chained to a theme selector only)', list(sections['generic'].items()), ('2.x', '3.x'))
    simple('context-2x', 'Context-dependent (pick by hand)', list(sections['context'].items()), ('2.x', 'Use'))
    simple('removed-2x', 'Removed (no 3.x element)', list(sections['removed'].items()), ('2.x', 'Why'))
    simple('vocabulary-2x', 'Core names kept (still printed beside the theme name)', [(k, v) for k, v in sections['vocabulary'].items()], ('Core name', '3.x twin'))
    parts.append(f"<details id=\"frozen-2x\"><summary>Unchanged on purpose ({len(sections['frozen'])})</summary><p>Font and animation value classes are public names and keep their spelling: "
                 + ', '.join(f'<code>{esc(x)}</code>' for x in sections['frozen']) + '.</p></details>')
    parts.append('<h2>Variables, JavaScript, library ids and files</h2>')
    for kind, title in KINDS:
        rows = grouped.get(kind, [])
        if not rows:
            continue
        parts.append(f'<details id="{kind}"><summary>{esc(title)} ({len(rows)})</summary>')
        parts.append('<table><thead><tr><th scope="col">Old</th><th scope="col">New</th><th scope="col">Action</th>'
                     '<th scope="col">What the script does</th><th scope="col">Note</th></tr></thead><tbody>')
        for r in rows:
            css = ' class="keep"' if r['action'] == 'keep' else ''
            parts.append(f"<tr{css}><td><code>{esc(r['old'])}</code></td><td><code>{esc(r['new'])}</code></td><td>{esc(r['action'])}</td>"
                         f"<td>{esc(handling_text(r))}</td><td>{esc(r.get('note', ''))}</td></tr>")
        parts.append('</tbody></table></details>')
    parts.append('<p>A name in braces is a pattern: <code>{n}</code> is a number, <code>{menu}</code> a cleaned menu name, <code>{size}</code> small, medium or large.</p>')
    parts.extend(['</main>', '</body>', '</html>'])
    return '\n'.join(parts) + '\n'


def compare_rename_map(data, path):
    source = json.loads(path.read_text())
    key = lambda r: (r['kind'], r['old'], r['new'], r['action'])
    theirs = collections.Counter(key(r) for r in source['rows'])
    ours = collections.Counter(key(r) for r in data['rows'] if r['source'] == 'rename-map')
    missing = list((theirs - ours).elements())
    extra = list((ours - theirs).elements())
    return missing, extra


def main():
    parser = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    parser.add_argument('--check', action='store_true')
    parser.add_argument('--root')
    parser.add_argument('--rename-map')
    args = parser.parse_args()
    root = pathlib.Path(args.root).resolve() if args.root else pathlib.Path(__file__).resolve().parent.parent
    sections = parse_classmap(root / 'src/Upgrade/ClassMap.php')
    data = load_data(root / 'src/Upgrade/w3css-rename-map.json')
    olds = set(sections['structural']) | set(sections['ids'])
    news = set(sections['structural'].values()) | set(sections['ids'].values())
    chain = sorted(olds & news)
    if chain:
        fail(2, f'MAP PROBLEM: new names that are also old names (a second run would rename again): {chain}')
    # A 2.x row must land on the final name, not on a name renamed again in 3.0.
    renamed_classes = {r['old'] for r in data['rows'] if r.get('kind') == 'class' and r.get('old') and r.get('new') and r['old'] != r['new']}
    # Ids keep their names (owner, 2026-11-10): sites link to them and target them.
    moved_ids = sorted(r['old'] for r in data['rows'] if r.get('kind') == 'id' and r.get('old') != r.get('new'))
    if moved_ids:
        fail(2, f'MAP PROBLEM: ids are never renamed; these rows rename one: {moved_ids[:10]}')
    stale = sorted(set(sections['structural'].values()) & renamed_classes)
    if stale:
        fail(2, f'MAP PROBLEM: 2.x targets renamed again in 3.0 (point them at the final name): {stale[:10]}')
    texts = {
        root / 'docs/CLASS-MIGRATION.md': build_md(sections, data),
        root / 'docs/class-migration.html': build_html(sections, data),
    }
    total = len(data['rows'])
    kinds = len({r['kind'] for r in data['rows']})
    if not args.check:
        for path, text in texts.items():
            path.parent.mkdir(exist_ok=True)
            path.write_text(text)
            print(f'wrote {path.relative_to(root)} ({len(text.splitlines())} lines)')
        print(f"rows {total} in {kinds} kinds; class map {len(sections['structural'])} classes, {len(sections['ids'])} ids")
        return 0
    problems = []
    for path, text in texts.items():
        if not path.exists() or path.read_text() != text:
            problems.append(f'{path.relative_to(root)} differs from the map: run python3 scripts/class-list.py')
    candidate = pathlib.Path(args.rename_map) if args.rename_map else None
    if candidate is None:
        found = sorted(glob.glob(str(root.parent / 'planning' / 'rename-map-w3css-*.json')))
        candidate = pathlib.Path(found[-1]) if found else None
    if candidate is None:
        compared = 'rename map JSON not found (theme repository): skipped'
    else:
        missing, extra = compare_rename_map(data, candidate)
        if missing or extra:
            problems.append(f'{candidate.name}: {len(missing)} row(s) missing from the data file, {len(extra)} not in the rename map; first: {(missing + extra)[:3]}')
        compared = f'rename map {candidate.name}: ' + ('in step' if not (missing or extra) else 'DRIFT')
    print(f"rows {total} in {kinds} kinds; class map {len(sections['structural'])} classes, {len(sections['ids'])} ids; {compared}")
    if problems:
        print('DRIFT:')
        for problem in problems:
            print(' - ' + problem)
        return 1
    print('IN STEP: class map, data file, Markdown list and HTML list agree')
    return 0


if __name__ == '__main__':
    sys.exit(main())
