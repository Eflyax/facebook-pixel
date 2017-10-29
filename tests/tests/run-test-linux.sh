#!/usr/bin/env bash
dir=$(cd `dirname $0` && pwd)
if [ $# -ge 1 ]; then
  runTests=$1
  shift
else
  runTests=$dir
fi
$dir/../vendor/bin/tester -p /usr/bin/php7.0 -c $dir/php-unix.ini $runTests $@