#!/bin/bash
# ======================================================================================================
# Operation system specific functions
# ======================================================================================================
var_os_last_uuid=""

# ==========================================================================
# Parameters:
# Return:
#   ...
# ==========================================================================
function f_os_uuid() {

  uuid=$(uuidgen)
  uuid=${uuid^^}
  var_os_last_uuid="$uuid"
  return 0

}

# =============================================
# Parameters:
# 1) directory
# 2) branch
# Return:
#   ...
# =============================================
function gitFetchAndCheckout() {

  # check its a git repo
  if [ -f "$1/.git/config" ]; then

    cd "$1" || exit
    echo "Try checkout branch '$2' in '$1' ..."

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
    return 1

  fi

  return 0
}


