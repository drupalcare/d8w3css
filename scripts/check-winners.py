#!/usr/bin/env python3
"""Static cascade-winner scan for W3CSS CSS: who wins today and would LOSE once layered?

Usage (python3 -I, from the theme root):
  check-winners.py --layers [--out FILE] [--control] [--docroot DIR] [--files GLOB ...]
      Lists every declaration of the judged files (default: css/base/* and css/layout/*, the batch 5a
      files) that wins today, all files unlayered, against a core, contrib, PB or VVJ rule, and would lose
      once its file sits in its plan layer (w3css.base, w3css.layout ...). A planted loser and a planted
      keeper always run first as a control. Exit 0, or 2 when the control fails or a judged file is
      missing from css-layers.json.
  check-winners.py --important [--files GLOB ...]
      For each !important declaration of the judged files: would it still win against the rivals it beats
      today if it were a normal declaration in its layer? Lists those that still need it.
  check-winners.py --self-test
      Built-in cases for the parser, the specificity function and the judge. Exit 1 on any failure.

Universe: every CSS file of the theme's library set (libraries/w3css-libraries.json; css/ and fonts/); core
modules, core/misc, modules/contrib and modules/custom of a Drupal docroot (symlinks followed, apex-named
paths skipped, Claro skipped as admin theme). Core, contrib, PB and VVJ stay unlayered; a theme file that
already holds @layer blocks keeps its own layers; a theme file not layered yet is modeled in its plan layer
(components and css/theme -> w3css.theme, utilities -> w3css.utilities, predefined -> w3css.schemes, fonts ->
w3css.base) so in-theme pairs are reported apart as category "theme" and are never moved.
Order key: importance, layer, specificity, load order.
Limits: no markup and no media exclusivity, so it over-reports (a pair may never meet on one page); a
selector with no class, id or type subject (`*`, `:root`) is not judged; inline styles and site custom CSS
are not modeled; the file load order is approximated (category weight + weight, library id). A pass is
evidence, not a browser check.
"""
import argparse
import collections
import fnmatch
import json
import os
import re
import sys

sys.dont_write_bytecode = True

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
DOCROOT = '/Users/alaahaddad/Sites/d11/web'
EXT_DIRS = ('core/modules', 'core/misc', 'modules/contrib', 'modules/custom')
CAT = {'base': -200, 'layout': -100, 'component': 0, 'state': 100, 'theme': 200}
BIG = 10 ** 6
CATEGORIES = ('core', 'contrib', 'PB', 'VVJ', 'theme')
JUDGED = ('css/base/w3css-base.css', 'css/layout/*.css')


# ---- CSS parsing (same parser as Solo's check-winners.py, plus the enclosing @layer) -----------
def strip_comments(t):
    out = []; i = 0; n = len(t)
    while i < n:
        if t.startswith('/*', i):
            j = t.find('*/', i + 2); j = n if j < 0 else j + 2
            out.append(''.join(c if c == '\n' else ' ' for c in t[i:j])); i = j
        elif t[i] in '"\'':
            q = t[i]; j = i + 1
            while j < n and t[j] != q:
                j += 2 if t[j] == '\\' else 1
            out.append(t[i:j + 1]); i = j + 1
        else:
            out.append(t[i]); i += 1
    return ''.join(out)


def split_top(s, ch):
    parts = []; depth = 0; cur = []; q = None
    for c in s:
        if q:
            cur.append(c)
            if c == q: q = None
            continue
        if c in '"\'': q = c
        elif c in '([': depth += 1
        elif c in ')]': depth -= 1
        if c == ch and depth == 0:
            parts.append(''.join(cur)); cur = []
        else:
            cur.append(c)
    parts.append(''.join(cur))
    return parts


OPAQUE = ('@keyframes', '@-webkit-keyframes', '@font-face', '@page', '@property', '@counter-style')


def parse(text):
    """Return (line, at-rule context, selector, property, value, important) per declaration."""
    t = strip_comments(text); rules = []; stack = []; buf = []; i = 0; n = len(t); line = 1; q = None; paren = 0

    def flush(body, sel, ctx, bline):
        for d in split_top(body, ';'):
            if ':' not in d: continue
            p, v = d.split(':', 1); p = p.strip().lower(); v = ' '.join(v.split())
            if not p: continue
            imp = bool(re.search(r'!\s*important\s*$', v, re.I))
            v = re.sub(r'\s*!\s*important\s*$', '', v, flags=re.I)
            for s in split_top(sel, ','):
                rules.append((bline, ctx, ' '.join(s.split()), p, v, imp))
    while i < n:
        c = t[i]
        if c == '\n': line += 1
        if q:
            buf.append(c)
            if c == '\\' and i + 1 < n: buf.append(t[i + 1]); i += 2; continue
            if c == q: q = None
            i += 1; continue
        if c in '"\'': q = c; buf.append(c); i += 1; continue
        if c == '(': paren += 1
        if c == ')': paren -= 1
        if c == '{' and paren == 0:
            pre = ' '.join(''.join(buf).split()); buf = []
            if stack and stack[-1][0] == 'opaque':
                stack.append(('opaque',)); i += 1; continue
            if pre.startswith('@'):
                stack.append(('opaque',) if pre.lower().startswith(OPAQUE) else ('at', pre))
            else:
                stack.append(('rule', pre, line))
            i += 1; continue
        if c == '}' and paren == 0:
            body = ''.join(buf); buf = []
            top = stack.pop() if stack else ('none',)
            if top[0] == 'rule':
                flush(body, top[1], tuple(s[1] for s in stack if s[0] == 'at'), top[2])
            i += 1; continue
        if c == ';' and paren == 0 and not (stack and stack[-1][0] == 'rule'):
            buf = []; i += 1; continue
        buf.append(c); i += 1
    return rules


def layer_of(ctx):
    names = [c[len('@layer '):].strip() for c in ctx if c.startswith('@layer ')]
    return '.'.join(names) if names else None


def specificity(sel):
    s = re.sub(r':where\((?:[^()]|\([^()]*\))*\)', '', sel)

    def mx(m):
        best = (0, 0, 0)
        for a in split_top(m.group(2), ','):
            best = max(best, specificity(a))
        return '\x00%d,%d,%d\x00' % best
    extra = [0, 0, 0]
    while True:
        s2 = re.sub(r':(is|not|has|matches)\(((?:[^()]|\([^()]*\))*)\)', mx, s)
        if s2 == s: break
        s = s2
    for m in re.finditer(r'\x00(\d+),(\d+),(\d+)\x00', s):
        for k in range(3): extra[k] += int(m.group(k + 1))
    s = re.sub(r'\x00[\d,]+\x00', '', s)
    s = re.sub(r'\[[^\]]*\]', lambda m: ' .x ', s)
    a = len(re.findall(r'#[\w-]+', s))
    pe = len(re.findall(r'::[\w-]+|:(?:before|after|first-line|first-letter)\b', s))
    s = re.sub(r'::[\w-]+(\([^)]*\))?|:(?:before|after|first-line|first-letter)\b', ' ', s)
    b = len(re.findall(r'\.[\w-]+', s)) + len(re.findall(r':[\w-]+', s))
    s = re.sub(r'#[\w-]+|\.[\w-]+|:[\w-]+(\([^)]*\))?', ' ', s)
    c = len(re.findall(r'(?<![\w-])[a-zA-Z][\w-]*', s)) + pe
    return (a + extra[0], b + extra[1], c + extra[2])


def split_complex(sel):
    out = []; cur = ''; depth = 0; comb = ''; i = 0; s = sel.strip()
    while i < len(s):
        c = s[i]
        if c in '([': depth += 1
        if c in ')]': depth -= 1
        if depth == 0 and c in ' >+~':
            j = i; cb = ' '
            while j < len(s) and s[j] in ' >+~':
                if s[j] != ' ': cb = s[j]
                j += 1
            out.append((cur, comb)); cur = ''; comb = cb; i = j; continue
        cur += c; i += 1
    out.append((cur, comb))
    return out


def tokens(sel):
    comp = split_complex(sel)[-1][0]
    comp = re.sub(r'::?[\w-]+(\((?:[^()]|\([^()]*\))*\))?', '', comp)
    t = set(re.findall(r'[.#][\w-]+', comp))
    m = re.match(r'[a-zA-Z][\w-]*', comp)
    if m: t.add(m.group(0).lower())
    return t


def rootp(p): return '' if p.startswith('--') else p.split('-')[0]


def conflict(a, b):
    return a == b or (not a.startswith('--') and not b.startswith('--') and (b.startswith(a + '-') or a.startswith(b + '-')))


# ---- the theme's files -------------------------------------------------------------------------
def library_files(root):
    """(file, order key, library) for every css file of the library set, in approximate load order."""
    data = json.load(open(os.path.join(root, 'libraries', 'w3css-libraries.json'), encoding='utf-8'))
    out = []; idx = 0
    for lib in sorted(data['libraries'], key=lambda l: l['id']):
        for cat, items in (lib.get('css') or {}).items():
            for it in items:
                w = (it.get('options') or {}).get('weight', 0)
                out.append((it['path'], (CAT[cat] + w, idx), lib['id'])); idx += 1
    seen = set(); uniq = []
    for f, o, lib in out:
        if f not in seen:
            seen.add(f); uniq.append((f, o, lib))
    return uniq


def plan_layer(f):
    """The layer a not-yet-layered theme file gets under the plan (themes-layers-plan-w3css section 2); None = stays unlayered."""
    if f.startswith('css/admin/') or f.endswith('w3css-host-overrides.css') or f == 'css/w3-css/w3.css': return None
    if f.endswith('w3css-layers.css'): return None
    if f.startswith('css/base/'): return 'w3css.base'
    if f.startswith('css/layout/'): return 'w3css.layout'
    if f.startswith(('css/components/', 'css/theme/')): return 'w3css.theme'
    if f.startswith('css/utilities/'): return 'w3css.utilities'
    if f.startswith('css/predefined/'): return 'w3css.schemes'
    if f.startswith('fonts/'): return None if 'font-awesome' in f else 'w3css.base'
    return None


def theme_decls(root, judged):
    """Every declaration of the library set. d['lay'] = layer the file is in today (None unlayered),
    d['after'] = layer once the plan is built (own @layer, else the plan layer; judged files: the plan layer)."""
    out = []
    for f, o, lib in library_files(root):
        path = os.path.join(root, f)
        if not os.path.exists(path): continue
        rules = parse(open(path, encoding='utf-8').read())
        # A file that already holds @layer blocks keeps its plain rules unlayered (an unlayered tail on purpose).
        tail_unlayered = any(layer_of(r[1]) for r in rules)
        for n, (ln, ctx, sel, p, v, imp) in enumerate(rules):
            lay = layer_of(ctx)
            after = lay or (None if tail_unlayered else plan_layer(f))
            out.append(dict(f=f, n=n, ln=ln, ctx=ctx, sel=sel, p=p, v=v, imp=imp, lay=lay, after=after,
                            o=(o[0], o[1], ln), sp=specificity(sel), lib=lib, judged=f in judged))
    return out


def external(docroot):
    out = []
    for sub in EXT_DIRS:
        for d, ds, fs in os.walk(os.path.join(docroot, sub), followlinks=True):
            ds[:] = sorted(x for x in ds if 'apex' not in x and x not in ('tests', 'node_modules', 'js', 'd8w3css', 'w3css-theme'))
            for fn in sorted(fs):
                if not fn.endswith('.css') or 'apex' in fn: continue
                p = os.path.join(d, fn)
                try:
                    rs = parse(open(p, encoding='utf-8', errors='replace').read())
                except Exception:
                    continue
                rel = p.split('/web/', 1)[-1]
                for n, (ln, ctx, sel, pr, v, imp) in enumerate(rs):
                    lay = layer_of(ctx)
                    out.append(dict(f=rel, n=n, ln=ln, ctx=ctx, sel=sel, p=pr, v=v, imp=imp, lay=('ext:' + lay) if lay else None,
                                    after=('ext:' + lay) if lay else None, o=(-BIG, 0, n), sp=specificity(sel), lib='ext'))
    return out


def category(d):
    if d['lib'] != 'ext': return 'theme'
    f = d['f']
    if f.startswith('core/'): return 'core'
    name = f.split('/')[2] if f.startswith('modules/') and f.count('/') > 2 else ''
    if name.startswith(('paragraphs_bundles', 'pb_import', 'w3css_paragraphs')): return 'PB'
    if name.startswith('vvj'): return 'VVJ'
    return 'contrib'


def layer_rank(layer, ladder, imp):
    """Rank inside one origin: normal favors later layers, !important reverses it; unlayered is last for normal and
    weakest for important. A layer of another stylesheet set (external @layer, name 'ext:...') sits below the w3css
    ladder only if declared first; the theme's ladder file loads first (weight -1000), so external layers rank above it."""
    if layer is None: base = BIG
    elif layer.startswith('ext:'): base = 10 ** 5
    else: base = ladder.index(layer)
    return -base if imp else base


def judge(decls, ext, ladder, important_mode=False):
    """(loser, [rivals]) per judged declaration that wins today and loses after layering.
    important_mode: judge the !important declarations as if normal in their layer (does it still need !important)."""
    pool = [d for d in decls if not d['judged']] + ext
    idx = collections.defaultdict(list)
    for r in pool:
        r['tk'] = tokens(r['sel'])
        for t in r['tk']: idx[(t, rootp(r['p']))].append(r)
    out = []
    for s in decls + [e for e in ext if e.get('judged')]:
        if not s['judged'] or not s['after'] or (important_mode and not s['imp']): continue
        s['tk'] = tokens(s['sel'])
        simp_after = False if important_mode else s['imp']
        today = (s['imp'], layer_rank(None, ladder, s['imp']), s['sp'], s['o'])
        after = (simp_after, layer_rank(s['after'], ladder, simp_after), s['sp'], s['o'])
        rivals = {}
        for t in s['tk']:
            for r in idx[(t, rootp(s['p']))]:
                if id(r) in rivals or r['v'] == s['v'] or not conflict(s['p'], r['p']): continue
                # the rival as it is today and as it will be
                rt = (r['imp'], layer_rank(r['lay'], ladder, r['imp']), r['sp'], r['o'])
                ra = (r['imp'], layer_rank(r['after'], ladder, r['imp']), r['sp'], r['o'])
                if today > rt and after < ra: rivals[id(r)] = r
        if rivals:
            out.append((s, sorted(rivals.values(), key=lambda r: (CATEGORIES.index(category(r)), r['f'], r['ln'], r['p']))))
    return out


def control(ladder):
    """A planted pair so an empty list cannot pass. LOSER wins today by specificity and loses layered (normal vs
    unlayered); STAYS is !important in its layer against a normal unlayered rival and keeps winning."""
    mk = lambda **k: dict(dict(n=0, ctx=(), lay=None, o=(0, 0, 1), judged=False, lib='ext'), **k)
    solo = [mk(f='CONTROL:loser', ln=1, sel='.w3css-page .ctl-box', p='color', v='red', imp=False, after='w3css.theme',
               sp=specificity('.w3css-page .ctl-box'), lib='control', judged=True),
            mk(f='CONTROL:stays', ln=2, sel='.ctl-keep', p='color', v='red', imp=True, after='w3css.theme',
               sp=specificity('.ctl-keep'), lib='control', judged=True)]
    ext = [mk(f='modules/contrib/control/rival.css', ln=1, sel='.ctl-box', p='color', v='blue', imp=False, after=None,
              sp=specificity('.ctl-box'), o=(-BIG, 0, 0)),
           mk(f='modules/contrib/control/rival.css', ln=2, sel='.ctl-keep', p='color', v='blue', imp=False, after=None,
              sp=specificity('.ctl-keep'), o=(-BIG, 0, 1))]
    return solo, ext


def run_control(ladder):
    s, e = control(ladder)
    names = {d['f'] for d, _ in judge(s, e, ladder)}
    return names == {'CONTROL:loser'}


def manifest_status(files, man):
    pats = []
    for k in man['files']:
        rx = re.compile('^' + re.escape(k).replace('\\*', '[^/]*') + '$')
        pats.append((k.count('/') * 1000 + len(k.replace('*', '')), k, rx))
    out = {}
    for f in files:
        hit = [(w, k) for w, k, rx in pats if rx.match(f)]
        out[f] = man['files'][max(hit)[1]] if hit else None
    return out


def write_losers(path, results, decls, ext, docroot, control_msg, important_mode):
    by = collections.OrderedDict()
    for s, rs in results: by.setdefault(s['f'], []).append((s, rs))
    tot = collections.Counter(); moved = collections.Counter()
    for _, rs in results:
        for r in rs: tot[category(r)] += 1
    judged = [d for d in decls if d['judged']]
    title = 'W3CSS !important still needed' if important_mode else 'W3CSS layer losers'
    L = ['# %s (generated by `d8w3css/scripts/check-winners.py %s`)' % (title, '--important' if important_mode else '--layers'), '',
         'Static analysis, no site, no browser. Declarations of the batch files that win today (all theme files compared '
         'unlayered) against a core, contrib, PB or VVJ rule and would LOSE once their file sits in its plan layer. '
         'Category "theme" is an in-theme pair (a theme file already layered, or modeled in its plan layer): reported, never moved. '
         'Docroot for core/contrib: `%s`.' % docroot, '',
         '| | Count |', '|---|---|',
         '| judged files | %d |' % len({d['f'] for d in judged}),
         '| judged declarations | %d |' % len(judged),
         '| theme declarations read | %d |' % len(decls),
         '| external rival declarations | %d |' % len(ext),
         '| judged declarations that would lose | %d |' % len(results),
         '| (declaration, rival) pairs | %d |' % sum(tot.values())]
    L += ['| pairs by rival: %s | %s |' % (c, tot[c]) for c in CATEGORIES]
    ext_only = [(s, [r for r in rs if category(r) != 'theme']) for s, rs in results]
    ext_only = [(s, rs) for s, rs in ext_only if rs]
    L += ['| declarations that lose to an external rival (these move) | %d |' % len(ext_only),
          '| declarations that lose only to an in-theme rival (listed, not moved) | %d |' % (len(results) - len(ext_only))]
    top = collections.Counter((category(r), r['f']) for _, rs in results for r in rs)
    L += ['', 'Rival files with the most pairs (top 25):', '']
    L += ['- [%s] %s: %d' % (c, f, n) for (c, f), n in top.most_common(25)]
    L += ['', 'Control: %s' % control_msg, '',
          'Limits: no markup and no media exclusivity, so it over-reports; a selector with no class, id or type subject '
          '(`*`, `:root`) is not judged; inline styles and site custom CSS are not modeled; the theme load order is '
          'approximated from the library data. Claro is skipped (admin theme). Rivals from admin-only stylesheets of '
          'core and contrib (toolbar, views_ui, layout_builder, off-canvas, claro-like) are still counted here: '
          'the list over-reports for them (needs checking by the person who moves a rule).', '']
    for f, items in sorted(by.items()):
        c = collections.Counter(category(r) for _, rs in items for r in rs)
        L += ['## %s' % f, '',
              'Layer: %s | losing declarations %d | pairs %d (%s)' % (
                  ', '.join(sorted({s['after'] for s, _ in items})), len(items), sum(c.values()),
                  ', '.join('%s %d' % (k, c[k]) for k in CATEGORIES)), '']
        for s, rs in items:
            L.append('- %s:%d `%s` `%s: %s%s` (%s) loses to:' % (s['f'], s['ln'], s['sel'], s['p'], s['v'], ' !important' if s['imp'] else '', s['after']))
            for r in rs:
                L.append('  - [%s] %s:%d `%s` `%s: %s%s`' % (category(r), r['f'], r['ln'], r['sel'], r['p'], r['v'], ' !important' if r['imp'] else ''))
        L.append('')
    open(path, 'w', encoding='utf-8').write('\n'.join(L))


def run_layers(root, docroot, out, control_on, patterns, important_mode):
    man = json.load(open(os.path.join(root, 'css-layers.json'), encoding='utf-8')); ladder = man['ladder']
    files = [f for f, _, _ in library_files(root)]
    judged = {f for f in files if any(fnmatch.fnmatch(f, p) for p in patterns)}
    decls = theme_decls(root, judged)
    ext = external(docroot)
    status = manifest_status(files, man)
    unlisted = sorted(f for f in judged if status.get(f) is None)
    ok = run_control(ladder)
    ctl = 'planted loser listed, planted keeper (important in a layer) not listed: %s' % ('PASS' if ok else 'FAIL')
    if control_on:
        cs, ce = control(ladder)
        decls = decls + cs; ext = ext + ce
    results = judge(decls, ext, ladder, important_mode)
    if control_on:
        listed = any(s['f'] == 'CONTROL:loser' for s, _ in results) and not any(s['f'] == 'CONTROL:stays' for s, _ in results)
        ctl += '; planted into the real run and %s' % ('present in the list' if listed else 'MISSING from the list')
        ok = ok and listed
    if out: write_losers(out, results, decls, ext, docroot, ctl, important_mode)
    tot = collections.Counter(category(r) for _, rs in results for r in rs)
    print('theme files %d (judged %d), declarations %d, external rivals %d' % (len(files), len(judged), len(decls), len(ext)))
    print('%s: %d declarations in %d files, %d pairs (%s)' % ('still need !important' if important_mode else 'would lose', len(results),
          len({s['f'] for s, _ in results}), sum(tot.values()), ', '.join('%s %d' % (c, tot[c]) for c in CATEGORIES)))
    print('control: %s' % ctl)
    for f in unlisted: print('NOT IN css-layers.json: %s' % f)
    return 0 if ok and not unlisted else 2


# ---- self test ---------------------------------------------------------------------------------
def self_test():
    fails = 0

    def check(name, got, want):
        nonlocal fails
        if got != want:
            fails += 1; print('SELF-TEST FAIL %s: got %r want %r' % (name, got, want))
    ladder = ['w3css.locked', 'w3css.reset', 'w3css.tokens', 'w3css.base', 'w3css.layout', 'w3css.library', 'w3css.theme',
              'w3css.schemes', 'w3css.utilities', 'w3css.subtheme', 'w3css.custom']
    check('spec class', specificity('.a .b'), (0, 2, 0))
    check('spec where', specificity(':where(.a) b'), (0, 0, 1))
    check('spec not', specificity('a:not(.x)'), (0, 1, 1))
    p = parse('@layer w3css.base { a { color: red !important; margin: 0 } } @media (min-width:1px) { .x { top: 1px } }')
    check('parse count', len(p), 3)
    check('parse imp', [x[5] for x in p], [True, False, False])
    check('parse layer', layer_of(p[0][1]), 'w3css.base')
    check('parse no layer', layer_of(p[2][1]), None)
    check('keyframes opaque', len(parse('@keyframes k { from { color: red } }')), 0)
    check('imp rank', layer_rank('w3css.layout', ladder, True) > layer_rank(None, ladder, True), True)
    check('normal rank', layer_rank('w3css.layout', ladder, False) < layer_rank(None, ladder, False), True)
    check('control', run_control(ladder), True)
    mk = lambda **k: dict(dict(n=0, ctx=(), lay=None, o=(0, 0, 1), judged=False, lib='ext', imp=False), **k)
    # a keeper: normal, but the rival is weaker by specificity today and a layered rival of equal... (negative case)
    s = mk(f='a.css', ln=1, sel='.a', p='color', v='red', after='w3css.layout', sp=specificity('.a'), lib='t', judged=True)
    e = mk(f='modules/contrib/m/x.css', ln=1, sel='.a', p='color', v='blue', after=None, sp=specificity('.a'), o=(-BIG, 0, 0))
    check('equal specificity: later theme file wins today, loses layered', len(judge([s], [e], ladder)), 1)
    e2 = dict(e, sel='.a .b', sp=specificity('.a .b'), tk=None)
    s2 = dict(s, sel='.a .b .c', sp=specificity('.a .b .c'))
    e3 = dict(e, sel='.a .b .c .d', sp=specificity('.a .b .c .d'))
    check('rival already stronger today: not a flip', len(judge([s2], [e3], ladder)), 0)
    imp = dict(s, imp=True)
    check('!important layered beats a normal unlayered rival', len(judge([imp], [e], ladder)), 0)
    check('!important judged as normal still loses', len(judge([imp], [e], ladder, True)), 1)
    e4 = dict(e, imp=True)
    check('important rival unlayered loses to important layered', len(judge([imp], [e4], ladder)), 0)
    th = mk(f='t.css', ln=1, sel='.z', p='color', v='green', after='w3css.library', lay='w3css.library', sp=specificity('.z'), lib='t')
    r = judge([s], [], ladder + [])
    check('no rivals: nothing', r, [])
    r = judge([dict(s, sel='.a .z', sp=specificity('.a .z'))] + [th], [], ladder)
    check('in-theme layered rival reported', [category(x) for _, rs in r for x in rs], ['theme'])
    print('self-test: %d cases, %d failures' % (16, fails))
    return 1 if fails else 0


def main():
    ap = argparse.ArgumentParser(description=__doc__.split('\n')[0])
    ap.add_argument('--layers', action='store_true', help='list judged declarations that win today and would lose once layered')
    ap.add_argument('--important', action='store_true', help='list !important declarations that still need it')
    ap.add_argument('--out', help='write the grouped list to this markdown file')
    ap.add_argument('--control', action='store_true', help='plant a known loser in the real run')
    ap.add_argument('--docroot', default=DOCROOT)
    ap.add_argument('--files', nargs='*', default=list(JUDGED), help='glob patterns of the judged files, relative to the theme root')
    ap.add_argument('--self-test', action='store_true')
    a = ap.parse_args()
    if a.self_test:
        return self_test()
    if a.layers or a.important:
        return run_layers(ROOT, a.docroot, a.out, a.control, a.files, a.important)
    ap.print_help()
    return 0


if __name__ == '__main__':
    sys.exit(main())
