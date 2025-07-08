#!/bin/bash
# endpoints and relative methods are taken from the following link
# https://github.com/WP-API/docs/tree/a1079e3713c0a3426e7d2d6598a16494dfb1e1d6/reference
# it represents the official documentation of the wordpress rest api in 2017
# the query params are the common ones in the specified endpoints
#
# the specific object (e.g. posts/1, media/4) are taken from the default
# wordpress installation, so they need to be modified if they change

ffuf -c -o results.json \
    -u "http://localhost:8081/wp-json/wp/v2/ENDPOINT?KEY=VALUE" \
    -X METHOD \
    -w endpoints.txt:ENDPOINT \
    -w methods.txt:METHOD \
    -w params_keys.txt:KEY \
    -w params_values.txt:VALUE \
    -mode clusterbomb \
    -t 100 \
    -fc 401,403,404