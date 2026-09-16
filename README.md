# BarberBot - Trabalho de Docker

Projeto acadÃªmico da disciplina de **ComputaÃ§Ã£o em Nuvem**. A aplicaÃ§Ã£o escolhida foi o **BarberBot**, sistema web em PHP para agendamento e gestÃ£o de serviÃ§os, containerizado com Docker e Docker Compose.

## Arquitetura

O ambiente possui dois serviÃ§os principais:

- `web`: BarberBot em PHP 8.2 + Apache, construÃ­do pelo `app/Dockerfile`.
- `db`: MySQL 8, utilizado pela aplicaÃ§Ã£o por meio do hostname Docker `db`.

Os dados do MySQL ficam no volume nomeado `mysql_data`, portanto permanecem apÃ³s `docker compose down`. Eles sÃ³ sÃ£o removidos com `docker compose down -v`.

## PrÃ©-requisitos

- Git
- Docker Desktop (ou Docker Engine com Docker Compose)

## Como executar apÃ³s clonar

### Linux/macOS/Git Bash

```bash
git clone https://github.com/jcordeiro1/Docker.git barberbot-docker
cd barberbot-docker
cp .env.example .env
docker compose up --build -d
```

### Windows PowerShell

```powershell
git clone https://github.com/jcordeiro1/Docker.git barberbot-docker
cd barberbot-docker
Copy-Item .env.example .env
docker compose up --build -d
```

Quando o comando terminar, acesse:

- AplicaÃ§Ã£o: `http://localhost:8080`
- Painel profissional: `http://localhost:8080/sistema/`

As credenciais de demonstraÃ§Ã£o estÃ£o no `.env` copiado a partir do `.env.example` e sÃ£o fictÃ­cias.

## Verificar os serviÃ§os

```bash
docker compose ps
```

Os serviÃ§os `web` e `db` devem aparecer em execuÃ§Ã£o. O banco possui `healthcheck`, e o serviÃ§o web sÃ³ inicia depois que o MySQL estiver saudÃ¡vel.

## Logs

```bash
docker compose logs -f web
docker compose logs -f db
```

## Parar sem apagar os dados

```bash
docker compose down
```

O volume `mysql_data` Ã© preservado.

## Parar e apagar os dados

```bash
docker compose down -v
```

Esse comando remove tambÃ©m o volume nomeado. Na prÃ³xima subida, o banco serÃ¡ inicializado novamente a partir de `database/barber.sql`.

## Construir somente a imagem da aplicaÃ§Ã£o

```bash
docker build -t barberbot-web ./app
```

O `Dockerfile` usa a imagem oficial `php:8.2-apache`, habilita as extensÃµes necessÃ¡rias ao BarberBot, declara `EXPOSE 80` e executa o Apache com `CMD ["apache2-foreground"]`.

## SeguranÃ§a do repositÃ³rio

- `.env` estÃ¡ no `.gitignore` e nÃ£o deve ser enviado ao GitHub.
- `.env.example` contÃ©m apenas valores fictÃ­cios.
- Chaves de OpenAI, reCAPTCHA e Google Maps sÃ£o lidas de variÃ¡veis de ambiente e ficam vazias no ambiente acadÃªmico.
- O SQL pÃºblico contÃ©m a estrutura do banco, mas os registros reais do dump original foram removidos.
- Logs e diretÃ³rios de uploads de usuÃ¡rios nÃ£o entram no repositÃ³rio acadÃªmico.

## Teste antes da entrega

Em uma pasta vazia, repita o mesmo processo que serÃ¡ usado na correÃ§Ã£o:

```bash
git clone https://github.com/jcordeiro1/Docker.git teste
cd teste
cp .env.example .env
docker compose up --build -d
docker compose ps
```

Depois abra `http://localhost:8080` e confirme que o BarberBot responde.

