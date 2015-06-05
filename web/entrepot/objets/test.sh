#!/bin/bash
DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
FILES=$DIR/*
for f in $FILES
do
  filename=$(basename "$f")
  # take action on each file. $f store current file name
  echo $filename >> item.txt
done