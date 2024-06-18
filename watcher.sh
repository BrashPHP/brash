#!/bin/bash

ulimit -n 100000

DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROCESS_NAME=""
STOP=false

# printf "$PROCESS_NAME\n"

# kill -f "$PROCESS_NAME"

function clean_up {
	# Perform program exit housekeeping
	STOP= true
	if kill -0 "$PROCESS_NAME" &>/dev/null; then
		echo "$PROCESS_NAME exists"
		kill -9 $PROCESS_NAME
	else
		echo "$PROCESS_NAME doesn't exist"
	fi
	sleep 0.5
	exit
}

trap clean_up SIGHUP SIGINT SIGTERM EXIT

php -f "$DIR/public/index.php" &

while true; do
	echo "in loop"
	PROCESS_NAME=$(ps aux | grep index.php | head -n1 | awk '{print $2}')

	inotifywait -e modify,create,delete -r $DIR/src &&
		echo "Killing ${PROCESS_NAME}" && kill "$PROCESS_NAME"

	while kill -0 "$PROCESS_NAME"; do
		sleep 0.5
		echo "Updated directory"

	done

	printf "\n---\n"
	
	if [ "$STOP" = false ]; then
		echo "restart"
		php -f "$DIR/public/index.php" &
		printf "\n\n\n"
	fi

done
