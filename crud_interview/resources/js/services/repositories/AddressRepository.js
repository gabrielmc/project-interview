import api from '../api';

export default {
  list(page = 1, perPage = 15) {
    return api.get('/enderecos', { params: { page, per_page: perPage } });
  },

  getById(id) {
    return api.get(`/enderecos/${id}`);
  },

  create(data) {
    return api.post('/enderecos', data);
  },

  update(id, data) {
    return api.put(`/enderecos/${id}`, data);
  },

  delete(id) {
    return api.delete(`/enderecos/${id}`);
  },

  searchCEP(cep) {
    return api.get(`/cep/${cep}`);
  },

  attachUser(addressId, userId) {
    return api.post(`/enderecos/${addressId}/vincular-usuario`, { user_id: userId });
  },

  detachUser(addressId, userId) {
    return api.delete(`/enderecos/${addressId}/desvincular-usuario/${userId}`);
  }
};