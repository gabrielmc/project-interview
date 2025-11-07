<template>
  <div class="modal-overlay" @click.self="close">
    <div class="modal-container">
      <!-- Header -->
      <div class="modal-header">
        <h2>Detalhes do Usuário</h2>
        <button @click="close" class="btn-close">&times;</button>
      </div>

      <!-- Content -->
      <div class="modal-content">
        <!-- Dados Pessoais -->
        <div class="details-section">
          <h3>Dados Pessoais</h3>
          
          <div class="detail-row">
            <span class="label">ID:</span>
            <span class="value">{{ user.id }}</span>
          </div>

          <div class="detail-row">
            <span class="label">Nome:</span>
            <span class="value">{{ user.name }}</span>
          </div>

          <div class="detail-row">
            <span class="label">CPF:</span>
            <span class="value">{{ formatCPF(user.cpf) }}</span>
          </div>

          <div class="detail-row">
            <span class="label">Email:</span>
            <span class="value">{{ user.email }}</span>
          </div>

          <div class="detail-row">
            <span class="label">Perfil:</span>
            <span class="value">{{ user.profile?.name || 'N/A' }}</span>
          </div>

          <div class="detail-row">
            <span class="label">Data de Cadastro:</span>
            <span class="value">{{ formatDate(user.created_at) }}</span>
          </div>

          <div class="detail-row">
            <span class="label">Última Atualização:</span>
            <span class="value">{{ formatDate(user.updated_at) }}</span>
          </div>
        </div>

        <!-- Endereços -->
        <div class="details-section">
          <h3>Endereços</h3>
          
          <div v-if="user.addresses && user.addresses.length > 0">
            <div 
              v-for="(address, index) in user.addresses" 
              :key="index" 
              class="address-card"
            >
              <div class="address-header">
                <strong>Endereço {{ index + 1 }}</strong>
              </div>
              
              <div class="detail-row">
                <span class="label">CEP:</span>
                <span class="value">{{ formatCEP(address.cep) }}</span>
              </div>

              <div class="detail-row">
                <span class="label">Logradouro:</span>
                <span class="value">{{ address.logradouro }}</span>
              </div>

              <div class="detail-row">
                <span class="label">Número:</span>
                <span class="value">{{ address.numero }}</span>
              </div>

              <div class="detail-row" v-if="address.complemento">
                <span class="label">Complemento:</span>
                <span class="value">{{ address.complemento }}</span>
              </div>

              <div class="detail-row">
                <span class="label">Bairro:</span>
                <span class="value">{{ address.bairro }}</span>
              </div>

              <div class="detail-row">
                <span class="label">Cidade:</span>
                <span class="value">{{ address.cidade }}</span>
              </div>

              <div class="detail-row">
                <span class="label">Estado:</span>
                <span class="value">{{ address.estado }}</span>
              </div>
            </div>
          </div>

          <div v-else class="no-addresses">
            Nenhum endereço cadastrado
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="modal-footer">
        <button @click="close" class="btn-fechar">
          Fechar
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'UserDetailsModal',
  props: {
    user: {
      type: Object,
      required: true
    }
  },
  emits: ['close'],
  setup(props, { emit }) {
    const close = () => {
      emit('close');
    };

    const formatDate = (dateString) => {
      if (!dateString) return 'N/A';
      const date = new Date(dateString);
      return date.toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    };

    const formatCPF = (cpf) => {
      if (!cpf) return 'N/A';
      return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
    };

    const formatCEP = (cep) => {
      if (!cep) return 'N/A';
      return cep.replace(/(\d{5})(\d{3})/, '$1-$2');
    };

    return {
      close,
      formatDate,
      formatCPF,
      formatCEP
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
  max-width: 700px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border-bottom: 2px solid #eee;
}

.modal-header h2 {
  margin: 0;
  color: #333;
  font-size: 20px;
}

.btn-close {
  background: none;
  border: none;
  font-size: 28px;
  cursor: pointer;
  color: #999;
  padding: 0;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-close:hover {
  color: #333;
}

.modal-content {
  padding: 20px;
}

.details-section {
  margin-bottom: 30px;
}

.details-section h3 {
  margin: 0 0 15px 0;
  color: #007bff;
  font-size: 18px;
  padding-bottom: 10px;
  border-bottom: 2px solid #007bff;
}

.detail-row {
  display: flex;
  padding: 10px 0;
  border-bottom: 1px solid #f0f0f0;
}

.detail-row .label {
  font-weight: 600;
  color: #555;
  min-width: 180px;
}

.detail-row .value {
  color: #333;
  flex: 1;
}

.address-card {
  background-color: #f9f9f9;
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 15px;
  border-left: 4px solid #007bff;
}

.address-header {
  margin-bottom: 10px;
  padding-bottom: 10px;
  border-bottom: 1px solid #ddd;
}

.address-header strong {
  color: #007bff;
  font-size: 16px;
}

.address-card .detail-row {
  padding: 8px 0;
}

.no-addresses {
  padding: 20px;
  text-align: center;
  color: #999;
  background-color: #f9f9f9;
  border-radius: 8px;
}

.modal-footer {
  padding: 15px 20px;
  border-top: 1px solid #eee;
  display: flex;
  justify-content: flex-end;
}

.btn-fechar {
  padding: 10px 30px;
  background-color: #6c757d;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.btn-fechar:hover {
  background-color: #545b62;
}
</style>