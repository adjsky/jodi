set quiet := true

default:
    just --list --unsorted

bootstrap: env install
    just api jodi:setup

install: api-install client-install

env:
    if [ ! -f .env ]; then cp .env.example .env; fi

[parallel]
test: api-test client-test

client-install:
    npm install

client-test:
    npm test

[parallel]
client-dev: client-serve api-types

client-serve:
    npm run dev -w client -- --host=jodi.localhost

[working-directory: "api"]
api *args:
    php artisan {{args}}

api-install:
    composer --working-dir=api install

api-test:
    composer --working-dir=api test

[parallel]
api-dev: && api-logs api-serve api-worker
    just api telescope:prune

api-serve:
    just api serve --host=jodi.localhost

api-worker:
    just api queue:listen --tries=1

api-logs:
    just api pail --timeout=0

api-types:
    just api typescript:transform --watch

[env("CAPACITOR_APP_ID", "io.github.adjsky.jodi.dev")]
[env("CAPACITOR_APP_NAME", "Jodi Dev")]
[env("BUILD_NUMBER", "1")]
android:
    just cap run android --live-reload --host=jodi.localhost --port=5173 --forwardPorts=5173:5173

[working-directory: "client"]
cap *args:
    npx cap {{args}}
