<template>
  <div class="profile-list-container">
    <div class="header-section">
      <h2>Gerenciamento de Perfis</h2>
      <button @click="openCreateModal" class="btn-novo">Novo Perfil</button>
    </div>

    <div v-if="loading" class="loading">Carregando...</div>

    <div v-else class="grid-container">
      <div v-for="profile in profiles" :key="profile.id" class="grid-card">
        <div class="card-header">
          <h3>{{ profile.name }}</h3>
          <span class="badge">{{ profile.users_count || 0 }} usuários</span>
        </div>
        
        <div class="card-body">
          <p>{{ profile.description || 'Sem descrição' }}</p>
        </div>
        
        <div class="card-actions">
          <button @click="editProfile(profile)" class="btn-action btn-edit">
            ✏️ Editar
          </button>
          <button @click="deleteProfile(profile)" class="btn-action btn-delete">
            🗑️ Remover
          </button>
        </div>
      </div>

      <div v-if="profiles.length === 0" class="no-data">
        Nenhum perfil cadastrado
      </div>
    </div>

    <!-- Modal de Criar/Editar -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-container">
        <div class="modal-header">
          <h3>{{ modalMode === 'create' ? 'Novo Perfil' : 'Editar Perfil' }}</h3>
          <button @click="closeModal" class="btn-close">&times;</button>
        </div>
        
        <form @submit.prevent="saveProfile" class="modal-form">
          <div class="form-group">
            <label>Nome *</label>
            <input v-model="form.name" type="text" required placeholder="Nome do perfil" />
            <span v-if="errors.name" class="error">{{ Array.isArray(errors.name) ? errors.name[0] : errors.name }}</span>
          </div>

          <div class="form-group">
            <label>Descrição</label>
            <textarea v-model="form.description" rows="4" placeholder="Descrição do perfil"></textarea>
          </div>

          <div class="form-actions">
            <button type="button" @click="closeModal" class="btn-cancel">Cancelar</button>
            <button type="submit" class="btn-save" :disabled="saving">
              {{ saving ? 'Salvando...' : 'Salvar' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
// import { ref, reactive, onMounted } from 'vue';
// import { profileService } from '../services/api';
import { inject, ref, reactive, onMounted } from 'vue';

export default {
  name: 'ProfileList',
  setup() {
    const profiles = ref([]);
    const loading = ref(false);
    const showModal = ref(false);
    const modalMode = ref('create');
    const saving = ref(false);
    const selectedProfile = ref(null);
    const errors = ref({});
    const { profile } = inject('repositories'); //Injeção de dependência com camada de repository

    const form = reactive({
      name: '',
      description: ''
    });

    const loadProfiles = async () => {
      loading.value = true;
      try {
        const response = await profile.list();
        console.log('Response perfis:', response.data);
        if (response.data.success) {
          profiles.value = response.data.data;
        }
      } catch (error) {
        console.error('Erro ao carregar perfis:', error);
        alert('Erro ao carregar perfis: ' + (error.response?.data?.message || error.message));
      } finally {
        loading.value = false;
      }
    };

    const openCreateModal = () => {
      form.name = '';
      form.description = '';
      errors.value = {};
      modalMode.value = 'create';
      selectedProfile.value = null;
      showModal.value = true;
    };

    const editProfile = (profile) => {
      form.name = profile.name;
      form.description = profile.description || '';
      errors.value = {};
      modalMode.value = 'edit';
      selectedProfile.value = profile;
      showModal.value = true;
    };

    const saveProfile = async () => {
      saving.value = true;
      errors.value = {};
      
      try {
        let response;
        const data = {
          name: form.name,
          description: form.description
        };

        if (modalMode.value === 'create') {
          response = await profile.create(data);
        } else {
          response = await profile.update(selectedProfile.value.id, data);
        }

        if (response.data.success) {
          alert(response.data.message);
          closeModal();
          loadProfiles();
        }
      } catch (error) {
        console.error('Erro ao salvar perfil:', error);
        
        if (error.response?.data?.errors) {
          errors.value = error.response.data.errors;
        } else {
          alert(error.response?.data?.message || 'Erro ao salvar perfil');
        }
      } finally {
        saving.value = false;
      }
    };

    const deleteProfile = async (profile) => {
      if (!confirm(`Tem certeza que deseja excluir o perfil "${profile.name}"?`)) {
        return;
      }

      try {
        const response = await profile.delete(profile.id);
        if (response.data.success) {
          alert('Perfil excluído com sucesso!');
          loadProfiles();
        }
      } catch (error) {
        console.error('Erro ao excluir perfil:', error);
        alert(error.response?.data?.message || 'Erro ao excluir perfil');
      }
    };

    const closeModal = () => {
      showModal.value = false;
      selectedProfile.value = null;
      errors.value = {};
    };

    onMounted(() => {
      loadProfiles();
    });

    return {
      profiles,
      loading,
      showModal,
      modalMode,
      saving,
      form,
      errors,
      loadProfiles,
      openCreateModal,
      editProfile,
      saveProfile,
      deleteProfile,
      closeModal
    };
  }
};
</script>

<style scoped>
.profile-list-container {
  padding: 20px;
}

.header-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.header-section h2 {
  margin: 0;
  color: #333;
}

.btn-novo {
  padding: 10px 25px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.btn-novo:hover {
  background-color: #0056b3;
}

.loading {
  text-align: center;
  padding: 40px;
  color: #666;
}

.grid-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
}

.grid-card {
  background: white;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  transition: transform 0.2s;
}

.grid-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
  padding-bottom: 15px;
  border-bottom: 2px solid #f0f0f0;
}

.card-header h3 {
  margin: 0;
  color: #333;
  font-size: 18px;
}

.badge {
  background-color: #007bff;
  color: white;
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 12px;
}

.card-body {
  margin-bottom: 15px;
}

.card-body p {
  margin: 0;
  color: #666;
  font-size: 14px;
}

.card-actions {
  display: flex;
  gap: 10px;
}

.btn-action {
  flex: 1;
  padding: 8px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.btn-action:hover {
  opacity: 0.8;
}

.btn-edit {
  background-color: #003366; 
  color: #fff; 
}

.btn-delete {
  background-color: #dc3545;
  color: white;
}

.no-data {
  grid-column: 1 / -1;
  text-align: center;
  padding: 60px;
  color: #999;
  background-color: #f9f9f9;
  border-radius: 8px;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  padding: 20px;
}

.modal-container {
  background-color: white;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid #eee;
}

.modal-header h3 {
  margin: 0;
}

.btn-close {
  background: none;
  border: none;
  font-size: 28px;
  cursor: pointer;
  color: #999;
}

.btn-close:hover {
  color: #333;
}

.modal-form {
  padding: 20px;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: 500;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.error {
  color: #dc3545;
  font-size: 12px;
  margin-top: 5px;
  display: block;
}

.form-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
  margin-top: 20px;
}

.btn-cancel {
  padding: 10px 20px;
  background-color: #6c757d;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.btn-cancel:hover {
  background-color: #545b62;
}

.btn-save {
  padding: 10px 20px;
  background-color: #28a745;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.btn-save:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.btn-save:hover:not(:disabled) {
  background-color: #218838;
}
</style>