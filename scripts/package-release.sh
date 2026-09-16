#!/usr/bin/env bash
set -euo pipefail

root="$(CDPATH='' cd -- "$(dirname -- "$0")/.." && pwd)"
version="${1:-0.2.3}"
build_dir="$root/build"
zip_path="$build_dir/docspress-$version.zip"
sha_path="$zip_path.sha256"

mkdir -p "$build_dir"
python3 - "$root/theme" "$zip_path" <<'PY'
import sys
import zipfile
from pathlib import Path

theme_root = Path(sys.argv[1]).resolve()
zip_path = Path(sys.argv[2]).resolve()

if zip_path.exists():
    zip_path.unlink()

files = sorted(path for path in theme_root.rglob("*") if path.is_file())
with zipfile.ZipFile(zip_path, "w", compression=zipfile.ZIP_DEFLATED, compresslevel=9) as archive:
    for path in files:
        relative = path.relative_to(theme_root).as_posix()
        archive.write(path, "docspress/" + relative)

print(zip_path)
PY

shasum -a 256 "$zip_path" > "$sha_path"
printf '%s\n' "$sha_path"
