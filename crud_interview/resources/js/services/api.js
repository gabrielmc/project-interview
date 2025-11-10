import axios from 'axios';

// Configuração base do axios
const api = axios.create({
  baseURL: 'http://localhost:8000/api/v1',
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Interceptor para requisições
api.interceptors.request.use(
  (config) => {
    // Aqui você pode adicionar tokens de autenticação, se necessário
    // const token = localStorage.getItem('token');
    // if (token) {
    //   config.headers.Authorization = `Bearer ${token}`;
    // }
    
    console.log('Requisição enviada:', config.method.toUpperCase(), config.url);
    return config;
  },
  (error) => {
    console.error('Erro na requisição:', error);
    return Promise.reject(error);
  }
);

// Interceptor para respostas
api.interceptors.response.use(
  (response) => {
    console.log('Resposta recebida:', response.status, response.config.url);
    return response;
  },
  (error) => {
    console.error('Erro na resposta:', error.response?.status, error.response?.data);
    
    // Tratamento de erros específicos
    if (error.response) {
      switch (error.response.status) {
        case 401:
          console.error('Não autorizado');
          // Redirecionar para login, se necessário
          break;
        case 403:
          console.error('Acesso negado');
          break;
        case 404:
          console.error('Recurso não encontrado');
          break;
        case 422:
          console.error('Erro de validação:', error.response.data.errors);
          break;
        case 500:
          console.error('Erro no servidor');
          break;
        default:
          console.error('Erro desconhecido:', error.response.status);
      }
    } else if (error.request) {
      console.error('Sem resposta do servidor');
    } else {
      console.error('Erro ao configurar requisição:', error.message);
    }
    
    return Promise.reject(error);
  }
);

// Métodos auxiliares específicos - CRUD
export const userService = {
  list: (page = 1, perPage = 15) => {
    //console.log('Chamando:', api.defaults.baseURL);
    return api.get('/usuarios', { params: { page, per_page: perPage } });
  },

  search: (filters) => {
    //console.log('Chamando:', api.defaults.baseURL);
    return api.get('/usuarios/pesquisar', { params: filters });
  },

  getById: (id) => {
    //console.log('Chamando:', api.defaults.baseURL);
    return api.get(`/usuarios/${id}`);
  },

  // Criar novo usuário
  create: (data) => {
    return api.post('/usuarios', data);
  },

  // Atualizar usuário
  update: (id, data) => {
    return api.put(`/usuarios/${id}`, data);
  },

  // Deletar usuário
  delete: (id) => {
    return api.delete(`/usuarios/${id}`);
  },

  // Listar endereços de um usuário
  getAddresses: (id) => {
    return api.get(`/usuarios/${id}/enderecos`);
  }
};

export const profileService = {
  list: () => {
    //console.log('Chamando:', api.defaults.baseURL);
    return api.get('/perfis');
  },

  getById: (id) => {
    //console.log('Chamando:', api.defaults.baseURL);
    return api.get(`/perfis/${id}`);
  },

  // Criar novo perfil
  create: (data) => {
    return api.post('/perfis', data);
  },

  // Atualizar perfil
  update: (id, data) => {
    return api.put(`/perfis/${id}`, data);
  },

  // Deletar perfil
  delete: (id) => {
    return api.delete(`/perfis/${id}`);
  }
};

export const addressService = {
  list: (page = 1, perPage = 15) => {
    //console.log('Chamando:', api.defaults.baseURL);
    return api.get('/enderecos', { params: { page, per_page: perPage } });
  },

  getById: (id) => {
    //console.log('Chamando:', api.defaults.baseURL);
    return api.get(`/enderecos/${id}`);
  },

  create: (data) => {
    return api.post('/enderecos', data);
  },

  // Atualizar endereço
  update: (id, data) => {
    return api.put(`/enderecos/${id}`, data);
  },

  // Deletar endereço
  delete: (id) => {
    return api.delete(`/enderecos/${id}`);
  },

  // Buscar CEP
  searchCEP: (cep) => {
    return api.get(`/cep/${cep}`);
  },

  // Vincular usuário ao endereço
  attachUser: (addressId, userId) => {
    return api.post(`/enderecos/${addressId}/vincular-usuario`, { user_id: userId });
  },

  // Desvincular usuário do endereço
  detachUser: (addressId, userId) => {
    return api.delete(`/enderecos/${addressId}/desvincular-usuario/${userId}`);
  }
};

export default api;