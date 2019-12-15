#!/bin/bash

# This file will watch for any changes on less, pug, jade files and compile them
# right away

# Check for dependencies
which fswatch >/dev/null || brew install fswatch
which terminal-notifier >/dev/null || brew install terminal-notifier
which npm >/dev/null || brew install npm
which pug >/dev/null || npm install pug-cli -g

echo "Watching for .less, .pug and .jade.."

fswatch -e '.*' -i '.jade$' -i '.pug$' -i '.less$' . | while read x
do
    if echo $x | grep 'less$'; then
        lessc $x > `echo $x | sed 's/less$/css/'` \
            && terminal-notifier -group pug -title 'LESS Compiler' -subtitle 'Success' -message "$x" \
            || terminal-notifier -group pug -title 'LESS Compiler' -subtitle 'Failed' -message "$x" -sound Funk -action com.apple.Terminal

        continue
    fi
    pug -P "$x" \
        && terminal-notifier -group pug -title 'PUG Compiler' -subtitle 'Success' -message "$x" \
        || terminal-notifier -group pug -title 'PUG Compiler' -subtitle 'Failed' -message "$x" -sound Funk -action com.apple.Terminal
done

#Clicking on notification won't open terminal due to bug:
# https://github.com/julienXX/terminal-notifier/issues/180

