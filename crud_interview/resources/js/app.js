import './bootstrap';
import { createApp } from 'vue';

// componentes
import MainApp from './components/MainApp.vue';
import UserList from './components/UserList.vue';
import UserFormModal from './components/UserFormModal.vue';
import UserDetailsModal from './components/UserDetailsModal.vue';
import ProfileList from './components/ProfileList.vue';
import AddressList from './components/AddressList.vue';

const app = createApp({});

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
      let value = e.target.value.replace(/\D/g, '');
      let maskedValue = '';
      let valueIndex = 0;
      
      for (let i = 0; i < mask.length && valueIndex < value.length; i++) {
        if (mask[i] === '#') {
          maskedValue += value[valueIndex];
          valueIndex++;
        } else {
          maskedValue += mask[i];
        }
      }
      e.target.value = maskedValue;
      // Disparar evento input para o v-model
      e.target.dispatchEvent(new Event('input', { bubbles: true }));
    });
  }
});

/**
 * Mount the application
 */
app.mount('#app');

export default app;