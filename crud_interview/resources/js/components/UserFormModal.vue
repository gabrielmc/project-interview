<template>
  <div class="modal-overlay" @click.self="close">
    <div class="modal-container">
      <!-- Header -->
      <div class="modal-header">
        <button @click="close" class="btn-voltar">
          Voltar
        </button>
      </div>

      <!-- Form -->
      <form @submit.prevent="save" class="user-form">
        <!-- Dados Básicos -->
        <div class="form-section">
          <div class="form-group">
            <label>Nome *</label>
            <input 
              v-model="form.name" 
              type="text" 
              required
              placeholder="Digite o nome completo"
            />
            <span v-if="errors.name" class="error">{{ errors.name }}</span>
          </div>

          <div class="form-group">
            <label>CPF *</label>
            <input 
              v-model="form.cpf" 
              type="text" 
              required
              v-mask="'###.###.###-##'"
              placeholder="000.000.000-00"
            />
            <span v-if="errors.cpf" class="error">{{ errors.cpf }}</span>
          </div>

          <div class="form-group">
            <label>Email *</label>
            <input 
              v-model="form.email" 
              type="email" 
              required
              placeholder="exemplo@email.com"
            />
            <span v-if="errors.email" class="error">{{ errors.email }}</span>
          </div>

          <div class="form-group">
            <label>Perfil *</label>
            <select v-model="form.profile_id" required>
              <option value="">Selecione</option>
              <option 
                v-for="profile in profiles" 
                :key="profile.id" 
                :value="profile.id"
              >
                {{ profile.name }}
              </option>
            </select>
            <span v-if="errors.profile_id" class="error">{{ errors.profile_id }}</span>
          </div>
        </div>

        <!-- Endereços -->
        <div class="addresses-section">
          <div class="section-header">
            <h3>Endereço</h3>
          </div>

          <div v-for="(address, index) in form.addresses" :key="index" class="address-item">
            <div class="address-fields">
              <div class="form-row">
                <div class="form-group" style="flex: 0 0 200px;">
                  <label>CEP</label>
                  <input 
                    v-model="address.cep" 
                    type="text"
                    v-mask="'#####-###'"
                    placeholder="00000-000"
                    @blur="searchCEP(index)"
                  />
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label>Logradouro</label>
                  <input 
                    v-model="address.logradouro" 
                    type="text"
                    placeholder="Rua, Avenida..."
                  />
                </div>
              </div>

              <div class="form-row">  
                <div class="form-group flex-1">
                  <button 
                    type="button" 
                    @click="addAddress" 
                    class="btn-adicionar"
                  >
                    Adicionar
                  </button>
                </div>
              </div>
            </div>

            <!-- Tabela de endereços adicionados -->
            <div v-if="addedAddresses.length > 0" class="addresses-table">
              <table>
                <thead>
                  <tr>
                    <th>Logradouro</th>
                    <th>CEP</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(addr, idx) in addedAddresses" :key="idx">
                    <td>{{ addr.logradouro }}</td>
                    <td>{{ addr.cep }}</td>
                    <td>
                      <button 
                        type="button" 
                        @click="removeAddress(idx)" 
                        class="btn-excluir-small"
                      >
                        Excluir
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Botão Salvar -->
        <div class="form-actions">
          <button type="submit" class="btn-salvar" :disabled="saving">
            {{ saving ? 'Salvando...' : (mode === 'edit' ? 'Atualizar' : 'Salvar') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted, watch } from 'vue';
import { userService, profileService, addressService } from '../services/api';

export default {
  name: 'UserFormModal',
  props: {
    user: {
      type: Object,
      default: null
    },
    mode: {
      type: String,
      default: 'create' // 'create' ou 'edit'
    }
  },
  emits: ['close', 'saved'],
  setup(props, { emit }) {
    const profiles = ref([]);
    const saving = ref(false);
    const errors = ref({});

    const form = reactive({
      name: '',
      cpf: '',
      email: '',
      profile_id: '',
      addresses: [
        {
          logradouro: '',
          cep: '',
          numero: '',
          complemento: '',
          bairro: '',
          cidade: '',
          estado: ''
        }
      ]
    });

    const addedAddresses = ref([]);

    // Carregar perfis
    const loadProfiles = async () => {
      try {
        const response = await profileService.list();
        if (response.data.success) {
          profiles.value = response.data.data;
        }
      } catch (error) {
        console.error('Erro ao carregar perfis:', error);
      }
    };

    // Buscar CEP
    const searchCEP = async (index) => {
      const cep = form.addresses[index].cep.replace(/\D/g, '');
      if (cep.length !== 8) return;
      try {
        const response = await addressService.searchCEP(cep);
        
        if (response.data.success) {
          const data = response.data.data;
          form.addresses[index] = {
            ...form.addresses[index],
            logradouro: data.logradouro,
            bairro: data.bairro,
            cidade: data.cidade,
            estado: data.estado,
            cep: data.cep
          };
        }
      } catch (error) {
        console.error('Erro ao buscar CEP:', error);
      }
    };

    // Adicionar endereço à lista
    const addAddress = () => {
      const address = form.addresses[0];
      if (!address.logradouro || !address.cep) {
        alert('Preencha o logradouro e CEP');
        return;
      }
      addedAddresses.value.push({ ...address });
      // Limpar formulário de endereço
      form.addresses[0] = {
        logradouro: '',
        cep: '',
        numero: '',
        complemento: '',
        bairro: '',
        cidade: '',
        estado: ''
      };
    };

    // Remover endereço da lista
    const removeAddress = (index) => {
      addedAddresses.value.splice(index, 1);
    };

    // Salvar usuário
    const save = async () => {
      errors.value = {};
      saving.value = true;

      try {
        const data = {
          name: form.name,
          email: form.email,
          cpf: form.cpf.replace(/\D/g, ''),
          profile_id: form.profile_id,
          addresses: addedAddresses.value.map(addr => ({
            ...addr,
            cep: addr.cep.replace(/\D/g, ''),
            numero: addr.numero || 'S/N'
          }))
        };

        let response;
        if (props.mode === 'edit') {
          response = await userService.update(props.user.id, data);
        } else {
          console.log(data);
          response = await userService.create(data);
        }

        if (response.data.success) {
          alert(response.data.message);
          emit('saved');
        }
      } catch (error) {
        console.error('Erro ao salvar:', error);
        
        if (error.response?.data?.errors) {
          errors.value = error.response.data.errors;
        } else {
          alert('Erro ao salvar usuário');
        }
      } finally {
        saving.value = false;
      }
    };

    // Fechar modal
    const close = () => {
      emit('close');
    };

    // Inicializar formulário com dados do usuário (modo edição)
    const initForm = () => {
      if (props.user && props.mode === 'edit') {
        form.name = props.user.name;
        form.cpf = props.user.cpf;
        form.email = props.user.email;
        form.profile_id = props.user.profile_id;

        // Carregar endereços existentes
        if (props.user.addresses && props.user.addresses.length > 0) {
          addedAddresses.value = props.user.addresses.map(addr => ({
            ...addr,
            cep: addr.cep.replace(/(\d{5})(\d{3})/, '$1-$2')
          }));
        }
      }
    };

    // Montar componente
    onMounted(() => {
      loadProfiles();
      initForm();
    });

    return {
      profiles,
      form,
      addedAddresses,
      errors,
      saving,
      searchCEP,
      addAddress,
      removeAddress,
      save,
      close
    };
  }
};
</script>

<style scoped>
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
  width: 100%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

.modal-header {
  padding: 15px 20px;
  border-bottom: 1px solid #eee;
}

.btn-voltar {
  padding: 8px 20px;
  background-color: #6c757d;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.user-form {
  padding: 20px;
}

.form-section {
  margin-bottom: 30px;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: 500;
  color: #333;
}

.form-group input,
.form-group select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.form-group select {
  background-color: white;
  cursor: pointer;
}

.error {
  color: #dc3545;
  font-size: 12px;
  margin-top: 5px;
  display: block;
}

.addresses-section {
  margin-bottom: 20px;
  padding: 15px;
  background-color: #f9f9f9;
  border-radius: 8px;
}

.section-header {
  margin-bottom: 15px;
}

.section-header h3 {
  margin: 0;
  color: #333;
  font-size: 16px;
}

.address-item {
  margin-bottom: 15px;
}

.address-fields {
  background-color: white;
  padding: 15px;
  border-radius: 6px;
}

.form-row {
  display: flex;
  gap: 15px;
  margin-bottom: 10px;
}

.form-row .form-group {
  flex: 1;
  margin-bottom: 0;
}

.flex-1 {
  flex: 1;
}

.btn-adicionar {
  width: 100%;
  padding: 10px;
  background-color: #28a745;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin-top: 22px;
}

.addresses-table {
  margin-top: 15px;
  background-color: white;
  border-radius: 6px;
  overflow: hidden;
}

.addresses-table table {
  width: 100%;
  border-collapse: collapse;
}

.addresses-table th {
  background-color: #e0e0e0;
  padding: 10px;
  text-align: left;
  font-weight: 600;
}

.addresses-table td {
  padding: 10px;
  border-bottom: 1px solid #eee;
}

.btn-excluir-small {
  padding: 5px 12px;
  background-color: #dc3545;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 12px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #eee;
}

.btn-salvar {
  padding: 12px 40px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
}

.btn-salvar:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.btn-salvar:hover:not(:disabled) {
  background-color: #0056b3;
}
</style>