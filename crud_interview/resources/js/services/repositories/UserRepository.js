import api from '../api';

export default {
  list(page = 1, perPage = 15) {
    return api.get('/usuarios', { params: { page, per_page: perPage } });
  },

  search(filters) {
    return api.get('/usuarios/pesquisar', { params: filters });
  },

  getById(id) {
    return api.get(`/usuarios/${id}`);
  },

  create(data) {
    return api.post('/usuarios', data);
  },

  update(id, data) {
    return api.put(`/usuarios/${id}`, data);
  },

  delete(id) {
    return api.delete(`/usuarios/${id}`);
  },

  getAddresses(id) {
    return api.get(`/usuarios/${id}/enderecos`);
  }
};