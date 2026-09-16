#!/usr/bin/env bash
set -euo pipefail

root="$(CDPATH='' cd -- "$(dirname -- "$0")/.." && pwd)"
theme="$root/theme"

for required in style.css index.php comments.php screenshot.png readme.txt searchform.php; do
	if [ ! -f "$theme/$required" ]; then
		printf 'Missing required theme file: %s\n' "$required" >&2
		exit 1
	fi
done

if [ "$(file -b --mime-type "$theme/screenshot.png")" != 'image/png' ]; then
	printf '%s\n' 'Theme screenshot is not a PNG.' >&2
	exit 1
fi

if rg -n -i 'humanities|hdocs' "$theme"; then
	printf '%s\n' 'Reference identity found in deployable theme.' >&2
	exit 1
fi

if rg -n -i --glob '*.php' --glob '*.js' --glob '*.css' 'cdn|fonts\.google|wp_remote_|curl_init' "$theme"; then
	printf '%s\n' 'Remote runtime resource found in deployable theme.' >&2
	exit 1
fi

find "$theme" -type f -name '*.php' -print0 | xargs -0 -n1 php -l
find "$theme" -type f -name '*.js' -print0 | while IFS= read -r -d '' file; do
	node --check "$file"
done

php "$root/tests/test-docspress.php"
printf '%s\n' 'Docspress local checks: PASS'
