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