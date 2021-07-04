#!/usr/bin/env bash

readonly hosts=(
    "${COMPOSE_PROJECT_NAME}.loc"
)

function dokmanInstall
{
    validateHostEntries "${hosts[@]}"

    # setup configuration files

    title 'Docker...'
    runCommand "docker/env ${DOKMAN_ENV} on" "Building and upping docker containers..."
}
