#!/bin/bash
GH_REPO="@github.com/laf/docs.git"
FULL_REPO="https://${GH_TOKEN}$GH_REPO"

pip install --user mkdocs
pip install --user pymdown-extensions

mkdir -p doc/out

cd doc/out

git init
git remote add origin $FULL_REPO
git fetch
git config user.name "docs-build"
git config user.email "travis@librenms.org"
git checkout gh-pages

copy ../mkdocs.yml .

mkdocs build --clean

touch .
git add -A .
git commit -m "GH-Pages update by travis after $TRAVIS_COMMIT"
git push -q origin gh-pages
