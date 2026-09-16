<?php
// Configurações do Marketplace (MASTER) e conexão dos TENANTS.
// Este arquivo fica protegido por /config/.htaccess (Require all denied)
return [
  'master' => [
    'host' => 'localhost',
    'db'   => 'barberbot_market',
    'user' => 'SEU_USER_MASTER',
    'pass' => 'SUA_SENHA_MASTER',
  ],

  // Credenciais para conectar em TODOS os DBs das barbearias (tenants)
  // Dica: crie um usuário MySQL com permissão nos bancos tenants.
  'tenant' => [
    'host'       => 'localhost',
    'default_db' => 'SEU_DB_PADRAO',   // usado quando NÃO existe tenant (site institucional)
    'user'       => 'SEU_USER_TENANTS',
    'pass'       => 'SUA_SENHA_TENANTS',
    'modo_teste' => 'Não',
  ],

  // Subdomínios que NÃO são tenants
  'ignore_subdomains' => ['www', 'app', 'api', 'sistema', 'painel'],
];
