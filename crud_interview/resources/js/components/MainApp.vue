<template>
  <div class="main-app">
    <!-- Navegação -->
    <div class="nav-tabs">
      <button 
        @click="currentTab = 'users'" 
        :class="{ active: currentTab === 'users' }"
        class="nav-tab"
      >
        👥 Usuários
      </button>
      <button 
        @click="currentTab = 'profiles'" 
        :class="{ active: currentTab === 'profiles' }"
        class="nav-tab"
      >
        🎯 Perfis
      </button>
      <button 
        @click="currentTab = 'addresses'" 
        :class="{ active: currentTab === 'addresses' }"
        class="nav-tab"
      >
        📍 Endereços
      </button>
    </div>

    <!-- Conteúdo -->
    <div class="tab-content">
      <user-list v-if="currentTab === 'users'" />
      <profile-list v-if="currentTab === 'profiles'" />
      <address-list v-if="currentTab === 'addresses'" />
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import UserList from './UserList.vue';
import ProfileList from './ProfileList.vue';
import AddressList from './AddressList.vue';

export default {
  name: 'MainApp',
  components: {
    UserList,
    ProfileList,
    AddressList
  },
  setup() {
    const currentTab = ref('users');

    return {
      currentTab
    };
  }
};
</script>

<style scoped>
.main-app {
  width: 100%;
}

.nav-tabs {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
  border-bottom: 2px solid #e0e0e0;
  padding: 0 20px;
}

.nav-tab {
  padding: 12px 24px;
  background-color: transparent;
  border: none;
  border-bottom: 3px solid transparent;
  cursor: pointer;
  font-size: 16px;
  font-weight: 500;
  color: #666;
  transition: all 0.3s;
  position: relative;
  top: 2px;
}

.nav-tab:hover {
  color: #007bff;
}

.nav-tab.active {
  color: #007bff;
  border-bottom-color: #007bff;
}

.tab-content {
  animation: fadeIn 0.3s;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (max-width: 768px) {
  .nav-tabs {
    overflow-x: auto;
    white-space: nowrap;
  }

  .nav-tab {
    padding: 10px 16px;
    font-size: 14px;
  }
}
</style>