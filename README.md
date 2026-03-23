# WordPress Docker Project

This project runs WordPress in Docker with a custom theme mounted from the local workspace.

## Start

```bash
docker compose up -d
```

Then open `http://localhost:8081` and complete the normal WordPress installer.

After install, the `movies-minimal` theme will be activated automatically by a small must-use plugin.

## Required plugins

To run this theme properly, install:

- `Advanced Custom Fields (ACF)`: required for the `Intro` and `Movie Rating` fields used by the theme

Optional:

- `Classic Editor`: only if you want the old WordPress editor experience

## Frontend workflow

The theme uses Vite and Tailwind CSS v4.

Install dependencies:

```bash
cd wp-content/themes/movies-minimal
npm install
```

Run watch mode so assets rebuild on every save:

```bash
npm run dev
```

This project does not use Vite HMR in the browser. Instead, Vite rebuilds `dist` whenever you save, and WordPress serves the updated compiled files on refresh.

Build once manually:

```bash
npm run build
```

## Project structure

- `docker-compose.yml`: WordPress + MariaDB stack
- `wp-content/themes/movies-minimal`: custom theme
- `wp-content/mu-plugins`: bootstrap plugin for automatic theme activation

## Current homepage

The homepage currently renders:

- site title
- a plain list of recent posts
- black, white, and gray only

## Stop

```bash
docker compose down
```
