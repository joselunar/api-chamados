# API de Chamados

Help desk em **PHP 8.3**, **CodeIgniter 4** e **MySQL**. O usuário abre chamado com prioridade; atendente e admin acompanham status, comentam e o sistema guarda o histórico de cada mudança.

O foco é organização de backend: papéis, transições de status, filtros e paginação.

## Papéis

| Papel | Pode |
| --- | --- |
| `usuario` | Abrir chamado, ver e comentar só os próprios |
| `atendente` | Ver todos, filtrar, atribuir a si, mudar status/prioridade |
| `admin` | Tudo do atendente + atribuir qualquer um e fechar chamado não resolvido |

Quando o solicitante comenta um chamado `aguardando_usuario`, o status volta automaticamente para `em_andamento`.

## Status

`aberto` → `em_andamento` → `aguardando_usuario` / `resolvido` → `fechado`

Reabrir só a partir de `resolvido` ou `fechado` para `em_andamento`. Transição inválida responde `409`.

## API

| Método | Rota |
| --- | --- |
| POST | `/api/login` |
| POST | `/api/chamados` |
| GET | `/api/chamados?status=&priority=&q=&assignee_id=&per_page=` |
| GET | `/api/chamados/{id}` |
| PATCH | `/api/chamados/{id}` |
| POST | `/api/chamados/{id}/comentarios` |
| GET | `/api/chamados/{id}/historico` |

```bash
curl -X POST http://localhost:8083/api/login ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"usuario@chamados.local\",\"password\":\"Usuario@123\"}"

curl "http://localhost:8083/api/chamados?status=em_andamento&priority=alta" ^
  -H "Authorization: Bearer SEU_TOKEN"
```

## Stack

| Camada | Tecnologia |
| --- | --- |
| Backend | PHP 8.2+ + CodeIgniter 4 |
| Banco | MySQL 8 |
| API | REST + JSON |
| Testes | PHPUnit |
| Infra local | Docker Compose |

## Como rodar

MySQL na porta **3309**.

```bash
git clone https://github.com/joselunar/api-chamados.git
cd api-chamados
copy .env.example .env
docker compose up -d
composer install
php spark key:generate
php spark migrate
php spark db:seed DatabaseSeeder
php spark serve --host localhost --port 8083
```

Interface: [http://localhost:8083](http://localhost:8083)

| Perfil | E-mail | Senha |
| --- | --- | --- |
| Usuário | `usuario@chamados.local` | `Usuario@123` |
| Atendente | `atendente@chamados.local` | `Atendente@123` |
| Admin | `admin@chamados.local` | `Admin@123` |

## Testes

```bash
php vendor/bin/phpunit tests/unit/TicketRulesTest.php
```

## Licença

MIT. Veja [LICENSE](LICENSE).
