Document file server
==================

API endpoints
- POST - upload new file
- PUT - update file content
- DELETE - delete existing file

Features:
- with HTTP User-Agent verification
- custom FILE storage based on ENV setup

TODOs:
- add image resize filters

```bash
docker compose up -d
```

#### Generate openapi.json
```bash
docker compose exec document-php composer apidoc
```

#### PHP Code style fix
```bash
docker compose exec document-php composer phpcs
```

#### PHP Static analyzer
```bash
docker compose exec document-php composer phpstan
```

### PHP CS + PHPStan + Unit tests
```bash
docker compose exec document-php composer all
```
