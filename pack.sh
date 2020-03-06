#!/bin/bash
pushd ..

FOLDER="twitch-streams"

shopt -s globstar
FILES=(
    "$FOLDER/build/**/*"
    # "$FOLDER/build/*"
    "$FOLDER/css/**/*"
    # "$FOLDER/css/*"
    "$FOLDER/views/**/*"
    # "$FOLDER/views/*"
    "$FOLDER/*.php"
)

# echo $FILES

# ls $FILES
# ls -l ${FILES[@]}

zip twitch-streams.zip ${FILES[@]}
popd
mv ../twitch-streams.zip .