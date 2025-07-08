#!/bin/bash
# params keys are taken from the following link
# https://github.com/WP-API/docs/blob/a1079e3713c0a3426e7d2d6598a16494dfb1e1d6/reference/posts.md

ffuf -c -o results.json \
    -u "http://localhost:8081/wp-json/wp/v2/posts/1?KEY=VALUE&KEY2=VALUE2" \
    -X POST \
    -w params_keys.txt:KEY \
    -w params_values.txt:VALUE \
    -w params_keys.txt:KEY2 \
    -w params_values.txt:VALUE2 \
    -mode clusterbomb \
    -t 100 \
    -fc 401,403,404