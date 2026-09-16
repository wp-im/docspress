#!/usr/bin/env bash
set -euo pipefail

root="$(CDPATH='' cd -- "$(dirname -- "$0")/.." && pwd)"
content="$root/site-content"
manifest="$content/manifest.tsv"

test -f "$manifest"
node --check "$root/scripts/build-wxr.js"
rows=0

while IFS='|' read -r type title slug parent menu_order template excerpt file; do
	[ -n "$type" ] || continue
	case "$type" in
		page|post) ;;
		*) printf 'Unknown content type: %s\n' "$type" >&2; exit 1 ;;
	esac
	[ -n "$title" ] || { printf 'Missing title for %s\n' "$slug" >&2; exit 1; }
	[ -n "$slug" ] || { printf 'Missing slug for %s\n' "$title" >&2; exit 1; }
	[ -n "$file" ] || { printf 'Missing source file for %s\n' "$slug" >&2; exit 1; }
	source="$content/$file"
	test -f "$source" || { printf 'Missing content source: %s\n' "$source" >&2; exit 1; }
	separator_count="$(rg -c '^---$' "$source" || true)"
	[ "$separator_count" -ge 2 ] || { printf 'Missing front matter separators: %s\n' "$source" >&2; exit 1; }
	rows=$(( rows + 1 ))
done < <(tail -n +2 "$manifest" | tr '\t' '|')

expected="$(find "$content/pages" "$content/posts" -type f -name '*.md' | wc -l | tr -d ' ')"
[ "$rows" -eq "$expected" ] || {
	printf 'Manifest/source count mismatch: %s rows, %s files\n' "$rows" "$expected" >&2
	exit 1
}

printf 'Docspress content checks: PASS (%s sources)\n' "$rows"
