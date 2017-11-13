#!/bin/bash 

set -e

product='ui'


check=$(git symbolic-ref HEAD | cut -d / -f3)
if [ $check != "develop" ]; then
    echo "Must be on develop branch"
    exit -1
fi

# So that we can see un-committed stuff
git status

# Display list of recently released versions
git fetch --tags
git log --tags --simplify-by-decoration --pretty="format:%d - %cr" | head -n5

echo "Which version we are releasing: "
read version

function finish {
  git checkout develop
  git branch -D release/$version
  git checkout composer.json
}
trap finish EXIT

# Create temporary branch (local only)
git branch release/$version
git checkout release/$version

# Find out previous version
prev_version=$(git log --tags --simplify-by-decoration --pretty="format:%d" | grep -Eo '[0-9\.A-Z-]+' | head -1)

echo "Releasing $prev_version -> $version"

vimr CHANGELOG.md

# Compute diffs
git log --graph --pretty=format:'%Cred%h%Creset -%C(yellow)%d%Creset %s %Cgreen(%cr)%Creset' --abbrev-commit --date=relative $prev_version...

git log --pretty=full $prev_version... | grep '#[0-9]*' | sed 's/.*#\([0-9]*\).*/\1/' | sort | uniq | while read i; do
    echo "-[ $i ]-------------------------------------------------------------------------------"
    ghi --color show $i | head -50
done

open "https://github.com/atk4/$product/compare/$prev_version...develop"

# Update dependency versions
sed -i "" -e '/atk4.*dev-develop/d' composer.json
composer update
composer require atk4/core atk4/data

composer update
./vendor/phpunit/phpunit/phpunit  --no-coverage

sed -i "" "s|'https://cdn.rawgit.com/atk4/ui/.*|'https://cdn.rawgit.com/atk4/ui/$version/public',|" src/App.php
sed -i "" "s|public \$version.*|public \$version = '$version';|" src/App.php
git commit -m "Updated CDN and \$version in App.php to $version" src/App.php || echo "but its ok"


echo "Press enter to publish the release"
read junk

git commit -m "Added release notes for $version" CHANGELOG.md || echo "but its ok"
merge_tag=$(git rev-parse HEAD)

# use stable verisons
git commit -m "Set up stable dependencies for $version" composer.json

# Build jsLib and bundle
(cd js; npm run build)

# Build CSS
lessc public/agileui.less public/agileui.css  --clean-css="--s1 --advanced --compatibility=ie8" --source-map
uglifyjs --compress -- public/agileui.js > public/agileui.min.js

echo '!agileui.css' >> public/.gitignore
echo '!agileui.css.map' >> public/.gitignore
echo '!agileui.min.js' >> public/.gitignore
echo '!atk4JS.js' >> public/.gitignore
echo '!atk4JS.min.js' >> public/.gitignore
#sed  -i "" '/^lib/d' js/.gitignore
git add public
git commit -m "Build release $version" public

git tag $version
git push origin release/$version
git push --tags

git checkout develop
git merge $merge_tag --no-edit
git push

echo '=[ SUCCESS ]================================================'
echo "Released atk4/$product Version $version"
echo '============================================================'
echo

open https://github.com/atk4/$product/releases/tag/$version

# do we care about master branch? nah
