// Para limpar o cache em dispositivos, sempre aumente o número APP_VER após fazer alterações.
// O aplicativo fornecerá conteúdo novo imediatamente ou após 2-3 atualizações (abrir/fechar)
var APP_NAME = 'Geastão Sistemas';
var APP_VER = '1.5';
var CACHE_NAME = APP_NAME + '-' + APP_VER;

// Arquivos necessários para fazer este aplicativo funcionar offline.
// Adicione todos os arquivos que você deseja visualizar offline abaixo.
// Deixe REQUIRED_FILES = [] para desabilitar offline.
var REQUIRED_FILES = [
	// HTML Files
  'index.php',
	// Styles
	'/painel/styles/style.css',
	'/painel/styles/bootstrap.css',
	// Scripts
	'/painel/scripts/custom.js',
	'painel/scripts/bootstrap.min.js',
	// Images
	'/painel/images/empty.png',
];

// Diagnóstico do Service Worker. Defina true para obter logs do console.
var APP_DIAG = false;

//Função Service Worker abaixo.
self.addEventListener('install', function(event) {
	event.waitUntil(
		caches.open(CACHE_NAME)
		.then(function(cache) {
			//Adding files to cache
			return cache.addAll(REQUIRED_FILES);
		}).catch(function(error) {
			//Erro de saída se os locais dos arquivos estiverem incorretos
			if(APP_DIAG){console.log('Service Worker Cache: Error Check REQUIRED_FILES array in _service-worker.js - files are missing or path to files is incorrectly written -  ' + error);}
		})
		.then(function() {
			//Install SW if everything is ok
			return self.skipWaiting();
		})
		.then(function(){
			if(APP_DIAG){console.log('Service Worker: Cache is OK');}
		})
	);
	if(APP_DIAG){console.log('Service Worker: Installed');}
});

self.addEventListener('fetch', function(event) {
	event.respondWith(
		//Fetch Data from cache if offline
		caches.match(event.request)
			.then(function(response) {
				if (response) {return response;}
				return fetch(event.request);
			}
		)
	);
	if(APP_DIAG){console.log('Service Worker: Fetching '+APP_NAME+'-'+APP_VER+' files from Cache');}
});

self.addEventListener('activate', function(event) {
	event.waitUntil(self.clients.claim());
	event.waitUntil(
		//Check cache number, clear all assets and re-add if cache number changed
		caches.keys().then(cacheNames => {
			return Promise.all(
				cacheNames
					.filter(cacheName => (cacheName.startsWith(APP_NAME + "-")))
					.filter(cacheName => (cacheName !== CACHE_NAME))
					.map(cacheName => caches.delete(cacheName))
			);
		})
	);
	if(APP_DIAG){console.log('Service Worker: Activated')}
});
