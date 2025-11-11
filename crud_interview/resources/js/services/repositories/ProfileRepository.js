import api from '../api';

export default {
  list() {
    return api.get('/perfis');
  },

  getById(id) {
    return api.get(`/perfis/${id}`);
  },

  create(data) {
    return api.post('/perfis', data);
  },

  update(id, data) {
    return api.put(`/perfis/${id}`, data);
  },

  delete(id) {
    return api.delete(`/perfis/${id}`);
  }
};