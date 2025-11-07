<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Sistema CRUD - Gerenciamento de Usuários e Perfis</title>
    
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
                    <h1>🔷 Sistema de Gerenciamento de Usuários</h1>
                    <nav class="header-nav">
                        <a href="/" class="nav-link active">Usuários</a>
                        <a href="/perfis" class="nav-link">Perfis</a>
                        <a href="/enderecos" class="nav-link">Endereços</a>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="app-main">
            <div class="container">
                <!-- Componente Vue Principal -->
                <user-list></user-list>
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
/* Estilos adicionais para o header */
.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-nav {
    display: flex;
    gap: 20px;
}

.nav-link {
    color: white;
    text-decoration: none;
    padding: 8px 16px;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.nav-link:hover,
.nav-link.active {
    background-color: rgba(255, 255, 255, 0.2);
}

.app-footer {
    background-color: #333;
    color: white;
    padding: 20px 0;
    text-align: center;
    margin-top: 50px;
}

.app-footer p {
    margin: 0;
    font-size: 14px;
}

#global-loading {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loading-spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #007bff;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        gap: 15px;
    }

    .header-nav {
        width: 100%;
        justify-content: center;
    }
}
</style>