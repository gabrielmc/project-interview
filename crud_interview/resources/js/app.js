/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp({
  components: {
    UserList
  }
});

//import ExampleComponent from './components/ExampleComponent.vue'; // [Disclaimer] FILE REMOVIDO NA CONFIGURAÇÂO DO FRONT 
//app.component('example-component', ExampleComponent);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */

// Diretiva para máscaras de input
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

// Montar aplicação
app.mount('#app');

// Exportar para uso global
export default app;
