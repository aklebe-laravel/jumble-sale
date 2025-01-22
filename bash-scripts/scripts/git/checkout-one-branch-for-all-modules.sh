#!/bin/bash
# =============================================
# Checking location is the script folder
# =============================================
BASEDIR=$(readlink -f "$0")
BASEDIR=$(dirname "$BASEDIR")
if [[ $BASEDIR != *"git" ]]; then
  echo "Wrong script directory!"
  echo $BASEDIR
  exit
fi

# Argument validation check
if [ "$#" -ne 1 ]; then
    echo "Usage: $0 <branchToCheckout>"
    exit 1
fi

# =============================================
# Preparing ...
# =============================================
PROJECTDIR="$BASEDIR/../.."
branchToCheckout="$1"

# =============================================
# Read default values ...
# =============================================
. "$PROJECTDIR/env/system.default.sh"

# =============================================
# Read config ...
# =============================================
. "$PROJECTDIR/env/system.sh" || exit

# =============================================
# include functions we need ...
# =============================================
. "$PROJECTDIR/functions/output.sh"
. "$PROJECTDIR/functions/confirmation.sh"

# =============================================
# calculate variables depends on config
# =============================================
fullModulePath="$destination_mercy_root_path/$mercyModuleDirectory"
fullThemePath="$destination_mercy_root_path/$mercyThemeDirectory"

# =============================================
# Confirmation
# =============================================
f_confirmation_mercy_root_settings "Checkout branch '$branchToCheckout' for all GIT repositories\nin '$fullModulePath' and '$fullThemePath'?"
[ ! $? -eq 0 ] && { exit 1; } # return not 0 = error and exit

# =============================================
#
# =============================================
function gitStuff() {

      # check its a git repo
      if [ -f "$1/.git/config" ]; then

        cd "$1" || exit
        echo "Try branch '$2' in '$1' ..."

        if output=$(git status --porcelain) && [ -z "$output" ]; then
          echo "OK!"
        else
          echo "Skipped! No clean git repository."
        fi

        git fetch origin "$2"
        git checkout "$2"
        echo "" # new line

      else

        echo "No git found in '$1'."

      fi

}

# =============================================
# App itself
# =============================================
if [ -d "$destination_mercy_root_path" ]; then
  gitStuff "$destination_mercy_root_path" "$branchToCheckout"
fi

# =============================================
# Modules
# =============================================
for d in $fullModulePath/*; do
  if [ -d "$d" ]; then
    gitStuff "$d" "$branchToCheckout"
  fi
done

# =============================================
# Themes
# =============================================
for d in $fullThemePath/*; do
  if [ -d "$d" ]; then
    gitStuff "$d" "$branchToCheckout"
  fi
done

cd "$BASEDIR" || exit


