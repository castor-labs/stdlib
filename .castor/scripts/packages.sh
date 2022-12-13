#!/bin/bash

jq -c '.projects[]' projects.json | while read i; do
    echo $i
done
