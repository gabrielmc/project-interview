
// repositoryPlugin ← plugin Vue p/ injeção global

import UserRepository from './repositories/UserRepository';
import ProfileRepository from './repositories/ProfileRepository';
import AddressRepository from './repositories/AddressRepository';

export default {
  install(app) {
    const repositories = {
      userInject: UserRepository,
      profileInject: ProfileRepository,
      addressInject: AddressRepository
    };

    app.provide('repositories', repositories);

    //Pode adicionar em globalProperties para uso via this.$repositories
    app.config.globalProperties.$repositories = repositories;
  }
};