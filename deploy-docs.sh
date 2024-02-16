#!/usr/bin/env sh

# abort on errors
set -e

# build\
npm ci
npm run docs:build

# navigate into the build output directory
cd docs/.vuepress/dist

# if you are deploying to a custom domain
# echo 'www.example.com' > CNAME

git config --global user.name "GitHub Actions Bot"
git config --global user.email "<>"

git init
git add -A
git commit -m 'docs'

git push -f git@github.com:VitalyArt/hltv-demo-parser.git master:gh-pages

cd -
