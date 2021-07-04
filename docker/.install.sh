#!/usr/bin/env bash

function getValueFromEnv
{
    local envPath="${1}"
    local variableName="${2}"

    echo $(awk -F'=' "/^${variableName}/ { print \$2}" ${envPath})
}

function getValueFromDokmanEnv
{
    local variableName="${1}"

    echo $(getValueFromEnv "${DOKMAN_PROJECT_ROOT}/docker/.env.dist" "${variableName}")
}

readonly hosts=(
    "$(getValueFromDokmanEnv "COMPOSE_PROJECT_NAME").loc"
)

function dokmanInstall
{
    local sitePort=$(getValueFromDokmanEnv "NGINX_HTTP_PORT")

    validateHostEntries "${hosts[@]}"

    # setup configuration files

    if [[ "${DOKMAN_ENV}" = "dev" ]]; then
        title 'Env...'
        if [[ ! -e '.env.local' ]]; then
            runCommand "cp \".env\" '.env.local'" "Copying .env to .env.local file..."
        else
            info 'Using existing .env.local application file.'
        fi
    fi

    title 'Docker...'
    runCommand "docker/env ${DOKMAN_ENV} on" "Building and upping docker containers..."

    title 'Dependencies...'
    runCommand "docker/enter ${DOKMAN_ENV}:php composer install" "Installing PHP dependencies..."

    if [[ "${DOKMAN_ENV}" = "dev" ]]; then
        # run only in dev mode!
        runCommand "docker/enter ${DOKMAN_ENV}:php bin/console doctrine:migrations:migrate --no-interaction" "Running database migrations..."

        title 'Database...'
        runCommand "docker/enter ${DOKMAN_ENV}:php bin/console doctrine:migrations:migrate --no-interaction" "Running database migrations..."

        title 'Finishing...'
        runCommand "bash -c \"xdg-open http://${hosts[0]}:${sitePort}\" 2> /dev/null" "Opening web browser"
    fi
}
