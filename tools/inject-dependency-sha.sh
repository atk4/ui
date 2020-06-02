#!/bin/bash

cat composer.json | \
  jq '.require["atk4/core"]=.require["atk4/core"]+"#'`git ls-remote https://github.com/atk4/core/ develop | cut -f1`'"' |\
  jq '.require["atk4/data"]=.require["atk4/data"]+"#'`git ls-remote https://github.com/atk4/data/ develop | cut -f1`'"' \
  > composer.tmp.json && mv composer.tmp.json composer.json


