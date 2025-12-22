#!/bin/bash
echo "== Auto push started =="

git status
git add .
git commit -m "auto update"
git push origin main

echo "== Auto push finished =="

