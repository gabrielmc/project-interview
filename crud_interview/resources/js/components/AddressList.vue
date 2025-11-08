<template>
  <div class="address-list-container">
    <div class="header-section">
      <h2>Gerenciamento de Endereços</h2>
      <button @click="openCreateModal" class="btn-novo">Novo Endereço</button>
    </div>

    <div v-if="loading" class="loading">Carregando...</div>

    <div v-else class="grid-container">
      <div v-for="address in addresses" :key="address.id" class="grid-card">
        <div class="card-header">
          <h3>{{ address.logradouro }}, {{ address.numero }}</h3>
        </div>
        
        <div class="card-body">
          <p><strong>CEP:</strong> {{ formatCEP(address.cep) }}</p>
          <p><strong>Bairro:</strong> {{ address.bairro }}</p>
          <p><strong>Cidade:</strong> {{ address.cidade }} - {{ address.estado }}</p>
          <p v-if="address.complemento"><strong>Complemento:</strong> {{ address.complemento }}</p>
        </div>
        
        <div class="card-actions">
          <button @click="editAddress(address)" class="btn-action btn-edit">
            ✏️ Editar
          </button>
          <button @click="deleteAddress(address)" class="btn-action btn-delete">
            🗑️ Remover
          </button>
        </div>
      </div>

      <div v-if="addresses.length === 0" class="no-data">
        Nenhum endereço cadastrado
      </div>
    </div>

    <!-- Paginação -->
    <div v-if="pagination.total > 0" class="pagination">
      <button 
        @click="goToPage(pagination.current_page - 1)" 
        :disabled="pagination.current_page === 1"
        class="btn-page"
      >
        Anterior
      </button>
      
      <span class="page-info">
        Página {{ pagination.current_page }} de {{ pagination.last_page }}
      </span>
      
      <button 
        @click="goToPage(pagination.current_page + 1)" 
        :disabled="pagination.current_page === pagination.last_page"
        class="btn-page"
      >
        Próxima
      </button>
    </div>

    <!-- Modal de Criar/Editar -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-container">
        <div class="modal-header">
          <h3>{{ modalMode === 'create' ? 'Novo Endereço' : 'Editar Endereço' }}</h3>
          <button @click="closeModal" class="btn-close">&times;</button>
        </div>
        
        <form @submit.prevent="saveAddress" class="modal-form">
          <div class="form-row">
            <div class="form-group" style="flex: 0 0 150px;">
              <label>CEP *</label>
              <input 
                v-model="form.cep" 
                type="text" 
                required 
                v-mask="'#####-###'"
                @blur="searchCEP"
                placeholder="00000-000" 
              />
            </div>
          </div>

          <div class="form-group">
            <label>Logradouro *</label>
            <input v-model="form.logradouro" type="text" required placeholder="Rua, Avenida..." />
          </div>

          <div class="form-row">
            <div class="form-group" style="flex: 0 0 100px;">
              <label>Número *</label>
              <input v-model="form.numero" type="text" required placeholder="123" />
            </div>

            <div class="form-group">
              <label>Complemento</label>
              <input v-model="form.complemento" type="text" placeholder="Apto, Sala..." />
            </div>
          </div>

          <div class="form-group">
            <label>Bairro *</label>
            <input v-model="form.bairro" type="text" required placeholder="Nome do bairro" />
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Cidade *</label>
              <input v-model="form.cidade" type="text" required placeholder="Nome da cidade" />
            </div>

            <div class="form-group" style="flex: 0 0 80px;">
              <label>Estado *</label>
              <input v-model="form.estado" type="text" required maxlength="2" placeholder="BA" />
            </div>
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
import { ref, reactive, onMounted } from 'vue';
import { addressService } from '../services/api';

export default {
  name: 'AddressList',
  setup() {
    const addresses = ref([]);
    const loading = ref(false);
    const showModal = ref(false);
    const modalMode = ref('create');
    const saving = ref(false);
    const selectedAddress = ref(null);

    const form = reactive({
      cep: '',
      logradouro: '',
      numero: '',
      complemento: '',
      bairro: '',
      cidade: '',
      estado: ''
    });

    const pagination = reactive({
      current_page: 1,
      last_page: 1,
      per_page: 9,
      total: 0
    });

    const loadAddresses = async (page = 1) => {
      loading.value = true;
      try {
        const response = await addressService.list('/enderecos', {
          params: { page, per_page: pagination.per_page }
        });
        if (response.data.success) {
          addresses.value = response.data.data.data;
          Object.assign(pagination, {
            current_page: response.data.data.current_page,
            last_page: response.data.data.last_page,
            total: response.data.data.total
          });
        }
      } catch (error) {
        console.error('Erro ao carregar endereços:', error);
        alert('Erro ao carregar endereços');
      } finally {
        loading.value = false;
      }
    };

    const searchCEP = async () => {
      const cep = form.cep.replace(/\D/g, '');
      if (cep.length !== 8) return;

      try {
        const response = await addressService.searchCEP(`/cep/${cep}`);
        if (response.data.success) {
          const data = response.data.data;
          form.logradouro = data.logradouro;
          form.bairro = data.bairro;
          form.cidade = data.cidade;
          form.estado = data.estado;
        }
      } catch (error) {
        console.error('Erro ao buscar CEP:', error);
      }
    };

    const openCreateModal = () => {
      Object.assign(form, {
        cep: '',
        logradouro: '',
        numero: '',
        complemento: '',
        bairro: '',
        cidade: '',
        estado: ''
      });
      modalMode.value = 'create';
      selectedAddress.value = null;
      showModal.value = true;
    };

    const editAddress = (address) => {
      Object.assign(form, {
        cep: address.cep,
        logradouro: address.logradouro,
        numero: address.numero,
        complemento: address.complemento,
        bairro: address.bairro,
        cidade: address.cidade,
        estado: address.estado
      });
      modalMode.value = 'edit';
      selectedAddress.value = address;
      showModal.value = true;
    };

    const saveAddress = async () => {
      saving.value = true;
      try {
        const data = {
          ...form,
          cep: form.cep.replace(/\D/g, '')
        };

        let response;
        if (modalMode.value === 'create') {
          response = await addressService.create('/enderecos', data);
        } else {
          response = await addressService.update(`/enderecos/${selectedAddress.value.id}`, data);
        }

        if (response.data.success) {
          alert(response.data.message);
          closeModal();
          loadAddresses(pagination.current_page);
        }
      } catch (error) {
        console.error('Erro ao salvar endereço:', error);
        alert('Erro ao salvar endereço');
      } finally {
        saving.value = false;
      }
    };

    const deleteAddress = async (address) => {
      if (!confirm(`Tem certeza que deseja excluir este endereço?`)) {
        return;
      }

      try {
        const response = await addressService.delete(`/enderecos/${address.id}`);
        if (response.data.success) {
          alert('Endereço excluído com sucesso!');
          loadAddresses(pagination.current_page);
        }
      } catch (error) {
        console.error('Erro ao excluir endereço:', error);
        alert('Erro ao excluir endereço');
      }
    };

    const goToPage = (page) => {
      if (page >= 1 && page <= pagination.last_page) {
        loadAddresses(page);
      }
    };

    const formatCEP = (cep) => {
      if (!cep) return 'N/A';
      return cep.replace(/(\d{5})(\d{3})/, '$1-$2');
    };

    const closeModal = () => {
      showModal.value = false;
      selectedAddress.value = null;
    };

    onMounted(() => {
      loadAddresses();
    });

    return {
      addresses,
      loading,
      showModal,
      modalMode,
      saving,
      form,
      pagination,
      loadAddresses,
      searchCEP,
      openCreateModal,
      editAddress,
      saveAddress,
      deleteAddress,
      goToPage,
      formatCEP,
      closeModal
    };
  }
};
</script>

<style scoped>
.address-list-container {
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

.loading {
  text-align: center;
  padding: 40px;
  color: #666;
}

.grid-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
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
  margin-bottom: 15px;
  padding-bottom: 15px;
  border-bottom: 2px solid #f0f0f0;
}

.card-header h3 {
  margin: 0;
  color: #333;
  font-size: 16px;
}

.card-body {
  margin-bottom: 15px;
}

.card-body p {
  margin: 5px 0;
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

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 15px;
  padding: 20px;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.btn-page {
  padding: 8px 16px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.btn-page:disabled {
  background-color: #ccc;
  cursor: not-allowed;
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
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
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

.modal-form {
  padding: 20px;
}

.form-group {
  margin-bottom: 15px;
  flex: 1;
}

.form-row {
  display: flex;
  gap: 15px;
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: 500;
}

.form-group input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
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
</style>