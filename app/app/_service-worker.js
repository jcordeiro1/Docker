// Para limpar o cache em dispositivos, sempre aumente o número APP_VER após fazer alterações.
// O aplicativo fornecerá conteúdo novo imediatamente ou após 2-3 atualizações (abrir/fechar)
var APP_NAME = 'Sistema Cobranças';
var APP_VER = '1.5';
var CACHE_NAME = APP_NAME + '-' + APP_VER;

// Arquivos necessários para fazer este aplicativo funcionar offline.
// Adicione todos os arquivos que você deseja visualizar offline abaixo.
// Deixe REQUIRED_FILES = [] para desabilitar offline.
var REQUIRED_FILES = [
	// HTML Files
  'index.php',
	// Styles
	'painel/styles/style.css',
	'painel/styles/bootstrap.css',
	// Scripts
	'painel/scripts/custom.js',
	'painel/scripts/bootstrap.min.js',
	// Images
	'painel/images/empty.png',
];

// Instala o Service Worker e adiciona arquivos ao cache
self.addEventListener('install', (event) => {
  console.log('✅ Service Worker instalado');

  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('📦 Cacheando arquivos:', REQUIRED_FILES);
        return cache.addAll(REQUIRED_FILES);
      })
      .then(() => self.skipWaiting())
      .catch(err => console.error('❌ Erro ao adicionar ao cache:', err))
  );
});

// Ativação do SW
self.addEventListener('activate', (event) => {
  console.log('✅ Service Worker ativado');
  event.waitUntil(self.clients.claim());
});

// Intercepta as requisições
self.addEventListener('fetch', (event) => {
  console.log('🔄 Interceptando:', event.request.url);

  event.respondWith(
    caches.match(event.request)
      .then(response => response || fetch(event.request))
  );
});
