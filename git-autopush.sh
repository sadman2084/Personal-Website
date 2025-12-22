#!/bin/bash

# ===== CONFIG (CHANGE ONLY THESE) =====
GIT_NAME="sadman2084"
GIT_EMAIL="irams4633@gmail.com"
GITHUB_REPO_URL="https://github.com/sadman2084/Personal-Website.git"
BRANCH="main"
# =====================================

echo "== Git Auto Setup Started =="

# init git if not exists
if [ ! -d ".git" ]; then
  git init
fi

# set branch
git branch -M $BRANCH

# set repo-level identity (MOST IMPORTANT)
git config user.name "$GIT_NAME"
git config user.email "$GIT_EMAIL"

# add remote if not exists
if ! git remote | grep -q origin; then
  git remote add origin $GITHUB_REPO_URL
fi

# add, commit, push
git add .
git commit -m "auto commit" || echo "Nothing to commit"
git push -u origin $BRANCH

echo "== Git Auto Setup Finished =="
