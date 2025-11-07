<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Sistema CRUD - Gerenciamento</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <!-- Header -->
        <header class="app-header">
            <div class="container">
                <div class="header-content">
                    <h1>🔷 Sistema de Gerenciamento</h1>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="app-main">
            <div class="container">
                <!-- Componente Principal com Navegação -->
                <main-app></main-app>
            </div>
        </main>

        <!-- Footer -->
        <footer class="app-footer">
            <div class="container">
                <p>&copy; {{ date('Y') }} Sistema CRUD - Todos os direitos reservados</p>
            </div>
        </footer>
    </div>

    <!-- Loading Global (opcional) -->
    <div id="global-loading" style="display: none;">
        <div class="loading-spinner"></div>
    </div>
</body>
</html>

<style>
/* Reset básico */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    background-color: #f5f5f5;
    color: #333;
    line-height: 1.6;
}

.container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Header */
.app-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px 0;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.app-header h1 {
    font-size: 24px;
    font-weight: 600;
    margin: 0;
}

/* Main */
.app-main {
    min-height: calc(100vh - 200px);
}

/* Footer */
.app-footer {
    background-color: #2c3e50;
    color: white;
    padding: 20px 0;
    text-align: center;
    margin-top: 50px;
}

.app-footer p {
    margin: 0;
    font-size: 14px;
}

/* Loading Global */
#global-loading {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loading-spinner {
    border: 6px solid #f3f3f3;
    border-top: 6px solid #667eea;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsividade */
@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }

    .app-header h1 {
        font-size: 20px;
    }

    .container {
        padding: 0 10px;
    }
}
</style>