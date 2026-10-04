#!/usr/bin/env python3
"""
Generates seed (placeholder) PHPUnit test files for every class in src/app.

Usage (from project root):
    python seed_tests.py            # create files
    python seed_tests.py --dry-run  # only show what would happen
"""

import re
import sys
from pathlib import Path

# ---------- Config ----------
SRC_DIR = "src/app"
TESTS_DIR = "tests/app"
TESTS_NS_ROOT = "Tests\\App"  # adjust to match composer.json autoload-dev
# ----------------------------

BLOCK_COMMENT = re.compile(r"/\*.*?\*/", re.DOTALL)
LINE_COMMENT = re.compile(r"(//|#(?!\[)).*$", re.MULTILINE)
DECLARATION = re.compile(
    r"^\s*(?:(?:abstract|final|readonly)\s+)*(?:class|interface|trait|enum)\s+([A-Za-z_]\w*)",
    re.MULTILINE,
)

TEMPLATE = """<?php

/**
 * Seed test for: {cls}
 * Source: {src}
 * TODO: write the actual tests.
 */

declare(strict_types=1);

namespace {ns};

use PHPUnit\\Framework\\TestCase;

class {cls}Test extends TestCase
{{
    public function testPlaceholder(): void
    {{
        $this->markTestIncomplete('Tests for {cls} are not written yet.');
    }}
}}
"""


def find_declared_type(code: str):
    """Return the first declared class/interface/trait/enum name, or None."""
    code = BLOCK_COMMENT.sub("", code)
    code = LINE_COMMENT.sub("", code)
    match = DECLARATION.search(code)
    return match.group(1) if match else None


def main() -> int:
    dry_run = "--dry-run" in sys.argv
    root = Path.cwd()
    src_root = root / SRC_DIR

    if not src_root.is_dir():
        print(f"Directory not found: {src_root} (run from project root)", file=sys.stderr)
        return 1

    created, ignored, skipped = [], [], []

    for path in sorted(src_root.rglob("*.php")):
        rel = path.relative_to(src_root)
        src_rel = f"{SRC_DIR}/{rel.as_posix()}"
        cls = find_declared_type(path.read_text(encoding="utf-8", errors="replace"))

        if cls is None:
            ignored.append(src_rel)
            continue

        test_dir = Path(TESTS_DIR) / rel.parent
        test_path = test_dir / f"{cls}Test.php"

        if (root / test_path).exists():
            skipped.append(test_path.as_posix())
            continue

        ns_parts = [TESTS_NS_ROOT] + list(rel.parent.parts)
        ns = "\\".join(ns_parts)

        if not dry_run:
            (root / test_dir).mkdir(parents=True, exist_ok=True)
            (root / test_path).write_text(
                TEMPLATE.format(cls=cls, src=src_rel, ns=ns), encoding="utf-8"
            )
        created.append(test_path.as_posix())

    prefix = "[DRY RUN] " if dry_run else ""
    print(f"{prefix}Created: {len(created)} test file(s)")

    print(f"\nIgnored (no class): {len(ignored)}")
    for f in ignored:
        print(f"  - {f}")

    print(f"\nSkipped (test already exists): {len(skipped)}")
    for f in skipped:
        print(f"  - {f}")

    print("\nNext: run vendor/bin/phpunit (tests should show as incomplete).")
    return 0


if __name__ == "__main__":
    sys.exit(main())
