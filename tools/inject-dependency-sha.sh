#!/bin/bash

cat composer.json | \
  jq '.require["atk4/core"]=.require["atk4/core"]+"#'`git ls-remote git@github.com:atk4/core.git develop | cut -f1`'"' |\
  jq '.require["atk4/data"]=.require["atk4/data"]+"#'`git ls-remote git@github.com:atk4/data.git develop | cut -f1`'"' \
  > composer.tmp.json && mv composer.tmp.json composer.json


