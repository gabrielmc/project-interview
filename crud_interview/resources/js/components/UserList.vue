<template>
  <div class="user-list-container">
    <!-- Botão Novo -->
    <div class="header-section">
      <button @click="openCreateModal" class="btn-novo">
        Novo
      </button>
    </div>

    <!-- Filtros de Pesquisa -->
    <div class="filters-section">
      <div class="filter-row">
        <div class="filter-group">
          <label>Nome</label>
          <input 
            v-model="filters.nome" 
            type="text" 
            placeholder="Digite o nome"
            @keyup.enter="search"
          />
        </div>

        <div class="filter-group">
          <label>CPF</label>
          <input 
            v-model="filters.cpf" 
            type="text" 
            placeholder="Digite o CPF"
            v-mask="'###########'"
            @keyup.enter="search"
          />
        </div>
      </div>

      <div class="filter-row">
        <div class="filter-group">
          <label>Início</label>
          <input 
            v-model="filters.data_inicio" 
            type="date"
          />
        </div>

        <div class="filter-group">
          <label>Fim</label>
          <input 
            v-model="filters.data_fim" 
            type="date"
          />
        </div>
      </div>

      <div class="filter-actions">
        <button @click="search" class="btn-filtrar">
          Filtrar
        </button>
        <button @click="clearFilters" class="btn-limpar">
          Limpar
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="loading">
      Carregando...
    </div>

    <!-- Tabela de Usuários -->
    <div v-else class="table-container">
      <table class="users-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Data cadastro</th>
            <th>Nome</th>
            <th>CPF</th>
            <th>E-mail</th>
            <th>Perfil</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id">
            <td>{{ user.id }}</td>
            <td>{{ formatDate(user.created_at) }}</td>
            <td>{{ user.name }}</td>
            <td>{{ formatCPF(user.cpf) }}</td>
            <td>{{ user.email }}</td>
            <td>{{ user.profile?.name || 'N/A' }}</td>
            <td class="action-buttons">
              <button @click="viewDetails(user)" class="btn-detalhar">
                Detalhar
              </button>
              <button @click="editUser(user)" class="btn-editar">
                Editar
              </button>
              <button @click="deleteUser(user)" class="btn-excluir">
                Excluir
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Mensagem quando não há dados -->
      <div v-if="users.length === 0" class="no-data">
        Nenhum usuário encontrado
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
          ({{ pagination.total }} registros)
        </span>
        
        <button 
          @click="goToPage(pagination.current_page + 1)" 
          :disabled="pagination.current_page === pagination.last_page"
          class="btn-page"
        >
          Próxima
        </button>
      </div>
    </div>

    <!-- Modal de Cadastro/Edição -->
    <UserFormModal 
      v-if="showModal"
      :user="selectedUser"
      :mode="modalMode"
      @close="closeModal"
      @saved="onUserSaved"
    />

    <!-- Modal de Detalhes -->
    <UserDetailsModal
      v-if="showDetailsModal"
      :user="selectedUser"
      @close="closeDetailsModal"
    />
  </div>
</template>

<script>

import UserFormModal from './UserFormModal.vue';
import UserDetailsModal from './UserDetailsModal.vue';
import { inject, ref, reactive, onMounted } from 'vue';

export default {
  name: 'UserList',
  components: {
    UserFormModal,
    UserDetailsModal
  },
  setup() {
    const users = ref([]);
    const loading = ref(false);
    const showModal = ref(false);
    const showDetailsModal = ref(false);
    const selectedUser = ref(null);
    const modalMode = ref('create'); // 'create' ou 'edit'
    const { userInject } = inject('repositories'); //Injeção de dependência com camada de repository

    const filters = reactive({
      nome: '',
      cpf: '',
      data_inicio: '',
      data_fim: ''
    });

    const pagination = reactive({
      current_page: 1,
      last_page: 1,
      per_page: 15,
      total: 0
    });

    // Carregar usuários
    const loadUsers = async (page = 1) => {
      loading.value = true;
      try {
        const response = await userInject.list(page, pagination.per_page);
        
        if (response.data.success) {
          users.value = response.data.data.data;
          Object.assign(pagination, {
            current_page: response.data.data.current_page,
            last_page: response.data.data.last_page,
            total: response.data.data.total
          });
        }
      } catch (error) {
        console.error('Erro ao carregar usuários:', error);
        alert('Erro ao carregar usuários: ' + (error.response?.data?.message || error.message));
      } finally {
        loading.value = false;
      }
    };

    // Pesquisar com filtros
    const search = async () => {
      loading.value = true;
      try {
        const params = {
          page: 1,
          per_page: pagination.per_page
        };

        if (filters.nome) params.nome = filters.nome;
        if (filters.cpf) params.cpf = filters.cpf.replace(/\D/g, '');
        if (filters.data_inicio) params.data_inicio = filters.data_inicio;
        if (filters.data_fim) params.data_fim = filters.data_fim;

        const response = await userInject.search(params);
        console.log('Response pesquisa:', response.data);
        
        if (response.data.success) {
          users.value = response.data.data.data;
          Object.assign(pagination, {
            current_page: response.data.data.current_page,
            last_page: response.data.data.last_page,
            total: response.data.data.total
          });
        }
      } catch (error) {
        console.error('Erro ao pesquisar:', error);
        alert('Erro ao pesquisar usuários: ' + (error.response?.data?.message || error.message));
      } finally {
        loading.value = false;
      }
    };

    // Limpar filtros
    const clearFilters = () => {
      filters.nome = '';
      filters.cpf = '';
      filters.data_inicio = '';
      filters.data_fim = '';
      loadUsers();
    };

    // Abrir modal de criação
    const openCreateModal = () => {
      selectedUser.value = null;
      modalMode.value = 'create';
      showModal.value = true;
    };

    // Editar usuário
    const editUser = (user) => {
      selectedUser.value = { ...user };
      modalMode.value = 'edit';
      showModal.value = true;
    };

    // Ver detalhes
    const viewDetails = (user) => {
      selectedUser.value = user;
      showDetailsModal.value = true;
    };

    // Deletar usuário
    const deleteUser = async (user) => {
      if (!confirm(`Tem certeza que deseja excluir o usuário "${user.name}"?`)) {
        return;
      }

      try {
        const response = await userInject.delete(user.id);
        
        if (response.data.success) {
          alert('Usuário excluído com sucesso!');
          loadUsers(pagination.current_page);
        }
      } catch (error) {
        console.error('Erro ao excluir usuário:', error);
        alert('Erro ao excluir usuário: ' + (error.response?.data?.message || error.message));
      }
    };

    // Fechar modal
    const closeModal = () => {
      showModal.value = false;
      selectedUser.value = null;
    };

    // Fechar modal de detalhes
    const closeDetailsModal = () => {
      showDetailsModal.value = false;
      selectedUser.value = null;
    };

    // Quando usuário for salvo
    const onUserSaved = () => {
      closeModal();
      loadUsers(pagination.current_page);
    };

    // Ir para página
    const goToPage = (page) => {
      if (page >= 1 && page <= pagination.last_page) {
        loadUsers(page);
      }
    };

    // Formatar data
    const formatDate = (dateString) => {
      if (!dateString) return 'N/A';
      const date = new Date(dateString);
      return date.toLocaleDateString('pt-BR');
    };

    // Formatar CPF
    const formatCPF = (cpf) => {
      if (!cpf) return 'N/A';
      return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
    };

    // Carregar ao montar
    onMounted(() => {
      loadUsers();
    });

    return {
      users,
      loading,
      filters,
      pagination,
      showModal,
      showDetailsModal,
      selectedUser,
      modalMode,
      loadUsers,
      search,
      clearFilters,
      openCreateModal,
      editUser,
      viewDetails,
      deleteUser,
      closeModal,
      closeDetailsModal,
      onUserSaved,
      goToPage,
      formatDate,
      formatCPF
    };
  }
};
</script>

<style scoped>
.user-list-container {
  padding: 20px;
  max-width: 1400px;
  margin: 0 auto;
}

.header-section {
  margin-bottom: 20px;
}

.btn-novo {
  padding: 10px 30px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.btn-novo:hover {
  background-color: #0056b3;
}

.filters-section {
  background-color: #f5f5f5;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.filter-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
  margin-bottom: 15px;
}

.filter-group {
  display: flex;
  flex-direction: column;
}

.filter-group label {
  margin-bottom: 5px;
  font-weight: 500;
  color: #333;
}

.filter-group input {
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.filter-actions {
  display: flex;
  gap: 10px;
}

.btn-filtrar {
  padding: 8px 25px;
  background-color: #28a745;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.btn-filtrar:hover {
  background-color: #218838;
}

.btn-limpar {
  padding: 8px 25px;
  background-color: #6c757d;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.btn-limpar:hover {
  background-color: #545b62;
}

.loading {
  text-align: center;
  padding: 40px;
  color: #666;
}

.table-container {
  background-color: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.users-table {
  width: 100%;
  border-collapse: collapse;
}

.users-table thead {
  background-color: #e0e0e0;
}

.users-table th {
  padding: 12px;
  text-align: left;
  font-weight: 600;
  color: #333;
  border-bottom: 2px solid #ccc;
}

.users-table td {
  padding: 12px;
  border-bottom: 1px solid #eee;
}

.users-table tbody tr:hover {
  background-color: #f9f9f9;
}

.action-buttons {
  display: flex;
  gap: 5px;
}

.action-buttons button {
  padding: 6px 12px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 12px;
}

.btn-detalhar {
  background-color: #17a2b8;
  color: white;
}

.btn-detalhar:hover {
  background-color: #138496;
}

.btn-edit {
  background-color: #074c91; 
  color: #fff; 
}

.btn-editar:hover {
  background-color: #074c91;
  color: #fff;
}

.btn-excluir {
  background-color: #dc3545;
  color: white;
}

.btn-excluir:hover {
  background-color: #c82333;
}

.no-data {
  padding: 40px;
  text-align: center;
  color: #999;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 15px;
  padding: 20px;
  background-color: #f9f9f9;
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

.btn-page:hover:not(:disabled) {
  background-color: #0056b3;
}

.page-info {
  color: #666;
  font-size: 14px;
}

@media (max-width: 768px) {
  .filter-row {
    grid-template-columns: 1fr;
  }
  
  .action-buttons {
    flex-direction: column;
  }
}
</style>