for f in *.php; do
  [ -e "$f" ] || continue
  mv -- "$f" "${f%.php}.php"
done

