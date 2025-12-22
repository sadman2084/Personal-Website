#!/bin/bash
echo "== Auto push started =="

git status
git add .
git commit -m "auto update"
git push origin

echo "== Auto push finished =="

