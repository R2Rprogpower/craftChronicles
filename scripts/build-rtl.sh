#!/bin/sh

set -eu

generated=0

build_rtl() {
    source_file="$1"
    output_file="$2"

    if [ ! -f "$source_file" ]; then
        echo "RTL source not found; skipping: $source_file"
        return
    fi

    rtlcss "$source_file" "$output_file"
    echo "Generated RTL stylesheet: $output_file"
    generated=$((generated + 1))
}

build_rtl public/build/css/app.min.css public/build/css/app-rtl.min.css
build_rtl public/build/css/bootstrap.min.css public/build/css/bootstrap-rtl.min.css

if [ "$generated" -eq 0 ]; then
    echo "No legacy RTL source files are present; RTL build is not required."
fi
