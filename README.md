<p align="center">
<img src="https://github.com/robier/symfony-dokman-demo/workflows/tests/badge.svg" alt="Build Status">
<a href="https://codecov.io/gh/robier/symfony-dokman-demo">
  <img src="https://codecov.io/gh/robier/symfony-dokman-demo/branch/master/graph/badge.svg" />
</a>
<a href="https://github.com/robier/symfony-dokman-demo">
<img src="https://img.shields.io/badge/License-MIT-green.svg" alt="MIT">
</a>
</p>

## About Symfony dokman demo

Demo application that shows true power of [dokman](https://github.com/robier/dokman) on Symfony project!

## Project requirements

PHP: `8.0+`
MariaDB: `10.6.2+`

## Docker setup for local development

- Docker: 20.10.6+
- Docker-Compose: 1.29.2+

0. Clone the project (note the `--recursive` flag)
    ```bash
    git clone --recursive git@github.com:robier/symfony-dokman-demo.git
    ```
   (if you did not run clone with `--resursive` then run `git submodule update --init docker/.dokman`)

0. Add `symfony-dokman-demo.loc` pointing to `127.0.0.1` to your `/etc/hosts` file.
   
0. Install the project (after installation, browser will open project automagically)
    ```bash
    docker/install dev -v
    ```
0. Generate your own GitHub development token, so you can use the application without any limits.
   Fallow this [tutorial](https://docs.github.com/en/github/authenticating-to-github/keeping-your-account-and-data-secure/creating-a-personal-access-token) 
   and place your token in file `.env.local` under ENV variable `GITHUB_API_TOKEN`. 

0. You can now access the project on `symfony-dokman-demo.loc:8889`. If you wish to change the default port create
   a new `.env` file in the docker folder and change the `NGINX_HTTP_PORT` to whatever port you want.

**Note:** All default values can be seen in the same docker folder in the **.env.dist** file.
After changing any of these settings you'll need to rebuild your Docker images with `docker/env dev build`.

The `docker/install dev` command will automatically up your Docker containers, but you can up and down them later using these commands:

```bash
docker/env dev on
```

and

```bash
docker/env dev off
```

You can restart all containers in the dev environment with this command:

```bash
docker/env dev restart
```

You can enter a specific container using (example for the PHP container):

```bash
docker/enter dev:php
```

If you need to inspect the logs you can use the following commands:

```bash
docker/env dev logs php
docker/env dev logs nginx
```

If you want to for example run `composer install` in the `dev` environment on a fresh container you can use this command:

```bash
docker/run dev:php composer install
```

After that the freshly created container will be removed.

Removing all containers and volumes (basically uninstall, after doing this, you need to install project via `docker/install` command):

```bash
docker/env dev down -v -t0 --remove-orphans
```

These are just the basics of what Dokman can do, for other features please check Dokman's documentation
[here](https://github.com/robier/dokman).

### Docker environments

Docker setup supports 2 different environments. We have `dev` and `ci` environment: 

- `ci` - have only PHP container and project is not accessible via web browser as we only run tests in `ci` environment
- `dev` - have all container available for easier development and project is accessible via web browser

## Xdebug

By default you can use Xdebug on the project. All you have to do is configure it in PHPStorm.
Put a breakpoint in some file where you want to debug and the first time you do this PHPStorm
will tell you that the remote file path is not mapped to any file path in the project. You should map
your local project folder to `/app` (in the `Absolute path on the server` column).
This must be mapped because the paths in the Docker container and the paths on the local machine do not match.

You can also debug CLI scripts, for example:

```bash
docker/enter dev:php xdebug bin/console 
```

If you want to run CLI scripts without debugging just ommit `xdebug` from the command.

At the moment there is only the `dev` environment in the Docker setup (as it can be seen in the `docker/environments` folder), but new environments can
be easily added in the future.

## Xprofile

The same way as you can use xdebug, you can alo use xprofile by running:

```bash
docker/enter dev:php xprofile bin/console
```

Profile files will be witten in `var/logs/cachegrind.out.*` files inside project.

## Project usage

This is a simple project that gets and aggregates some statistics from GitHub. Currently, it has one endpoint `/word-popularity/{word}`
that will ping GitHub and calculate popularity of a given `{word}`. It takes number all negative and all positive mentions inside issues and
pull requests. It will sum those two numbers and with special algorithm (($best + $worst) / $best * 10) we will get a float in range 0-10.

Endpoint uses MariaDB to cache results. If you want to reduce/increase cache TTL (time to live), you can do that by changing `app.cache.ttl` value
to any other value (keep in mind, value is in seconds) in file `config/service.yaml`. When TTL elapses, application will ping API of provider and update the value in database.

When querying the endpoint, you will get 3 values:
- `term` - {word} that we searched for
- `score` - float number between 0 and 10 (2 decimals)
- `age` - number in seconds that show how old is the score for given value

Currently, application only supports GitHub as provider to fetch the data. There is also the `Twitter` provider (it's a dummy provider, that
every time generates random score for given word). To change current using provider you need to change line from

```yaml
App\Service\WordPopularityService\Provider\ProviderInterface: '@App\Service\WordPopularityService\Provider\GitHub'
```

to

```yaml
App\Service\WordPopularityService\Provider\ProviderInterface: '@App\Service\WordPopularityService\Provider\Twitter'
```

in file `config/service.yaml`. Doctrine cache mechanism will also be applied, so score would only change when cache becomes stale.

Endpoint examples:

```bash
curl http://symfony-dokman-demo.loc:8889/word-popularity/php
{"term":"php","score":3.36,"age":0}
```

```bash
curl http://symfony-dokman-demo.loc:8889/word-popularity/javascript
{"term":"javascript","score":3.2,"age":7}
```

## Tests

Run tests with command:

```bash
docker/run dev:php vendor/bin/phpunit
```

## License

Symfony dokman demo is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
