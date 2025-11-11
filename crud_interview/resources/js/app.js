import './bootstrap';
import { createApp } from 'vue';

// Importa o plugin de repositórios
import repositoryPlugin from './services/repositoryPlugin';

// Componentes globais
import MainApp from './components/MainApp.vue';
import UserList from './components/UserList.vue';
import UserFormModal from './components/UserFormModal.vue';
import UserDetailsModal from './components/UserDetailsModal.vue';
import ProfileList from './components/ProfileList.vue';
import AddressList from './components/AddressList.vue';

// Cria a instância da aplicação
const app = createApp({});

// Plugin de repositórios (injeção global)
app.use(repositoryPlugin);

// Registrar componentes globalmente
app.component('main-app', MainApp);
app.component('user-list', UserList);
app.component('user-form-modal', UserFormModal);
app.component('user-details-modal', UserDetailsModal);
app.component('profile-list', ProfileList);
app.component('address-list', AddressList);

/**
 * Diretiva para máscaras de input
 */
app.directive('mask', {
  mounted(el, binding) {
    const mask = binding.value;
    
    el.addEventListener('input', (e) => {
      const value = e.target.value.replace(/\D/g, '');
      const masked = applyMask(value, mask);

      // Só dispara o evento se o valor mudou
      if (masked !== e.target.value) {
        e.target.value = masked;
        e.target.dispatchEvent(new Event('input', { bubbles: true }));
      }
    });
  }
});

/** Monta a aplicação */
app.mount('#app');

export default app;
