from pathlib import Path
import re, sys

ROOT = Path(__file__).resolve().parents[1]
errors = []

def need(cond, msg):
    if not cond:
        errors.append(msg)

def text(rel):
    p = ROOT / rel
    need(p.exists(), f"arquivo ausente: {rel}")
    return p.read_text(encoding='utf-8', errors='ignore') if p.exists() else ''

# Required files
for rel in ['app/Dockerfile','app/.dockerignore','docker-compose.yml','.env.example','.gitignore','README.md','database/barber.sql']:
    need((ROOT/rel).exists(), f"arquivo ausente: {rel}")

dockerfile = text('app/Dockerfile')
need(re.search(r'^FROM\s+php:', dockerfile, re.M), 'Dockerfile deve usar imagem oficial php')
need(re.search(r'^EXPOSE\s+\d+', dockerfile, re.M), 'Dockerfile deve declarar EXPOSE')
need(re.search(r'^CMD\s+', dockerfile, re.M), 'Dockerfile deve declarar CMD')

di = text('app/.dockerignore')
entries = [ln.strip() for ln in di.splitlines() if ln.strip() and not ln.lstrip().startswith('#')]
need(len(entries) >= 3, '.dockerignore precisa de pelo menos 3 entradas')
need(any(e == '.git' or e.startswith('.git') for e in entries), '.dockerignore deve ignorar .git')
need(any(e == '.env' or e.startswith('.env') for e in entries), '.dockerignore deve ignorar .env')

compose = text('docker-compose.yml')
need(re.search(r'^\s*web:\s*$', compose, re.M), 'Compose deve conter serviço web')
need(re.search(r'^\s*db:\s*$', compose, re.M), 'Compose deve conter serviço db')
need('mysql:8' in compose, 'Compose deve usar MySQL 8')
need('/var/lib/mysql' in compose, 'Compose deve persistir /var/lib/mysql')
need('healthcheck:' in compose and 'mysqladmin ping' in compose, 'Banco deve possuir healthcheck com mysqladmin ping')
need('condition: service_healthy' in compose, 'web deve depender de db service_healthy')
need(re.search(r'DB_HOST:\s*db\b', compose), 'web deve acessar banco pelo nome do serviço db')
need(re.search(r'^volumes:\s*\n\s+mysql_data:', compose, re.M), 'Compose deve declarar volume nomeado mysql_data')

gitignore = text('.gitignore')
need(re.search(r'^\.env\s*$', gitignore, re.M), '.gitignore deve listar .env')

envex = text('.env.example')
for key in ['DB_HOST','DB_NAME','DB_USER','DB_PASSWORD','DB_ROOT_PASSWORD','APP_PORT']:
    need(re.search(rf'^{key}=', envex, re.M), f'.env.example sem {key}')

conn = text('app/sistema/conexao.php')
for key in ['DB_HOST','DB_NAME','DB_USER','DB_PASSWORD']:
    need(f"getenv('{key}')" in conn or f'getenv("{key}")' in conn, f'conexao.php deve ler {key} do ambiente')
need("$servidor = 'localhost'" not in conn, 'conexao.php não deve fixar localhost')

# Compatibilidade com MySQL 8 em modo estrito: o texto padrao completo precisa caber.
sql = text('database/barber.sql')
m = re.search(r'`texto_agendamento`\s+varchar\((\d+)\)', sql, re.I)
need(m is not None, 'schema deve declarar texto_agendamento como varchar')
if m:
    need(int(m.group(1)) >= len('Selecionar Prestador de Serviço'), 'texto_agendamento deve comportar o texto padrao completo no MySQL 8')
need("texto_agendamento = 'Selecionar Prestador de Serviço'" in conn, 'conexao.php deve manter o texto padrao completo')

if errors:
    print('FALHOU:')
    for e in errors:
        print(' -', e)
    sys.exit(1)
print('OK: requisitos estáticos principais atendidos')
