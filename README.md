# BarberBot - Trabalho de Docker

Projeto acadêmico da disciplina de **Computação em Nuvem**. A aplicação escolhida foi o **BarberBot**, sistema web em PHP para agendamento e gestão de serviços, containerizado com Docker e Docker Compose.

## Arquitetura

O ambiente possui dois serviços principais:

- `web`: BarberBot em PHP 8.2 + Apache, construído pelo `app/Dockerfile`.
- `db`: MySQL 8, utilizado pela aplicação por meio do hostname Docker `db`.

Os dados do MySQL ficam no volume nomeado `mysql_data`, portanto permanecem após `docker compose down`. Eles só são removidos com `docker compose down -v`.

## Pré-requisitos

- Git
- Docker Desktop (ou Docker Engine com Docker Compose)

## Como executar após clonar

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

- Aplicação: `http://localhost:8080`
- Painel profissional: `http://localhost:8080/sistema/`

As credenciais de demonstração estão no `.env` copiado a partir do `.env.example` e são fictícias.

## Verificar os serviços

```bash
docker compose ps
```

Os serviços `web` e `db` devem aparecer em execução. O banco possui `healthcheck`, e o serviço web só inicia depois que o MySQL estiver saudável.

## Logs

```bash
docker compose logs -f web
docker compose logs -f db
```

## Parar sem apagar os dados

```bash
docker compose down
```

O volume `mysql_data` é preservado.

## Parar e apagar os dados

```bash
docker compose down -v
```

Esse comando remove também o volume nomeado. Na próxima subida, o banco será inicializado novamente a partir de `database/barber.sql`.

## Construir somente a imagem da aplicação

```bash
docker build -t barberbot-web ./app
```

O `Dockerfile` usa a imagem oficial `php:8.2-apache`, habilita as extensões necessárias ao BarberBot, declara `EXPOSE 80` e executa o Apache com `CMD ["apache2-foreground"]`.

## Segurança do repositório

- `.env` está no `.gitignore` e não deve ser enviado ao GitHub.
- `.env.example` contém apenas valores fictícios.
- Chaves de OpenAI, reCAPTCHA e Google Maps são lidas de variáveis de ambiente e ficam vazias no ambiente acadêmico.
- O SQL público contém a estrutura do banco, mas os registros reais do dump original foram removidos.
- Logs e diretórios de uploads de usuários não entram no repositório acadêmico.

## Teste antes da entrega

Em uma pasta vazia, repita o mesmo processo que será usado na correção:

```bash
git clone https://github.com/jcordeiro1/Docker.git teste
cd teste
cp .env.example .env
docker compose up --build -d
docker compose ps
```

Depois abra `http://localhost:8080` e confirme que o BarberBot responde.

